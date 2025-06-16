<?php
// Admin panel for L1J Database Management
session_start();
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Database connection
function getDbConnection() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "l1j_remastered";
    
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}

// Function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Function to validate numeric input
function validateNumeric($input, $fieldName, $min = null, $max = null) {
    if (!is_numeric($input)) {
        return "$fieldName must be a number.";
    }
    
    if ($min !== null && $input < $min) {
        return "$fieldName must be at least $min.";
    }
    
    if ($max !== null && $input > $max) {
        return "$fieldName must not exceed $max.";
    }
    
    return null; // No error
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>L1J Database Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="admin-content-wrapper mt-4">
        <?php
        // Include the appropriate page based on the 'page' parameter
        switch ($page) {
            case 'spawn':
                include 'pages/spawn.php';
                break;
            case 'drops':
                include 'pages/drops.php';
                break;
            case 'monsters':
                include 'pages/monsters.php';
                break;
            case 'beginner':
                include 'pages/beginner.php';
                break;
            default:
                include 'pages/home.php';
                break;
        }
        ?>
    </div>

    <footer class="admin-footer mt-auto py-3">
        <div class="admin-footer-container text-center">
            <span>L1J Database Admin Panel &copy; <?php echo date('Y'); ?></span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="scripts.js"></script>
</body>
</html>
