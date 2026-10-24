<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$host = 'localhost';
$dbname = 'school_management_system';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $dbname);
$conn->set_charset("utf8mb4");

if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}

// Function to check if admin is logged in
function require_login() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: index.php");
        exit();
    }
}
?>
