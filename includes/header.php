<?php
require_once 'includes/db.php';
if (basename($_SERVER['PHP_SELF']) != 'index.php' && basename($_SERVER['PHP_SELF']) != 'login.php') {
    require_login();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
    <!-- Animate CSS for Animations -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>
<body>

<?php if (isset($_SESSION['admin_id'])): ?>
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php"><i class="fa-solid fa-school me-2"></i> SMS</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="students.php">Students</a></li>
        <li class="nav-item"><a class="nav-link" href="teachers.php">Teachers</a></li>
        <li class="nav-item"><a class="nav-link" href="courses.php">Courses</a></li>
        <li class="nav-item"><a class="nav-link" href="enroll_student.php">Student Enrollments</a></li>
        <li class="nav-item"><a class="nav-link" href="assign_teacher.php">Teacher Allocations</a></li>
        <li class="nav-item"><a class="nav-link" href="fees.php">Fees</a></li>
        <li class="nav-item"><a class="nav-link" href="schedules.php">Schedules</a></li>
        <li class="nav-item ms-lg-3"><a class="btn btn-danger text-white px-3 mt-1" href="logout.php">Logout</a></li>
      </ul>
    </div>
  </div>
</nav>
<?php endif; ?>

<div class="container my-5 animate__animated animate__fadeInUp">

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
        <i class="fa-solid fa-circle-check me-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
        <i class="fa-solid fa-circle-exclamation me-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
