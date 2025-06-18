<?php
// Include session helper functions
require_once __DIR__ . '/includes/session_helper.php';

// Ensure session is started
ensureSessionStarted();

// Simple admin login for bin tables access
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Simple hardcoded admin credentials - change these in production!
    if ($username === 'admin' && $password === 'admin123') {
        setAdminLogin($username);
        header('Location: index.php');
        exit();
    } else {
        $error = 'Invalid username or password';
    }
}

// If already logged in, redirect
if (isAdminLoggedIn()) {
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
    <!-- Include base styles with CSS variables -->
    <link rel="stylesheet" href="../assets/css/base.css">
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
