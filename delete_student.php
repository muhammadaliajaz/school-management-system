<?php
require_once 'includes/db.php';
require_login();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
        $_SESSION['success'] = "Student deleted successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error deleting student.";
    }
}

header("Location: students.php");
exit();
?>
