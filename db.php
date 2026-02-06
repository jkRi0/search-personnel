<?php
// Database connection settings - change to match your MySQL config
$host = 'localhost';
$user = 'root';      // default XAMPP user
$pass = '';          // default XAMPP password (empty)
$dbname = 'personnel_db';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Ensure connection uses UTF-8 for proper handling of characters like Ñ
$conn->set_charset('utf8mb4');
?>
