<?php
// ============================================
// SAMPLE CONFIGURATION - RENAME TO config.php
// ============================================
// Replace these with your actual database credentials

$host = 'sql108.infinityfree.com';
$user = 'ifo-42702122';
$pass = 'ktgvI2PvxysZd';
$dbname = 'ifo-42702122-activeworld';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>