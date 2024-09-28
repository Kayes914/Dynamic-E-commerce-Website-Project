<?php
include 'includes/connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_input = $_POST['login_input'];  // This could be either the email or username
    $password = $_POST['password'];

    // Prepare SQL statement (check if the input is either a username or an email)
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$login_input, $login_input]);
    $user = $stmt->fetch();

    // Verify password
    if ($user && password_verify($password, $user['password'])) {
        // Store user information in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // Redirect based on the user's role
        if ($user['role'] === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: index.php");
        }
        exit();  // Make sure to stop further execution after redirect
    } else {
        $error_message = "Invalid username/email or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Finch Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<div class="login-container">
    <h2>Login</h2>
    
    <!-- Display error messages, if any -->
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($error_message) ?>
        </div>
    <?php endif; ?>

    <!-- Login Form -->
    <form method="POST" action="login.php">
        <div class="mb-3">
            <label for="login_input" class="form-label">Username or Email</label>
            <input type="text" class="form-control" id="login_input" name="login_input" placeholder="Enter your username or email" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
