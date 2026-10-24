<?php
require_once 'includes/db.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    echo "<script>window.location.href='students.php';</script>";
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "<script>window.location.href='students.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $class = trim($_POST['class']);
    $dob = trim($_POST['dob']);
    $address = trim($_POST['address']);
    $status = $_POST['status'] ?? 'Active';

    try {
        $stmt = $conn->prepare("UPDATE students SET first_name=?, last_name=?, email=?, phone=?, class=?, dob=?, address=?, status=? WHERE id=?");
        $stmt->bind_param("ssssssssi", $first_name, $last_name, $email, $phone, $class, $dob, $address, $status, $id);
        $stmt->execute();
        
        $_SESSION['success'] = "Student updated successfully!";
        echo "<script>window.location.href='students.php';</script>";
        exit();
    } catch (Exception $e) {
        $error = "Error updating student: " . $e->getMessage();
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Edit Student</h2>
    <a href="students.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to List</a>
</div>

<?php if(isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="card p-4">
    <form action="" method="POST">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">First Name *</label>
                <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Last Name *</label>
                <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($student['email']); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($student['phone']); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Class / Grade</label>
                <input type="text" name="class" class="form-control" value="<?php echo htmlspecialchars($student['class'] ?? ''); ?>">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="dob" class="form-control" value="<?php echo $student['dob']; ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="Active" <?php echo (($student['status'] ?? 'Active') == 'Active') ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo (($student['status'] ?? '') == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($student['address']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Update Student</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
