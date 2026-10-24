<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/db.php';

if (isset($_SESSION['admin_id'])) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "Please fill in all fields.";
        header("Location: signup.php");
        exit();
    } elseif ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: signup.php");
        exit();
    } else {
        // Check if username already exists
        try {
            $stmt = $conn->prepare("SELECT id FROM admin WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $_SESSION['error'] = "Username already exists. Please choose another.";
                header("Location: signup.php");
                exit();
            } else {
                // Insert new admin
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
                $stmt->bind_param("ss", $username, $hashed_password);
                
                if ($stmt->execute()) {
                    $_SESSION['success'] = "Account created successfully. Please login.";
                    header("Location: index.php");
                    exit();
                } else {
                    $_SESSION['error'] = "Failed to create account. Please try again.";
                    header("Location: signup.php");
                    exit();
                }
            }
        } catch (Exception $e) {
            $_SESSION['error'] = "Database error: " . $e->getMessage();
            header("Location: signup.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign Up - School Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }


        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            position: relative;
            z-index: 10;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }

        .login-icon {
            font-size: 3.5rem;
            background: linear-gradient(135deg, #6366f1, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 15px;
        }

    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="card login-card text-center">
        <i class="fa-solid fa-user-plus login-icon"></i>
        <h3 class="fw-bold mb-1" style="color: #1e293b;">Create Account</h3>
        <p class="text-muted mb-4">Sign up as a new admin</p>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger py-2 rounded-3 shadow-sm" style="font-size: 0.9rem;">
                <i class="fa-solid fa-circle-exclamation me-1"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="signup.php" method="POST" class="text-start">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent"><i class="fa-solid fa-user text-muted"></i></span>
                    <input type="text" name="username" class="form-control" required autofocus placeholder="admin">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control" required placeholder="********">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Confirm Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" name="confirm_password" class="form-control" required placeholder="********">
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold text-uppercase tracking-wider">Create Account</button>
        </form>
        
        <div class="mt-4">
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Already have an account? <a href="index.php" class="text-primary text-decoration-none fw-bold">Sign In</a></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>
