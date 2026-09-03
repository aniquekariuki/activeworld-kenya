<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli('localhost', 'root', '', 'activeworld_db');

$result = $conn->query("SELECT id, name, email, phone, event_type, event_date, venue, status, created_at FROM event_requests ORDER BY created_at DESC");

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = $row;
}

echo json_encode(['success' => true, 'data' => $events]);
?>