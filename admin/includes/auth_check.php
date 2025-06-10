<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: /l1jdb_database/admin/login.php');
    exit();
}

// Optional: Check for session timeout (30 minutes)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header('Location: /l1jdb_database/admin/login.php?timeout=1');
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();
