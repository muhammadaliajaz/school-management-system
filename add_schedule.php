<?php
require_once 'includes/db.php';
include 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_id = $_POST['course_id'];
    $teacher_id = $_POST['teacher_id'];
    $day_of_week = $_POST['day_of_week'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $room_number = trim($_POST['room_number']);

    // Comprehensive conflict check: Overlapping time for the same Teacher, the same Room, or the same Course
    if (!empty($room_number)) {
        $stmt = $conn->prepare("
            SELECT id FROM schedules 
            WHERE day_of_week = ? 
            AND (teacher_id = ? OR room_number = ? OR course_id = ?)
            AND (start_time < ? AND end_time > ?)
        ");
        $stmt->bind_param("sisiss", $day_of_week, $teacher_id, $room_number, $course_id, $end_time, $start_time);
    } else {
        $stmt = $conn->prepare("
            SELECT id FROM schedules 
            WHERE day_of_week = ? 
            AND (teacher_id = ? OR course_id = ?)
            AND (start_time < ? AND end_time > ?)
        ");
        $stmt->bind_param("siiss", $day_of_week, $teacher_id, $course_id, $end_time, $start_time);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Scheduling conflict detected! The teacher, room, or course is already booked for this time slot.";
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO schedules (course_id, teacher_id, day_of_week, start_time, end_time, room_number) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissss", $course_id, $teacher_id, $day_of_week, $start_time, $end_time, $room_number);
            $stmt->execute();
            
            $_SESSION['success'] = "Schedule created successfully!";
            echo "<script>window.location.href='schedules.php';</script>";
            exit();
        } catch (Exception $e) {
            $error = "Error adding schedule: " . $e->getMessage();
        }
    }
}

// Fetch only allocated teachers and courses to make it logical
$courses = $conn->query("SELECT id, name FROM courses")->fetch_all(MYSQLI_ASSOC);
$teachers = $conn->query("SELECT id, first_name, last_name FROM teachers")->fetch_all(MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Add Class Schedule</h2>
    <a href="schedules.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to Schedule</a>
</div>

<?php if(isset($error)): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="card p-4">
    <form action="" method="POST">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Course *</label>
                <select name="course_id" class="form-select" required>
                    <option value="">-- Select Course --</option>
                    <?php foreach($courses as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Teacher *</label>
                <select name="teacher_id" class="form-select" required>
                    <option value="">-- Select Teacher --</option>
                    <?php foreach($teachers as $t): ?>
                        <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['first_name'] . ' ' . $t['last_name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Day of Week *</label>
                <select name="day_of_week" class="form-select" required>
                    <option value="Monday">Monday</option>
                    <option value="Tuesday">Tuesday</option>
                    <option value="Wednesday">Wednesday</option>
                    <option value="Thursday">Thursday</option>
                    <option value="Friday">Friday</option>
                    <option value="Saturday">Saturday</option>
                    <option value="Sunday">Sunday</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Start Time *</label>
                <input type="time" name="start_time" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">End Time *</label>
                <input type="time" name="end_time" class="form-control" required>
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label">Room Number</label>
            <input type="text" name="room_number" class="form-control" placeholder="e.g. 101A">
        </div>
        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Save Schedule</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
