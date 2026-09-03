<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'activeworld_db');

// Get payment summary
$summary = $conn->query("
    SELECT 
        SUM(CASE WHEN payment_status = 'paid' THEN 1 ELSE 0 END) as paid_count,
        SUM(CASE WHEN payment_status = 'pending' THEN 1 ELSE 0 END) as pending_count,
        SUM(CASE WHEN payment_status = 'partial' THEN 1 ELSE 0 END) as partial_count,
        SUM(total_amount) as total_amount,
        SUM(deposit_paid) as total_paid,
        SUM(balance) as total_balance
    FROM event_requests
")->fetch_assoc();

// Get all payments
$payments = $conn->query("
    SELECT p.*, e.name as customer_name, e.event_type 
    FROM payments p 
    JOIN event_requests e ON p.event_id = e.id 
    ORDER BY p.created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Reports - Active World</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 1400px; margin: 0 auto; }
        h1 { color: #F26419; margin-bottom: 20px; }
        .summary { display: grid; grid-template-columns: repeat(6, 1fr); gap: 20px; margin-bottom: 30px; }
        .summary-card { background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .summary-number { font-size: 28px; font-weight: bold; color: #F26419; }
        .summary-label { color: #666; margin-top: 5px; }
        table { width: 100%; background: white; border-radius: 10px; border-collapse: collapse; overflow: hidden; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #2c3e50; color: white; }
        .back-btn { background: #F26419; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-bottom: 20px; }
        .status-paid { color: #27ae60; font-weight: bold; }
        .status-pending { color: #e74c3c; font-weight: bold; }
        .status-partial { color: #f39c12; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
        
        <h1>💰 Payment Reports</h1>
        
        <div class="summary">
            <div class="summary-card">
                <div class="summary-number"><?php echo $summary['paid_count']; ?></div>
                <div class="summary-label">Paid Events</div>
            </div>
            <div class="summary-card">
                <div class="summary-number"><?php echo $summary['pending_count']; ?></div>
                <div class="summary-label">Pending Events</div>
            </div>
            <div class="summary-card">
                <div class="summary-number"><?php echo $summary['partial_count']; ?></div>
                <div class="summary-label">Partial Payments</div>
            </div>
            <div class="summary-card">
                <div class="summary-number">KES <?php echo number_format($summary['total_amount']); ?></div>
                <div class="summary-label">Total Value</div>
            </div>
            <div class="summary-card">
                <div class="summary-number">KES <?php echo number_format($summary['total_paid']); ?></div>
                <div class="summary-label">Total Paid</div>
            </div>
            <div class="summary-card">
                <div class="summary-number">KES <?php echo number_format($summary['total_balance']); ?></div>
                <div class="summary-label">Total Balance</div>
            </div>
        </div>
        
        <h2>Payment History</h2>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Event</th>
                    <th>Amount (KES)</th>
                    <th>Method</th>
                    <th>Transaction ID</th>
                    <th>Receipt</th>
                    <th>Recorded By</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php while($payment = $payments->fetch_assoc()): ?>
                <tr>
                    <td><?php echo date('M d, Y H:i', strtotime($payment['created_at'])); ?></td>
                    <td><?php echo htmlspecialchars($payment['customer_name']); ?></td>
                    <td><?php echo htmlspecialchars($payment['event_type']); ?></td>
                    <td>KES <?php echo number_format($payment['amount']); ?></td>
                    <td>
                        <?php 
                        $method_icon = [
                            'mpesa' => '💳 M-Pesa',
                            'bank' => '🏦 Bank',
                            'cash' => '💵 Cash',
                            'cheque' => '📝 Cheque'
                        ];
                        echo $method_icon[$payment['payment_method']] ?? $payment['payment_method'];
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($payment['transaction_id'] ?: '-'); ?></td>
                    <td><?php echo htmlspecialchars($payment['receipt_number'] ?: '-'); ?></td>
                    <td><?php echo htmlspecialchars($payment['created_by']); ?></td>
                    <td><?php echo htmlspecialchars(substr($payment['notes'], 0, 50)); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>