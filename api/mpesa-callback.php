<?php
require_once 'mpesa-config.php';

// Read callback data from Safaricom
$callbackData = file_get_contents('php://input');
$data = json_decode($callbackData, true);

// Save log for debugging
$logFile = __DIR__ . '/../mpesa_log.txt';
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Callback received\n", FILE_APPEND);
file_put_contents($logFile, $callbackData . "\n\n", FILE_APPEND);

if (isset($data['Body']['stkCallback'])) {
    $stkCallback = $data['Body']['stkCallback'];
    $resultCode = $stkCallback['ResultCode'];
    $checkoutRequestID = $stkCallback['CheckoutRequestID'];
    
    $db = MpesaConfig::getDB();
    
    if ($resultCode == 0) {
        // Payment successful
        $amount = $stkCallback['CallbackMetadata']['Item'][0]['Value'];
        $receipt = $stkCallback['CallbackMetadata']['Item'][1]['Value'];
        
        $stmt = $db->prepare("UPDATE mpesa_transactions SET status = 'completed', amount = ?, receipt_number = ? WHERE checkout_request_id = ?");
        $stmt->bind_param("dss", $amount, $receipt, $checkoutRequestID);
        $stmt->execute();
        
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Payment SUCCESS: $receipt\n", FILE_APPEND);
    } else {
        // Payment failed
        $resultDesc = $stkCallback['ResultDesc'];
        $stmt = $db->prepare("UPDATE mpesa_transactions SET status = 'failed' WHERE checkout_request_id = ?");
        $stmt->bind_param("s", $checkoutRequestID);
        $stmt->execute();
        
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Payment FAILED: $resultDesc\n", FILE_APPEND);
    }
    $db->close();
}

// Respond to Safaricom
echo json_encode(['ResultCode' => 0, 'ResultDesc' => 'Success']);
?>