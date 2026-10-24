<?php
require_once 'includes/db.php';
require_login();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $conn->prepare("DELETE FROM teachers WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
        $_SESSION['success'] = "Teacher deleted successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting teacher.";
    }
}

header("Location: teachers.php");
exit();
?>
