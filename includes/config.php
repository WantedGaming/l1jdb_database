<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'l1j_remastered');
define('DB_USER', 'root');
define('DB_PASS', '');

// PDO connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Site configuration
define('SITE_NAME', 'L1J Database');
define('SITE_URL', 'http://localhost/l1jdb_database');
define('SITE_ROOT', $_SERVER['DOCUMENT_ROOT'] . '/l1jdb_database');

// Path helper functions
function getAbsolutePath($relativePath) {
    return SITE_ROOT . '/' . ltrim($relativePath, '/');
}

function getSiteUrl($path = '') {
    return SITE_URL . '/' . ltrim($path, '/');
}
?>
