<?php
require_once 'includes/db.php';
include 'includes/header.php';

// Handle Allocation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'assign') {
    $teacher_id = $_POST['teacher_id'];
    $course_ids = $_POST['course_ids'] ?? [];

    if (!empty($course_ids) && is_array($course_ids)) {
        $success_count = 0;
        $error_count = 0;
        
        $check_stmt = $conn->prepare("SELECT id FROM teacher_allocations WHERE teacher_id = ? AND course_id = ?");
        $stmt = $conn->prepare("INSERT INTO teacher_allocations (teacher_id, course_id) VALUES (?, ?)");
        
        foreach ($course_ids as $course_id) {
            $check_stmt->bind_param("ii", $teacher_id, $course_id);
            $check_stmt->execute();
            $result = $check_stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error_count++;
            } else {
                try {
                    $stmt->bind_param("ii", $teacher_id, $course_id);
                    if ($stmt->execute()) {
                        $success_count++;
                    } else {
                        $error_count++;
                    }
                } catch (Exception $e) {
                    $error_count++;
                }
            }
        }
        
        if ($success_count > 0) {
            $_SESSION['success'] = "$success_count course(s) assigned successfully!";
            if ($error_count > 0) {
                $_SESSION['error'] = "$error_count course(s) could not be assigned (likely already assigned).";
            }
        } else {
            $_SESSION['error'] = "Could not assign courses. Teacher may already be assigned.";
        }
    } else {
        $_SESSION['error'] = "Please select at least one course.";
    }
    echo "<script>window.location.href='assign_teacher.php';</script>";
    exit();
}

// Handle Removal
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM teacher_allocations WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $_SESSION['success'] = "Teacher assignment removed successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error removing assignment.";
    }
    echo "<script>window.location.href='assign_teacher.php';</script>";
    exit();
}

// Handle Clear All for Teacher
if (isset($_GET['delete_teacher_id'])) {
    $t_id = $_GET['delete_teacher_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM teacher_allocations WHERE teacher_id = ?");
        $stmt->bind_param("i", $t_id);
        $stmt->execute();
        $_SESSION['success'] = "All assignments for the teacher removed successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error removing assignments.";
    }
    echo "<script>window.location.href='assign_teacher.php';</script>";
    exit();
}

$teachers = $conn->query("SELECT id, first_name, last_name, specialization FROM teachers")->fetch_all(MYSQLI_ASSOC);
$courses = $conn->query("SELECT id, name FROM courses")->fetch_all(MYSQLI_ASSOC);

$allocations_raw = $conn->query("
    SELECT ta.id, t.id as teacher_id, t.first_name, t.last_name, c.name as course_name 
    FROM teacher_allocations ta
    JOIN teachers t ON ta.teacher_id = t.id
    JOIN courses c ON ta.course_id = c.id
    ORDER BY ta.id DESC
")->fetch_all(MYSQLI_ASSOC);

$grouped_allocations = [];
foreach ($allocations_raw as $row) {
    $t_id = $row['teacher_id'];
    if (!isset($grouped_allocations[$t_id])) {
        $grouped_allocations[$t_id] = [
            'teacher_name' => $row['first_name'] . ' ' . $row['last_name'],
            'courses' => []
        ];
    }
    $grouped_allocations[$t_id]['courses'][] = [
        'id' => $row['id'],
        'course_name' => $row['course_name']
    ];
}
?>

</div> <!-- Close default container from header -->
<div class="container-fluid px-xl-5 px-3">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Teacher Allocations</h2>
</div>

<?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-2 col-md-3">
        <div class="card p-4 mb-4">
            <h5 class="fw-bold mb-3">Assign Teacher to Course</h5>
            <form action="" method="POST">
                <input type="hidden" name="action" value="assign">
                <div class="mb-3">
                    <label class="form-label">Select Teacher</label>
                    <select name="teacher_id" class="form-select" required>
                        <option value="">-- Select Teacher --</option>
                        <?php foreach($teachers as $t): ?>
                            <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['first_name'] . ' ' . $t['last_name'] . ' (' . $t['specialization'] . ')'); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Select Course(s)</label>
                    <select name="course_ids[]" class="form-select" multiple required style="height: 150px;">
                        <?php foreach($courses as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted d-block mt-1">Hold Ctrl (or Cmd on Mac) to select multiple courses.</small>
                </div>
                <button type="submit" class="btn btn-success w-100"><i class="fa-solid fa-chalkboard-user me-1"></i> Assign</button>
            </form>
        </div>
    </div>
    <div class="col-lg-10 col-md-9">
        <div class="card p-4">
            <h5 class="fw-bold mb-3">Current Allocations</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Teacher</th>
                            <th>Courses Assigned</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($grouped_allocations) > 0): ?>
                            <?php foreach($grouped_allocations as $teacher_id => $data): ?>
                            <tr>
                                <td><i class="fa-solid fa-chalkboard-user text-muted me-2"></i> <?php echo htmlspecialchars($data['teacher_name']); ?></td>
                                <td>
                                    <?php foreach($data['courses'] as $course): ?>
                                        <span class="badge bg-primary d-inline-flex align-items-center me-1 mb-1" style="font-size: 0.85rem; padding: 0.4em 0.6em;">
                                            <?php echo htmlspecialchars($course['course_name']); ?>
                                            <a href="?delete_id=<?php echo $course['id']; ?>" class="text-white ms-2 text-decoration-none" onclick="return confirm('Remove this course assignment?');" title="Remove Course" style="font-size: 1.2em; line-height: 0.5;">&times;</a>
                                        </span>
                                    <?php endforeach; ?>
                                </td>
                                <td>
                                    <a href="?delete_teacher_id=<?php echo $teacher_id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove all courses for this teacher?');"><i class="fa-solid fa-trash"></i> Clear All</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3" class="text-center text-muted">No allocations found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="card p-4 mt-4">
    <h5 class="fw-bold mb-3"><i class="fa-solid fa-chart-bar me-2"></i> Teacher Workloads Summary</h5>
    <?php
    $loads = $conn->query("
        SELECT t.id, t.first_name, t.last_name, t.specialization, COUNT(ta.id) as course_count, GROUP_CONCAT(c.name SEPARATOR ', ') as assigned_courses
        FROM teachers t
        LEFT JOIN teacher_allocations ta ON t.id = ta.teacher_id
        LEFT JOIN courses c ON ta.course_id = c.id
        GROUP BY t.id
        ORDER BY course_count DESC
    ")->fetch_all(MYSQLI_ASSOC);
    ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Teacher Name</th>
                    <th>Specialization</th>
                    <th>Total Load (Courses)</th>
                    <th>Assigned Subjects</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($loads as $load): ?>
                <tr>
                    <td class="fw-bold"><?php echo htmlspecialchars($load['first_name'] . ' ' . $load['last_name']); ?></td>
                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($load['specialization'] ?? 'N/A'); ?></span></td>
                    <td>
                        <span class="badge <?php echo $load['course_count'] > 2 ? 'bg-danger' : 'bg-success'; ?>">
                            <?php echo $load['course_count']; ?> Courses
                        </span>
                    </td>
                    <td class="text-muted text-sm"><?php echo $load['assigned_courses'] ? htmlspecialchars($load['assigned_courses']) : '<em>No assignments</em>'; ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
