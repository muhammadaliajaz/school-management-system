<?php

require_once 'includes/db.php';
include 'includes/header.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_code = trim($_POST['course_code']);
    $name = trim($_POST['name']);
    $department = trim($_POST['department']);
    $description = trim($_POST['description']);
    $credits = trim($_POST['credits']);
    $teacher_id = trim($_POST['teacher_id']);

    try {
        // Start transaction
        $conn->begin_transaction();

        $stmt = $conn->prepare("INSERT INTO courses (course_code, name, description, credits, department) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssis", $course_code, $name, $description, $credits, $department);
        $stmt->execute();
        
        $course_id = $conn->insert_id;

        if (!empty($teacher_id)) {
            $stmt_teacher = $conn->prepare("INSERT INTO teacher_allocations (teacher_id, course_id) VALUES (?, ?)");
            $stmt_teacher->bind_param("ii", $teacher_id, $course_id);
            $stmt_teacher->execute();
        }

        $conn->commit();
        $_SESSION['success'] = "Course added successfully!";
        echo "<script>window.location.href='courses.php';</script>";
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Error adding course: " . $e->getMessage();
    }
}

// Fetch teachers for dropdown
$teachers = $conn->query("SELECT id, first_name, last_name, specialization FROM teachers ORDER BY first_name ASC")->fetch_all(MYSQLI_ASSOC);

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Add New Course</h2>
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
                <input type="text" name="course_code" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Course Title *</label>
                <input type="text" name="name" class="form-control" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Credits *</label>
                <input type="number" name="credits" class="form-control" min="1" max="10" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Department</label>
                <input type="text" name="department" class="form-control">
            </div>
            <div class="col-md-4">
                <label class="form-label">Assign Teacher</label>
                <select name="teacher_id" class="form-select">
                    <option value="">-- No Teacher Assigned --</option>
                    <?php foreach($teachers as $t): ?>
                        <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['first_name'] . ' ' . $t['last_name'] . ' (' . $t['specialization'] . ')'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="4"></textarea>
        </div>
        <button type="submit" class="btn btn-info text-white"><i class="fa-solid fa-save me-1"></i> Save Course</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
