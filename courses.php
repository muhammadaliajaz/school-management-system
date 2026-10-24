<?php
require_once 'includes/db.php';
include 'includes/header.php';

$result = $conn->query("
    SELECT c.*, GROUP_CONCAT(CONCAT(t.first_name, ' ', t.last_name) SEPARATOR ', ') as assigned_teachers 
    FROM courses c
    LEFT JOIN teacher_allocations ta ON c.id = ta.course_id
    LEFT JOIN teachers t ON ta.teacher_id = t.id
    GROUP BY c.id
    ORDER BY c.id ASC
");
$courses = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Manage Courses</h2>
    <a href="add_course.php" class="btn btn-info text-white"><i class="fa-solid fa-plus me-1"></i> Add New Course</a>
</div>



<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Course Title</th>
                    <th>Credits</th>
                    <th>Department</th>
                    <th>Assigned Teacher(s)</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($courses) > 0): ?>
                    <?php foreach($courses as $course): ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($course['course_code']); ?></span></td>
                        <td class="fw-bold"><?php echo htmlspecialchars($course['name']); ?></td>
                        <td><?php echo $course['credits']; ?></td>
                        <td><?php echo htmlspecialchars($course['department']); ?></td>
                        <td><?php echo $course['assigned_teachers'] ? htmlspecialchars($course['assigned_teachers']) : '<em class="text-muted">None</em>'; ?></td>
                        <td>
                            <a href="edit_course.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-info text-white"><i class="fa-solid fa-pen"></i></a>
                            <a href="delete_course.php?id=<?php echo $course['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this course?');"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">No courses found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
