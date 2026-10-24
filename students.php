<?php
require_once 'includes/db.php';
include 'includes/header.php';

$result = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
$students = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Manage Students</h2>
    <a href="add_student.php" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Add New Student</a>
</div>



<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Class</th>
                    <th>Status</th>
                    <th>Enrolled Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($students) > 0): ?>
                    <?php foreach($students as $student): ?>
                    <tr onclick="window.location.href='view_student.php?id=<?php echo $student['id']; ?>'" style="cursor: pointer;">
                        <td><?php echo $student['id']; ?></td>
                        <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['phone']); ?></td>
                        <td><span class="badge bg-info text-dark"><?php echo htmlspecialchars($student['class'] ?? 'N/A'); ?></span></td>
                        <td>
                            <?php if (($student['status'] ?? 'Active') === 'Active'): ?>
                                <span class="badge bg-success">Active</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Inactive</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($student['created_at'])); ?></td>
                        <td onclick="event.stopPropagation();">
                            <a href="view_student.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-success" title="View Details"><i class="fa-solid fa-eye"></i></a>
                            <a href="edit_student.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-info" title="Edit"><i class="fa-solid fa-pen"></i></a>
                            <a href="delete_student.php?id=<?php echo $student['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this student?');" title="Delete"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">No students found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
