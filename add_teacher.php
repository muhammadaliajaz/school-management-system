<?php
require_once 'includes/db.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $specialization = trim($_POST['specialization']);
    $education = trim($_POST['education']);

    try {
        $stmt = $conn->prepare("INSERT INTO teachers (first_name, last_name, email, phone, specialization, education) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first_name, $last_name, $email, $phone, $specialization, $education);
        $stmt->execute();
        
        $_SESSION['success'] = "Teacher added successfully!";
        echo "<script>window.location.href='teachers.php';</script>";
        exit();
    } catch (Exception $e) {
        $error = "Error adding teacher: " . $e->getMessage();
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Add New Teacher</h2>
    <a href="teachers.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to List</a>
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
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Specialization</label>
                <input type="text" name="specialization" class="form-control" placeholder="e.g. Mathematics, Science">
            </div>
            <div class="col-md-6">
                <label class="form-label">Education / Qualification</label>
                <input type="text" name="education" class="form-control" placeholder="e.g. Masters in Physics">
            </div>
        </div>
        <button type="submit" class="btn btn-success"><i class="fa-solid fa-save me-1"></i> Save Teacher</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
