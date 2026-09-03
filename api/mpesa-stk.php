<?php
// ============================================
// CORS HEADERS - Allow requests from any domain
// ============================================
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'mpesa-config.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$phone = $input['phone'] ?? '';
$amount = $input['amount'] ?? '';

// Format phone number to 254XXXXXXXXX
$phone = preg_replace('/[^0-9]/', '', $phone);
if (substr($phone, 0, 1) == '0') {
    $phone = '254' . substr($phone, 1);
}
if (strlen($phone) != 12) {
    echo json_encode(['success' => false, 'message' => 'Phone must be 12 digits: 254700000000']);
    exit;
}

// Get access token
$access_token = MpesaConfig::getAccessToken();
if (!$access_token) {
    echo json_encode(['success' => false, 'message' => 'Failed to authenticate with Safaricom']);
    exit;
}

// Prepare STK Push
$timestamp = date('YmdHis');
$password = base64_encode(MpesaConfig::$shortcode . MpesaConfig::$passkey . $timestamp);

$data = array(
    'BusinessShortCode' => MpesaConfig::$shortcode,
    'Password' => $password,
    'Timestamp' => $timestamp,
    'TransactionType' => 'CustomerPayBillOnline',
    'Amount' => (int)$amount,
    'PartyA' => $phone,
    'PartyB' => MpesaConfig::$shortcode,
    'PhoneNumber' => $phone,
    'CallBackURL' => MpesaConfig::$callbackUrl,
    'AccountReference' => 'PAY' . time(),
    'TransactionDesc' => 'Event Payment'
);

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, 'https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest');
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $access_token
));
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($curl);
$http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

$result = json_decode($response);

if ($http_code == 200 && isset($result->ResponseCode) && $result->ResponseCode == '0') {
    // Save to database
    $db = MpesaConfig::getDB();
    if ($db) {
        $stmt = $db->prepare("INSERT INTO mpesa_transactions (checkout_request_id, phone_number, amount, status) VALUES (?, ?, ?, 'pending')");
        $stmt->bind_param("ssd", $result->CheckoutRequestID, $phone, $amount);
        $stmt->execute();
        $db->close();
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Check your phone for M-Pesa prompt',
        'checkoutRequestID' => $result->CheckoutRequestID
    ]);
} else {
    $errorMsg = isset($result->errorMessage) ? $result->errorMessage : 'Payment request failed';
    echo json_encode(['success' => false, 'message' => $errorMsg]);
}
?>