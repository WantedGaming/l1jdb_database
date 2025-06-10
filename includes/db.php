<?php
// Database configuration
$db_host = 'localhost';
$db_name = 'l1j_remastered';
$db_user = 'root';
$db_pass = '';

// Create MySQLi connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8");

// Create PDO connection for newer code
try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8";
    $pdo = new PDO($dsn, $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("PDO Connection failed: " . $e->getMessage());
}

// Function to safely escape strings
function escape($conn, $str) {
    return $conn->real_escape_string($str);
}

// Function to get single value from query
function getSingleValue($conn, $query) {
    $result = $conn->query($query);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_array();
        return $row[0];
    }
    return null;
}
?>
