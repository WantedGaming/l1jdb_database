<?php
session_start();

// Simple admin login for bin tables access
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Simple hardcoded admin credentials - change these in production!
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['last_activity'] = time();
        
        header('Location: index.php');
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}

// If already logged in, redirect
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - L1J Database</title>
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico">
    <!-- Include main style.css for CSS variables -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <!-- Include admin-specific styles -->
    <link rel="stylesheet" href="../assets/css/admin.css">

</head>
<body class="login-page">
    <div class="login-container">
        <form method="POST" class="login-form">
            <h2>L1J Database Admin</h2>
            
            <?php if (isset($_GET['timeout']) && $_GET['timeout'] == 1): ?>
                <div class="info-message">
                    Session expired. Please login again.
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="login-btn">Login</button>
            
            <div class="demo-info">
                <strong>Demo Credentials:</strong><br>
                Username: admin | Password: admin123
            </div>
            
            <div class="back-to-site">
                <a href="../">← Back to Website</a>
            </div>
        </form>
    </div>
</body>
</html>
