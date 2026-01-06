<?php
// db.php
session_start();

$host = 'localhost';
$db   = 'grocery_db';
$user = 'root';
$pass = ''; // Default XAMPP password is empty

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Helper function for security (XSS prevention)
function escape($string) {
    global $conn;
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>