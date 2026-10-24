<?php
require_once 'includes/db.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    echo "<script>window.location.href='students.php';</script>";
    exit();
}

$id = $_GET['id'];

// Get student details
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    echo "<script>window.location.href='students.php';</script>";
    exit();
}

// Get enrolled courses
$course_stmt = $conn->prepare("
    SELECT c.course_code, c.name, c.credits, e.enrollment_date 
    FROM enrollments e 
    JOIN courses c ON e.course_id = c.id 
    WHERE e.student_id = ?
");
$course_stmt->bind_param("i", $id);
$course_stmt->execute();
$courses_result = $course_stmt->get_result();
$courses = [];
if ($courses_result) {
    $courses = $courses_result->fetch_all(MYSQLI_ASSOC);
}

// Get fee status
$fee_stmt = $conn->prepare("SELECT * FROM fees WHERE student_id = ? ORDER BY due_date DESC");
$fee_stmt->bind_param("i", $id);
$fee_stmt->execute();
$fees_result = $fee_stmt->get_result();
$fees = [];
if ($fees_result) {
    $fees = $fees_result->fetch_all(MYSQLI_ASSOC);
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Student Details</h2>
    <div>
        <a href="edit_student.php?id=<?php echo $id; ?>" class="btn btn-info text-white me-2"><i class="fa-solid fa-pen me-1"></i> Edit</a>
        <a href="students.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to List</a>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-primary text-white border-0">
                <h5 class="card-title mb-0"><i class="fa-solid fa-user me-2"></i> Personal Info</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                        <i class="fa-solid fa-user fa-3x text-secondary"></i>
                    </div>
                    <h4 class="mt-3 mb-1"><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></h4>
                </div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0 d-flex justify-content-between"><strong>Student ID:</strong> <span><?php echo $student['id']; ?></span></li>
                    <li class="list-group-item px-0 d-flex justify-content-between"><strong>Status:</strong> <span>
                        <?php if (($student['status'] ?? 'Active') === 'Active'): ?>
                            <span class="badge bg-success">Active</span>
                        <?php else: ?>
                            <span class="badge bg-danger">Inactive</span>
                        <?php endif; ?>
                    </span></li>
                    <li class="list-group-item px-0 d-flex justify-content-between"><strong>Class:</strong> <span><?php echo htmlspecialchars($student['class'] ?: 'N/A'); ?></span></li>
                    <li class="list-group-item px-0 d-flex justify-content-between"><strong>Email:</strong> <span><?php echo htmlspecialchars($student['email'] ?: 'N/A'); ?></span></li>
                    <li class="list-group-item px-0 d-flex justify-content-between"><strong>Phone:</strong> <span><?php echo htmlspecialchars($student['phone'] ?: 'N/A'); ?></span></li>
                    <li class="list-group-item px-0 d-flex justify-content-between"><strong>DOB:</strong> <span><?php echo $student['dob'] ? date('M d, Y', strtotime($student['dob'])) : 'N/A'; ?></span></li>
                    <li class="list-group-item px-0 d-flex justify-content-between"><strong>Enrolled:</strong> <span><?php echo date('M d, Y', strtotime($student['created_at'])); ?></span></li>
                    <li class="list-group-item px-0">
                        <strong>Address:</strong><br>
                        <span class="text-muted"><?php echo htmlspecialchars($student['address'] ?: 'N/A'); ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-success text-white border-0">
                <h5 class="card-title mb-0"><i class="fa-solid fa-book me-2"></i> Enrolled Courses</h5>
            </div>
            <div class="card-body p-0">
                <?php if(count($courses) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Code</th>
                                    <th>Course Name</th>
                                    <th>Credits</th>
                                    <th>Enrolled On</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($courses as $c): ?>
                                <tr>
                                    <td class="ps-3"><span class="badge bg-secondary"><?php echo htmlspecialchars($c['course_code']); ?></span></td>
                                    <td><?php echo htmlspecialchars($c['name']); ?></td>
                                    <td><?php echo $c['credits']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($c['enrollment_date'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-center text-muted">
                        <i class="fa-solid fa-book-open fa-2x mb-2 text-light"></i>
                        <p class="mb-0">No courses enrolled yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-warning text-dark border-0">
                <h5 class="card-title mb-0"><i class="fa-solid fa-file-invoice-dollar me-2"></i> Fee History</h5>
            </div>
            <div class="card-body p-0">
                <?php if(count($fees) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Amount</th>
                                    <th>Due Date</th>
                                    <th>Status</th>
                                    <th>Payment Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($fees as $f): ?>
                                <tr>
                                    <td class="ps-3 fw-bold"><?php echo number_format($f['amount'], 2); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($f['due_date'])); ?></td>
                                    <td>
                                        <?php if($f['status'] == 'Paid'): ?>
                                            <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Paid</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="fa-solid fa-clock me-1"></i> Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $f['payment_date'] ? date('M d, Y', strtotime($f['payment_date'])) : '<span class="text-muted">-</span>'; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="p-4 text-center text-muted">
                        <i class="fa-solid fa-receipt fa-2x mb-2 text-light"></i>
                        <p class="mb-0">No fee records found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
