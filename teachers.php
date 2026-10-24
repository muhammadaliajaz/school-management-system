<?php
require_once 'includes/db.php';
include 'includes/header.php';

$result = $conn->query("SELECT * FROM teachers ORDER BY created_at DESC");
$teachers = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Manage Teachers</h2>
    <a href="add_teacher.php" class="btn btn-success"><i class="fa-solid fa-plus me-1"></i> Add New Teacher</a>
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
                    <th>Specialization</th>
                    <th>Education</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($teachers) > 0): ?>
                    <?php foreach($teachers as $teacher): ?>
                    <tr>
                        <td><?php echo $teacher['id']; ?></td>
                        <td><?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                        <td><?php echo htmlspecialchars($teacher['phone']); ?></td>
                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($teacher['specialization']); ?></span></td>
                        <td><?php echo htmlspecialchars($teacher['education'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="edit_teacher.php?id=<?php echo $teacher['id']; ?>" class="btn btn-sm btn-info text-white"><i class="fa-solid fa-pen"></i></a>
                            <a href="delete_teacher.php?id=<?php echo $teacher['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this teacher?');"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">No teachers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
