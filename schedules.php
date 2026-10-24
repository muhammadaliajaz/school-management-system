<?php
require_once 'includes/db.php';
include 'includes/header.php';

// Handle deletion
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM schedules WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $_SESSION['success'] = "Schedule removed successfully!";
    } catch (Exception $e) {
        $_SESSION['error'] = "Error removing schedule.";
    }
    echo "<script>window.location.href='schedules.php';</script>";
    exit();
}

$result = $conn->query("
    SELECT s.id, c.name as course_name, t.first_name, t.last_name, s.day_of_week, s.start_time, s.end_time, s.room_number 
    FROM schedules s
    JOIN courses c ON s.course_id = c.id
    JOIN teachers t ON s.teacher_id = t.id
    ORDER BY FIELD(s.day_of_week, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'), s.start_time
");
$schedules = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Class Time Schedule</h2>
    <a href="add_schedule.php" class="btn btn-primary"><i class="fa-solid fa-calendar-plus me-1"></i> Add Schedule</a>
</div>



<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Day</th>
                    <th>Time</th>
                    <th>Course</th>
                    <th>Teacher</th>
                    <th>Room</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($schedules) > 0): ?>
                    <?php 
                    $current_day = '';
                    foreach($schedules as $s): 
                        $time_formatted = date('h:i A', strtotime($s['start_time'])) . ' - ' . date('h:i A', strtotime($s['end_time']));
                        
                        // Formatting day changes
                        if ($s['day_of_week'] != $current_day) {
                            $current_day = $s['day_of_week'];
                            echo "<tr class='table-primary'><td colspan='6' class='fw-bold'>{$current_day}</td></tr>";
                        }
                    ?>
                    <tr>
                        <td><span class="badge bg-secondary"><?php echo $s['day_of_week']; ?></span></td>
                        <td class="fw-bold"><?php echo $time_formatted; ?></td>
                        <td><?php echo htmlspecialchars($s['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($s['first_name'] . ' ' . $s['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($s['room_number']); ?></td>
                        <td>
                            <a href="?delete_id=<?php echo $s['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this schedule?');"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" class="text-center text-muted">No schedules found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
