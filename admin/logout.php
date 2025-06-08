<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Log admin activity
if (isset($_SESSION['user_id'])) {
    logAdminActivity('LOGOUT', 'accounts', $_SESSION['user_id'], 'User logged out');
}

// Destroy session
session_destroy();

// Redirect to home
header('Location: ' . SITE_URL);
exit;
?>
