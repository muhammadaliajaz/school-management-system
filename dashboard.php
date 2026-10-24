<?php
require_once 'includes/db.php';
include 'includes/header.php';

// Fetch statistics
$total_students = $conn->query("SELECT COUNT(*) as c FROM students")->fetch_assoc()['c'];
$total_teachers = $conn->query("SELECT COUNT(*) as c FROM teachers")->fetch_assoc()['c'];
$total_courses = $conn->query("SELECT COUNT(*) as c FROM courses")->fetch_assoc()['c'];

$total_revenue = $conn->query("SELECT SUM(amount) as total FROM fees WHERE status='Paid'")->fetch_assoc()['total'] ?? 0;

if (!$total_revenue) $total_revenue = 0.00;
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Main Dashboard</h2>
    <span class="text-muted">Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</span>
</div>

<div class="row g-4">
    <div class="col-md-3">
        <div class="card text-center p-4">
            <h1 class="display-4 text-primary fw-bold"><?php echo $total_students; ?></h1>
            <p class="text-muted mb-0">Total Students</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-4">
            <h1 class="display-4 text-success fw-bold"><?php echo $total_teachers; ?></h1>
            <p class="text-muted mb-0">Total Teachers</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-4">
            <h1 class="display-4 text-info fw-bold"><?php echo $total_courses; ?></h1>
            <p class="text-muted mb-0">Total Courses</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center p-4">
            <h1 class="text-warning fw-bold">Rs. <?php echo number_format($total_revenue, 2); ?></h1>
            <p class="text-muted mb-0">Total Revenue</p>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-6">
        <div class="card p-4 h-100">
            <h5 class="fw-bold mb-3">Recent Admissions</h5>
            <?php
            $recent_students = $conn->query("SELECT first_name, last_name, created_at FROM students ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);
            ?>
            <ul class="list-group list-group-flush mb-3">
                <?php if (count($recent_students) > 0): ?>
                    <?php foreach ($recent_students as $student): ?>
                        <li class="list-group-item d-flex justify-content-between">
                            <span><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></span>
                            <small class="text-muted"><?php echo date('M d, Y', strtotime($student['created_at'])); ?></small>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="list-group-item text-muted">No students admitted yet.</li>
                <?php endif; ?>
            </ul>
            <div class="mt-auto text-end">
                <a href="students.php" class="btn btn-sm btn-outline-primary">Show All Students</a>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-4">
            <h5 class="fw-bold mb-3">Quick Actions</h5>
            <div class="d-grid gap-2">
                <a href="students.php" class="btn btn-outline-primary text-start"><i class="fa-solid fa-user-plus me-2"></i> Manage Students</a>
                <a href="teachers.php" class="btn btn-outline-success text-start"><i class="fa-solid fa-chalkboard-user me-2"></i> Manage Teachers</a>
                <a href="courses.php" class="btn btn-outline-info text-start"><i class="fa-solid fa-book me-2"></i> Manage Courses</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
