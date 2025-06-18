<?php
// Include session helper functions
require_once __DIR__ . '/includes/session_helper.php';

// Destroy the admin session
destroyAdminSession();

// Redirect to login page
header('Location: login.php');
exit();
