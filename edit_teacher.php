<?php
require_once 'includes/db.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    echo "<script>window.location.href='teachers.php';</script>";
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM teachers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$teacher = $result->fetch_assoc();

if (!$teacher) {
    echo "<script>window.location.href='teachers.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $specialization = trim($_POST['specialization']);
    $education = trim($_POST['education']);

    try {
        $stmt = $conn->prepare("UPDATE teachers SET first_name=?, last_name=?, email=?, phone=?, specialization=?, education=? WHERE id=?");
        $stmt->bind_param("ssssssi", $first_name, $last_name, $email, $phone, $specialization, $education, $id);
        $stmt->execute();
        
        $_SESSION['success'] = "Teacher updated successfully!";
        echo "<script>window.location.href='teachers.php';</script>";
        exit();
    } catch (Exception $e) {
        $error = "Error updating teacher: " . $e->getMessage();
    }
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Edit Teacher</h2>
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
                <input type="text" name="first_name" class="form-control" value="<?php echo htmlspecialchars($teacher['first_name']); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Last Name *</label>
                <input type="text" name="last_name" class="form-control" value="<?php echo htmlspecialchars($teacher['last_name']); ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($teacher['email']); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($teacher['phone']); ?>">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Specialization</label>
                <input type="text" name="specialization" class="form-control" value="<?php echo htmlspecialchars($teacher['specialization']); ?>">
            </div>
            <div class="col-md-6">
                <label class="form-label">Education / Qualification</label>
                <input type="text" name="education" class="form-control" value="<?php echo htmlspecialchars($teacher['education'] ?? ''); ?>">
            </div>
        </div>
        <button type="submit" class="btn btn-success"><i class="fa-solid fa-save me-1"></i> Update Teacher</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
