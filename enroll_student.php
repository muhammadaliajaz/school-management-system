<?php
require_once 'includes/db.php';
include 'includes/header.php';

// Handle Enrollment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'enroll') {
    $student_id = $_POST['student_id'];
    $course_ids = $_POST['course_ids'] ?? [];

    if (!empty($course_ids) && is_array($course_ids)) {
        $success_count = 0;
        $error_count = 0;
        
        $stmt = $conn->prepare("INSERT IGNORE INTO enrollments (student_id, course_id) VALUES (?, ?)");
        
        foreach ($course_ids as $course_id) {
            $stmt->bind_param("ii", $student_id, $course_id);
            $stmt->execute();
            
            // affected_rows is 1 if inserted, 0 if ignored due to duplicate
            if ($stmt->affected_rows > 0) {
                $success_count++;
            } else {
                $error_count++;
            }
        }
        
        if ($success_count > 0) {
            $_SESSION['success'] = "$success_count course(s) assigned successfully!";
            if ($error_count > 0) {
                $_SESSION['error'] = "$error_count course(s) could not be assigned (likely already enrolled).";
            }
        } else {
            $_SESSION['error'] = "Could not assign courses. Student may already be enrolled.";
        }
    } else {
        $_SESSION['error'] = "Please select at least one course.";
    }

    echo "<script>window.location.href='enroll_student.php';</script>";
    exit();
}

// Handle Unenrollment
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM enrollments WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $_SESSION['success'] = "Enrollment removed successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error removing enrollment.";
    }
    echo "<script>window.location.href='enroll_student.php';</script>";
    exit();
}

// Handle Clear All for Student
if (isset($_GET['delete_student_id'])) {
    $s_id = $_GET['delete_student_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM enrollments WHERE student_id = ?");
        $stmt->bind_param("i", $s_id);
        $stmt->execute();
        $_SESSION['success'] = "All enrollments for the student removed successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error removing enrollments.";
    }
    echo "<script>window.location.href='enroll_student.php';</script>";
    exit();
}

$students = $conn->query("SELECT id, first_name, last_name FROM students")->fetch_all(MYSQLI_ASSOC);
$courses = $conn->query("SELECT id, name FROM courses")->fetch_all(MYSQLI_ASSOC);

$enrollments_raw = $conn->query("
    SELECT 
        enrollments.id, 
        students.id AS student_id, 
        students.first_name, 
        students.last_name, 
        courses.name AS course_name, 
        enrollments.enrollment_date 
    FROM enrollments
    JOIN students ON enrollments.student_id = students.id
    JOIN courses ON enrollments.course_id = courses.id
    ORDER BY enrollments.enrollment_date DESC
")->fetch_all(MYSQLI_ASSOC);

$grouped_enrollments = [];
foreach ($enrollments_raw as $row) {
    $s_id = $row['student_id'];
    if (!isset($grouped_enrollments[$s_id])) {
        $grouped_enrollments[$s_id] = [
            'student_name' => $row['first_name'] . ' ' . $row['last_name'],
            'courses' => [],
            'latest_enrollment' => $row['enrollment_date']
        ];
    }
    $grouped_enrollments[$s_id]['courses'][] = [
        'id' => $row['id'],
        'course_name' => $row['course_name']
    ];
}
?>

</div> <!-- Close default container from header -->
<div class="container-fluid px-xl-5 px-3">
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Student Enrollments (Course Assignments)</h2>
</div>



<div class="row">
    <div class="col-lg-2 col-md-3">
        <div class="card p-4 mb-4">
            <h5 class="fw-bold mb-3">Assign Course to Student</h5>
            <form action="" method="POST">
                <input type="hidden" name="action" value="enroll">
                <div class="mb-3">
                    <label class="form-label">Select Student</label>
                    <select name="student_id" class="form-select" required>
                        <option value="">-- Select Student --</option>
                        <?php foreach($students as $s): ?>
                            <option value="<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name']); ?></option>
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
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-plus me-1"></i> Assign Course</button>
            </form>
        </div>
    </div>
    <div class="col-lg-10 col-md-9">
        <div class="card p-4">
            <h5 class="fw-bold mb-3">Recent Assignments</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Courses Assigned</th>
                            <th>Last Enrolled</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($grouped_enrollments) > 0): ?>
                            <?php foreach($grouped_enrollments as $student_id => $data): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($data['student_name']); ?></td>
                                <td>
                                    <?php foreach($data['courses'] as $course): ?>
                                        <span class="badge bg-info text-dark d-inline-flex align-items-center me-1 mb-1" style="font-size: 0.85rem; padding: 0.4em 0.6em;">
                                            <?php echo htmlspecialchars($course['course_name']); ?>
                                            <a href="?delete_id=<?php echo $course['id']; ?>" class="text-danger ms-2 text-decoration-none" onclick="return confirm('Remove this course assignment?');" title="Remove Course" style="font-size: 1.2em; line-height: 0.5;">&times;</a>
                                        </span>
                                    <?php endforeach; ?>
                                </td>
                                <td><?php echo date('M d, Y', strtotime($data['latest_enrollment'])); ?></td>
                                <td>
                                    <a href="?delete_student_id=<?php echo $student_id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove all courses for this student?');"><i class="fa-solid fa-trash"></i> Clear All</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center text-muted">No assignments found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
