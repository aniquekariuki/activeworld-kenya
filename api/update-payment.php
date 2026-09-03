<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'activeworld_db');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get POST data
$id = (int)$_POST['id'];
$amount_paid = (float)$_POST['amount_paid'];
$total_amount = (float)$_POST['total_amount'];
$deposit_percentage = (int)$_POST['deposit_percentage'];
$payment_method = $_POST['payment_method'] ?? null;
$transaction_id = $_POST['transaction_id'] ?? null;

// Calculate deposit amount
$deposit_amount = ($deposit_percentage / 100) * $total_amount;

// Determine payment status
if ($amount_paid <= 0) {
    $payment_status = 'no_payment';
} elseif ($amount_paid >= $total_amount) {
    $payment_status = 'fully_paid';
} elseif ($amount_paid >= $deposit_amount) {
    $payment_status = 'deposit_paid';
} else {
    $payment_status = 'partial_paid';
}

// Update database
$sql = "UPDATE event_requests SET 
    amount_paid = $amount_paid,
    total_amount = $total_amount,
    deposit_percentage = $deposit_percentage,
    deposit_amount = $deposit_amount,
    payment_status = '$payment_status',
    payment_method = " . ($payment_method ? "'$payment_method'" : "NULL") . ",
    transaction_id = " . ($transaction_id ? "'$transaction_id'" : "NULL") . ",
    payment_date = CURDATE()
    WHERE id = $id";

if ($conn->query($sql)) {
    echo json_encode([
        'success' => true,
        'message' => 'Payment updated successfully!',
        'payment_status' => $payment_status,
        'deposit_amount' => $deposit_amount,
        'status_label' => getStatusLabel($payment_status)
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed: ' . $conn->error]);
}

$conn->close();

function getStatusLabel($status) {
    $labels = [
        'no_payment' => '❌ No Payment',
        'deposit_paid' => '💳 Deposit Paid',
        'partial_paid' => '📊 Partial Paid',
        'fully_paid' => '✅ Fully Paid'
    ];
    return $labels[$status] ?? $status;
}
?>