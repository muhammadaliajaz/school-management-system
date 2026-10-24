<?php
require_once 'includes/db.php';
include 'includes/header.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $amount = trim($_POST['amount']);
    $due_date = trim($_POST['due_date']);
    $status = $_POST['status'];
    $payment_date = ($status == 'Paid') ? date('Y-m-d') : null;

    try {
        $stmt = $conn->prepare("INSERT INTO fees (student_id, amount, due_date, status, payment_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("idsss", $student_id, $amount, $due_date, $status, $payment_date);
        $stmt->execute();
        
        $_SESSION['success'] = "Fee record created successfully!";
        echo "<script>window.location.href='fees.php';</script>";
        exit();
    } catch (Exception $e) {
        $error = "Error creating fee record: " . $e->getMessage();
    }
}

$students = $conn->query("SELECT id, first_name, last_name FROM students")->fetch_all(MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Add Fee Record</h2>
    <a href="fees.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to Fees</a>
</div>

<?php if(isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="card p-4">
    <form action="" method="POST">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Select Student *</label>
                <select name="student_id" class="form-select" required>
                    <option value="">-- Choose Student --</option>
                    <?php foreach($students as $s): ?>
                        <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Amount (Rs.) *</label>
                <input type="number" step="0.01" name="amount" class="form-control" required placeholder="e.g. 500.00">
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-6">
                <label class="form-label">Due Date *</label>
                <input type="date" name="due_date" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Status *</label>
                <select name="status" class="form-select" required>
                    <option value="Pending">Pending</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-warning fw-bold text-dark"><i class="fa-solid fa-save me-1"></i> Create Record</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
