<?php
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $_SESSION['error'] = "Please enter both username and password.";
        header("Location: index.php");
        exit();
    }

    try {
        $stmt = $conn->prepare("SELECT id, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($password, $admin['password'])) {
            // Login successful
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['username'] = $username;
            header("Location: dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid username or password.";
            header("Location: index.php");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Database error: " . $e->getMessage();
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
