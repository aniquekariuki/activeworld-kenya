<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'activeworld_db');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ============================================
// UPDATE PAYMENT WITH AMOUNTS
// ============================================
if (isset($_POST['update_payment'])) {
    $id = (int)$_POST['id'];
    $payment_status = $_POST['payment_status'];
    $amount_paid = !empty($_POST['amount_paid']) ? (float)$_POST['amount_paid'] : 0;
    $total_amount = !empty($_POST['total_amount']) ? (float)$_POST['total_amount'] : 0;
    $deposit_percentage = !empty($_POST['deposit_percentage']) ? (int)$_POST['deposit_percentage'] : 30;
    $payment_method = !empty($_POST['payment_method']) ? "'" . $conn->real_escape_string($_POST['payment_method']) . "'" : 'NULL';
    $transaction_id = !empty($_POST['transaction_id']) ? "'" . $conn->real_escape_string($_POST['transaction_id']) . "'" : 'NULL';
    
    // Calculate deposit amount
    $deposit_amount = ($deposit_percentage / 100) * $total_amount;
    
    $sql = "UPDATE event_requests SET 
        payment_status = '$payment_status',
        amount_paid = $amount_paid,
        total_amount = $total_amount,
        deposit_percentage = $deposit_percentage,
        deposit_amount = $deposit_amount,
        payment_method = $payment_method,
        transaction_id = $transaction_id,
        payment_date = CURDATE()
        WHERE id = $id";
    
    if ($conn->query($sql)) {
        $message = "✅ Payment updated! Amount: KES " . number_format($amount_paid, 2);
        $message_type = "success";
    } else {
        $message = "❌ Error: " . $conn->error;
        $message_type = "error";
    }
}

// ============================================
// UPDATE EVENT STATUS
// ============================================
if (isset($_POST['update_status'])) {
    $id = (int)$_POST['id'];
    $status = $_POST['status'];
    $conn->query("UPDATE event_requests SET status = '$status' WHERE id = $id");
}

// ============================================
// DELETE EVENT
// ============================================
if (isset($_POST['delete'])) {
    $id = (int)$_POST['id'];
    $conn->query("DELETE FROM event_requests WHERE id = $id");
}

// ============================================
// EDIT EVENT
// ============================================
if (isset($_POST['edit'])) {
    $id = (int)$_POST['id'];
    $name = $conn->real_escape_string($_POST['name']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $email = $conn->real_escape_string($_POST['email']);
    $event_type = $conn->real_escape_string($_POST['event_type']);
    $event_date = !empty($_POST['event_date']) ? "'" . $_POST['event_date'] . "'" : 'NULL';
    $venue = $conn->real_escape_string($_POST['venue']);
    $message_text = $conn->real_escape_string($_POST['message']);
    
    $conn->query("UPDATE event_requests SET 
        name='$name', phone='$phone', email='$email', 
        event_type='$event_type', event_date=$event_date, 
        venue='$venue', message='$message_text' 
        WHERE id=$id");
}

// ============================================
// GET DATA WITH SEARCH & FILTER
// ============================================
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$payment_filter = isset($_GET['payment']) ? $_GET['payment'] : '';

$where = [];
if ($search) {
    $where[] = "(name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%')";
}
if ($status_filter && $status_filter != 'all') {
    $where[] = "status = '$status_filter'";
}
if ($payment_filter && $payment_filter != 'all') {
    $where[] = "payment_status = '$payment_filter'";
}
$where_sql = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$result = $conn->query("SELECT * FROM event_requests $where_sql ORDER BY created_at DESC");

// ============================================
// STATISTICS
// ============================================
$stats = $conn->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN payment_status = 'deposit_paid' THEN 1 ELSE 0 END) as deposit_paid,
        SUM(CASE WHEN payment_status = 'partial_paid' THEN 1 ELSE 0 END) as partial_paid,
        SUM(CASE WHEN payment_status = 'fully_paid' THEN 1 ELSE 0 END) as fully_paid
    FROM event_requests
")->fetch_assoc();

// ============================================
// PAYMENT TOTALS
// ============================================
$payment_totals = $conn->query("
    SELECT 
        SUM(CASE WHEN payment_status = 'deposit_paid' THEN deposit_amount ELSE 0 END) as total_deposit,
        SUM(CASE WHEN payment_status = 'partial_paid' THEN amount_paid ELSE 0 END) as total_partial,
        SUM(CASE WHEN payment_status = 'fully_paid' THEN total_amount ELSE 0 END) as total_fully_paid
    FROM event_requests
")->fetch_assoc();

// ============================================
// EXPORT
// ============================================
if (isset($_GET['export'])) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=event_requests.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID', 'Date', 'Name', 'Email', 'Phone', 'Event Type', 'Venue', 'Status', 'Payment Status', 'Amount Paid', 'Total Amount', 'Deposit %']);
    $export_data = $conn->query("SELECT * FROM event_requests ORDER BY created_at DESC");
    while ($row = $export_data->fetch_assoc()) {
        fputcsv($output, [
            $row['id'], $row['created_at'], $row['name'], $row['email'], $row['phone'],
            $row['event_type'], $row['venue'], $row['status'], $row['payment_status'],
            $row['amount_paid'], $row['total_amount'], $row['deposit_percentage']
        ]);
    }
    fclose($output);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Active World</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f0f2f5; }
        .header { background: #2c3e50; color: white; padding: 15px 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; }
        .header h1 { font-size: 20px; }
        .logout-btn { background: #e74c3c; color: white; padding: 8px 20px; text-decoration: none; border-radius: 5px; }
        .message { padding: 15px 20px; margin: 0 30px 20px 30px; border-radius: 10px; font-weight: bold; }
        .message.success { background: #27ae60; color: white; }
        .message.error { background: #e74c3c; color: white; }
        
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; padding: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .stat-number { font-size: 32px; font-weight: bold; color: #F26419; }
        .stat-number.green { color: #27ae60; }
        .stat-number.orange { color: #f39c12; }
        .stat-number.blue { color: #3498db; }
        .stat-label { color: #666; font-size: 14px; margin-top: 5px; }
        
        .payment-totals { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; padding: 0 30px 20px 30px; }
        .payment-card { background: white; padding: 15px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .payment-card .amount { font-size: 24px; font-weight: bold; color: #F26419; }
        .payment-card .label { color: #666; font-size: 12px; margin-top: 5px; }
        
        .filters { background: white; margin: 0 30px 20px 30px; padding: 20px; border-radius: 10px; display: flex; gap: 15px; flex-wrap: wrap; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .filters input, .filters select { padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; }
        .filters button { background: #F26419; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        .export-btn { background: #27ae60; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none; display: inline-block; }
        
        .table-container { background: white; margin: 0 30px 30px 30px; border-radius: 10px; overflow-x: auto; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #34495e; color: white; position: sticky; top: 0; }
        
        .status-pending { background: #f39c12; color: white; padding: 3px 10px; border-radius: 20px; font-size: 11px; display: inline-block; }
        .status-approved { background: #27ae60; color: white; padding: 3px 10px; border-radius: 20px; font-size: 11px; display: inline-block; }
        .status-completed { background: #3498db; color: white; padding: 3px 10px; border-radius: 20px; font-size: 11px; display: inline-block; }
        
        .payment-no_payment { background: #95a5a6; color: white; padding: 3px 10px; border-radius: 20px; font-size: 11px; display: inline-block; }
        .payment-deposit_paid { background: #8e44ad; color: white; padding: 3px 10px; border-radius: 20px; font-size: 11px; display: inline-block; }
        .payment-partial_paid { background: #e67e22; color: white; padding: 3px 10px; border-radius: 20px; font-size: 11px; display: inline-block; }
        .payment-fully_paid { background: #27ae60; color: white; padding: 3px 10px; border-radius: 20px; font-size: 11px; display: inline-block; }
        
        .btn-edit, .btn-delete, .btn-payment { padding: 4px 8px; border-radius: 4px; border: none; cursor: pointer; margin: 2px; font-size: 11px; }
        .btn-edit { background: #3498db; color: white; }
        .btn-delete { background: #e74c3c; color: white; }
        .btn-payment { background: #8e44ad; color: white; }
        
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); }
        .modal-content { background: white; margin: 3% auto; padding: 30px; width: 550px; border-radius: 10px; max-height: 90vh; overflow-y: auto; }
        .close { float: right; font-size: 28px; cursor: pointer; }
        .modal-content label { display: block; color: #333; font-weight: bold; margin-bottom: 5px; }
        .modal-content input, .modal-content select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 15px; }
        .modal-content .row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .modal-content .btn-submit { width: 100%; padding: 12px; background: #F26419; color: white; border: none; border-radius: 5px; font-size: 16px; font-weight: bold; cursor: pointer; }
        .modal-content .btn-submit:hover { background: #e0550a; }
        
        @media (max-width: 768px) {
            .stats { grid-template-columns: repeat(2, 1fr); }
            .payment-totals { grid-template-columns: 1fr; }
            .modal-content { width: 95%; }
            .modal-content .row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🏆 Active World Kenya - Admin Dashboard</h1>
        <a href="logout.php" class="logout-btn">🚪 Logout</a>
    </div>
    
    <?php if (isset($message)): ?>
        <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
    <?php endif; ?>
    
    <div class="stats">
        <div class="stat-card"><div class="stat-number"><?php echo $stats['total']; ?></div><div class="stat-label">Total Requests</div></div>
        <div class="stat-card"><div class="stat-number orange"><?php echo $stats['pending']; ?></div><div class="stat-label">Pending</div></div>
        <div class="stat-card"><div class="stat-number green"><?php echo $stats['approved']; ?></div><div class="stat-label">Approved</div></div>
        <div class="stat-card"><div class="stat-number blue"><?php echo $stats['completed']; ?></div><div class="stat-label">Completed</div></div>
    </div>
    
    <div class="payment-totals">
        <div class="payment-card"><div class="amount">KES <?php echo number_format($payment_totals['total_deposit'] ?? 0, 2); ?></div><div class="label">Total Deposits</div></div>
        <div class="payment-card"><div class="amount">KES <?php echo number_format($payment_totals['total_partial'] ?? 0, 2); ?></div><div class="label">Total Partial Payments</div></div>
        <div class="payment-card"><div class="amount">KES <?php echo number_format($payment_totals['total_fully_paid'] ?? 0, 2); ?></div><div class="label">Total Fully Paid</div></div>
    </div>
    
    <div class="filters">
        <form method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" name="search" placeholder="Search..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="status">
                <option value="all" <?php echo $status_filter == 'all' ? 'selected' : ''; ?>>All Status</option>
                <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="approved" <?php echo $status_filter == 'approved' ? 'selected' : ''; ?>>Approved</option>
                <option value="completed" <?php echo $status_filter == 'completed' ? 'selected' : ''; ?>>Completed</option>
            </select>
            <select name="payment">
                <option value="all" <?php echo $payment_filter == 'all' ? 'selected' : ''; ?>>All Payments</option>
                <option value="no_payment" <?php echo $payment_filter == 'no_payment' ? 'selected' : ''; ?>>No Payment</option>
                <option value="deposit_paid" <?php echo $payment_filter == 'deposit_paid' ? 'selected' : ''; ?>>Deposit Paid</option>
                <option value="partial_paid" <?php echo $payment_filter == 'partial_paid' ? 'selected' : ''; ?>>Partial Paid</option>
                <option value="fully_paid" <?php echo $payment_filter == 'fully_paid' ? 'selected' : ''; ?>>Fully Paid</option>
            </select>
            <button type="submit">🔍 Filter</button>
        </form>
        <a href="?export=1" class="export-btn">📊 Export</a>
    </div>
    
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Event Type</th>
                    <th>Venue</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $row['id']; ?></td>
                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                    <td><?php echo htmlspecialchars($row['event_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['venue'] ?: 'TBD'); ?></td>
                    <td>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <select name="status" onchange="this.form.submit()">
                                <option value="pending" <?php echo $row['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                <option value="approved" <?php echo $row['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                                <option value="completed" <?php echo $row['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                            </select>
                            <input type="hidden" name="update_status" value="1">
                        </form>
                    </td>
                    <td>
                        <?php 
                            $payment_status = $row['payment_status'] ?? 'no_payment';
                            $labels = [
                                'no_payment' => '❌ No Payment',
                                'deposit_paid' => '💳 Deposit',
                                'partial_paid' => '📊 Partial',
                                'fully_paid' => '✅ Fully Paid'
                            ];
                        ?>
                        <span class="payment-<?php echo $payment_status; ?>">
                            <?php echo $labels[$payment_status] ?? '❌ No Payment'; ?>
                        </span>
                        <?php if ($row['amount_paid'] && $row['amount_paid'] > 0): ?>
                            <br><small>Paid: KES <?php echo number_format($row['amount_paid'], 2); ?></small>
                        <?php endif; ?>
                        <?php if ($row['total_amount'] && $row['total_amount'] > 0): ?>
                            <br><small>Total: KES <?php echo number_format($row['total_amount'], 2); ?></small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="btn-edit" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">✏️ Edit</button>
                        <button class="btn-payment" onclick="openPaymentModal(<?php echo htmlspecialchars(json_encode($row)); ?>)">💳 Pay</button>
                        <form method="POST" style="display: inline;" onsubmit="return confirm('Delete?')">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="delete" class="btn-delete">🗑️</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($result->num_rows == 0): ?>
                <tr><td colspan="10" style="text-align:center; padding:40px;">No event requests found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editModal')">&times;</span>
            <h3>✏️ Edit Event Request</h3>
            <form method="POST">
                <input type="hidden" name="id" id="edit_id">
                <label>Full Name</label>
                <input type="text" name="name" id="edit_name" placeholder="Full Name" required>
                <label>Phone</label>
                <input type="text" name="phone" id="edit_phone" placeholder="Phone" required>
                <label>Email</label>
                <input type="email" name="email" id="edit_email" placeholder="Email" required>
                <label>Event Type</label>
                <select name="event_type" id="edit_event_type">
                    <option>Concert / Live Show</option>
                    <option>Corporate Conference</option>
                    <option>Wedding / Ceremony</option>
                    <option>Private Party</option>
                    <option>Brand Activation</option>
                    <option>Graduation</option>
                    <option>Team Building</option>
                    <option>Festival / Expo</option>
                </select>
                <label>Event Date</label>
                <input type="date" name="event_date" id="edit_event_date">
                <label>Venue</label>
                <input type="text" name="venue" id="edit_venue" placeholder="Venue">
                <label>Message</label>
                <textarea name="message" id="edit_message" rows="3" placeholder="Message"></textarea>
                <button type="submit" name="edit" class="btn-submit">💾 Save Changes</button>
            </form>
        </div>
    </div>
    
    <!-- Payment Modal with Amounts -->
    <div id="paymentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('paymentModal')">&times;</span>
            <h3>💳 Update Payment & Amounts</h3>
            <form method="POST">
                <input type="hidden" name="id" id="pay_id">
                
                <label>Payment Status</label>
                <select name="payment_status" id="pay_status">
                    <option value="no_payment">❌ No Payment</option>
                    <option value="deposit_paid">💳 Deposit Paid</option>
                    <option value="partial_paid">📊 Partial Paid</option>
                    <option value="fully_paid">✅ Fully Paid</option>
                </select>
                
                <div class="row">
                    <div>
                        <label>Amount Paid (KES)</label>
                        <input type="number" name="amount_paid" id="pay_amount" placeholder="0.00" step="0.01">
                    </div>
                    <div>
                        <label>Total Amount (KES)</label>
                        <input type="number" name="total_amount" id="pay_total" placeholder="0.00" step="0.01">
                    </div>
                </div>
                
                <div class="row">
                    <div>
                        <label>Deposit Percentage (%)</label>
                        <input type="number" name="deposit_percentage" id="pay_deposit_percent" placeholder="30" step="1" value="30">
                    </div>
                    <div>
                        <label>Payment Method</label>
                        <select name="payment_method" id="pay_method">
                            <option value="">Select</option>
                            <option value="M-Pesa">M-Pesa</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cash">Cash</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>
                </div>
                
                <label>Transaction ID</label>
                <input type="text" name="transaction_id" id="pay_transaction" placeholder="e.g. MPSA123456">
                
                <button type="submit" name="update_payment" class="btn-submit" style="background:#8e44ad;">💾 Update Payment</button>
            </form>
        </div>
    </div>
    
    <script>
        function openEditModal(row) {
            document.getElementById('edit_id').value = row.id;
            document.getElementById('edit_name').value = row.name;
            document.getElementById('edit_phone').value = row.phone;
            document.getElementById('edit_email').value = row.email;
            document.getElementById('edit_event_type').value = row.event_type;
            document.getElementById('edit_event_date').value = row.event_date;
            document.getElementById('edit_venue').value = row.venue || '';
            document.getElementById('edit_message').value = row.message || '';
            document.getElementById('editModal').style.display = 'block';
        }
        
        function openPaymentModal(row) {
            document.getElementById('pay_id').value = row.id;
            document.getElementById('pay_status').value = row.payment_status || 'no_payment';
            document.getElementById('pay_amount').value = row.amount_paid || '';
            document.getElementById('pay_total').value = row.total_amount || '';
            document.getElementById('pay_deposit_percent').value = row.deposit_percentage || 30;
            document.getElementById('pay_method').value = row.payment_method || '';
            document.getElementById('pay_transaction').value = row.transaction_id || '';
            document.getElementById('paymentModal').style.display = 'block';
        }
        
        function closeModal(id) {
            document.getElementById(id).style.display = 'none';
        }
        
        window.onclick = function(event) {
            if (event.target.className == 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>