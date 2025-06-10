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
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: var(--background);
        }

        .login-container {
            background-color: var(--primary);
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 400px;
            border: 1px solid var(--secondary);
        }

        .login-form h2 {
            text-align: center;
            color: var(--accent);
            margin-bottom: 30px;
            font-size: 1.8rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text);
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            background-color: var(--secondary);
            border: 1px solid var(--primary);
            border-radius: 4px;
            color: var(--text);
            font-size: 16px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(253, 127, 68, 0.2);
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: var(--accent);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .login-btn:hover {
            background-color: #e06b3c;
            transform: translateY(-1px);
        }

        .error-message {
            background-color: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            border: 1px solid #e74c3c;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }

        .info-message {
            background-color: rgba(52, 152, 219, 0.2);
            color: #3498db;
            border: 1px solid #3498db;
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }

        .demo-info {
            margin-top: 20px;
            padding: 15px;
            background-color: var(--secondary);
            border-radius: 4px;
            text-align: center;
            color: var(--text);
            font-size: 14px;
            opacity: 0.8;
        }

        .demo-info strong {
            color: var(--accent);
        }

        .back-to-site {
            text-align: center;
            margin-top: 20px;
        }

        .back-to-site a {
            color: var(--accent);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .back-to-site a:hover {
            opacity: 0.8;
            text-decoration: underline;
        }
    </style>
</head>
<body>
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
