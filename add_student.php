<?php
require_once 'includes/db.php';
include 'includes/header.php';

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
        $stmt = $conn->prepare("INSERT INTO students (first_name, last_name, email, phone, class, dob, address, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $first_name, $last_name, $email, $phone, $class, $dob, $address, $status);
        $stmt->execute();
        
        $_SESSION['success'] = "Student added successfully!";
        // Using javascript redirect to prevent header already sent issues if any
        echo "<script>window.location.href='students.php';</script>";
        exit();
    } catch (Exception $e) {
        $error = "Error adding student: " . $e->getMessage();
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Add New Student</h2>
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
                <input type="text" name="first_name" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Last Name *</label>
                <input type="text" name="last_name" class="form-control" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Class / Grade</label>
                <input type="text" name="class" class="form-control" placeholder="e.g. Grade 10">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Date of Birth</label>
                <input type="date" name="dob" class="form-control">
            </div>
            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label">Address</label>
            <textarea name="address" class="form-control" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Save Student</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
