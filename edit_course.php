<?php
require_once 'includes/db.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    echo "<script>window.location.href='courses.php';</script>";
    exit();
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM courses WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();

if (!$course) {
    echo "<script>window.location.href='courses.php';</script>";
    exit();
}

// Fetch current assigned teacher (if any, getting first one for simplicity)
$stmt_ta = $conn->prepare("SELECT teacher_id FROM teacher_allocations WHERE course_id = ? LIMIT 1");
$stmt_ta->bind_param("i", $id);
$stmt_ta->execute();
$ta_result = $stmt_ta->get_result();
$current_teacher = $ta_result->fetch_assoc();
$current_teacher_id = $current_teacher ? $current_teacher['teacher_id'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_code = trim($_POST['course_code']);
    $name = trim($_POST['name']);
    $department = trim($_POST['department']);
    $description = trim($_POST['description']);
    $credits = trim($_POST['credits']);
    $teacher_id = trim($_POST['teacher_id']);

    try {
        $conn->begin_transaction();
        
        $stmt = $conn->prepare("UPDATE courses SET course_code=?, name=?, description=?, credits=?, department=? WHERE id=?");
        $stmt->bind_param("sssisi", $course_code, $name, $description, $credits, $department, $id);
        $stmt->execute();
        
        // Update teacher allocation
        $stmt_del = $conn->prepare("DELETE FROM teacher_allocations WHERE course_id=?");
        $stmt_del->bind_param("i", $id);
        $stmt_del->execute();
        
        if (!empty($teacher_id)) {
            $stmt_ins = $conn->prepare("INSERT INTO teacher_allocations (teacher_id, course_id) VALUES (?, ?)");
            $stmt_ins->bind_param("ii", $teacher_id, $id);
            $stmt_ins->execute();
        }

        $conn->commit();
        $_SESSION['success'] = "Course updated successfully!";
        echo "<script>window.location.href='courses.php';</script>";
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error updating course: " . $e->getMessage();
    }
}

// Fetch teachers for dropdown
$teachers = $conn->query("SELECT id, first_name, last_name, specialization FROM teachers ORDER BY first_name ASC")->fetch_all(MYSQLI_ASSOC);

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Edit Course</h2>
    <a href="courses.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to List</a>
</div>

<?php if(isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="card p-4">
    <form action="" method="POST">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Course Code *</label>
                <input type="text" name="course_code" class="form-control" value="<?php echo htmlspecialchars($course['course_code']); ?>" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Course Title *</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($course['name']); ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Credits *</label>
                <input type="number" name="credits" class="form-control" min="1" max="10" value="<?php echo $course['credits']; ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Department</label>
                <input type="text" name="department" class="form-control" value="<?php echo htmlspecialchars($course['department']); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Assign Teacher</label>
                <select name="teacher_id" class="form-select">
                    <option value="">-- No Teacher Assigned --</option>
                    <?php foreach($teachers as $t): ?>
                        <option value="<?php echo $t['id']; ?>" <?php echo $t['id'] == $current_teacher_id ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($t['first_name'] . ' ' . $t['last_name'] . ' (' . $t['specialization'] . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($course['description']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-info text-white"><i class="fa-solid fa-save me-1"></i> Update Course</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
