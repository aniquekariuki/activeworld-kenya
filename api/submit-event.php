<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    ob_end_clean(); http_response_code(200); exit();
}

header('Content-Type: application/json');

function send($data) {
    ob_end_clean();
    echo json_encode($data);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send(['success' => false, 'message' => 'Method not allowed']);
}

// Read input
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) $input = $_POST;
if (!$input) send(['success' => false, 'message' => 'No data received.']);

// Basic validation
$name       = trim($input['name']       ?? '');
$phone      = trim($input['phone']      ?? '');
$email      = trim($input['email']      ?? '');
$event_type = trim($input['event_type'] ?? '');
$event_date = trim($input['event_date'] ?? '') ?: null;
$guests     = trim($input['guests']     ?? '') ?: null;
$venue      = trim($input['venue']      ?? '') ?: null;
$msg        = trim($input['message']    ?? '') ?: null;

if (!$name)       send(['success'=>false,'message'=>'Please enter your full name.']);
if (!$phone)      send(['success'=>false,'message'=>'Please enter your phone number.']);
if (!$email)      send(['success'=>false,'message'=>'Please enter your email.']);
if (!$event_type) send(['success'=>false,'message'=>'Please select an event type.']);

$phone = preg_replace('/[^0-9]/', '', $phone);
if (substr($phone,0,1)==='0') $phone = '254'.substr($phone,1);
$phone_display = '+'.$phone;

// Connect to DB
$conn = new mysqli('localhost', 'root', '', 'activeworld_db');
if ($conn->connect_error) {
    send(['success'=>false,'message'=>'DB Error: '.$conn->connect_error]);
}

// Auto-create table
$conn->query("CREATE TABLE IF NOT EXISTS event_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(25) NOT NULL,
    email VARCHAR(150) NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    event_date DATE,
    guests VARCHAR(50),
    venue VARCHAR(200),
    message TEXT,
    status VARCHAR(30) DEFAULT 'pending',
    reference VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

// Insert
$stmt = $conn->prepare("INSERT INTO event_requests (name,phone,email,event_type,event_date,guests,venue,message,status) VALUES (?,?,?,?,?,?,?,?,'pending')");
$stmt->bind_param("ssssssss", $name, $phone_display, $email, $event_type, $event_date, $guests, $venue, $msg);

if (!$stmt->execute()) {
    send(['success'=>false,'message'=>'DB insert failed: '.$conn->error]);
}

$id  = $stmt->insert_id;
$ref = 'AW'.str_pad($id, 5, '0', STR_PAD_LEFT);
$conn->query("UPDATE event_requests SET reference='$ref' WHERE id=$id");
$stmt->close();
$conn->close();

// Save email to file (localhost)
$dir = __DIR__.'/../email_logs/';
if (!is_dir($dir)) mkdir($dir, 0777, true);
file_put_contents($dir.date('Y-m-d_H-i-s')."_$ref.txt",
    "To: $email\nName: $name\nPhone: $phone_display\nEvent: $event_type\nDate: $event_date\nRef: $ref\n"
);

send(['success'=>true, 'message'=>'Request submitted! We will contact you within 24 hours.', 'reference'=>$ref]);