<?php
// Include session helper functions
require_once __DIR__ . '/session_helper.php';

// Ensure session is started
ensureSessionStarted();

// Check if user is logged in
if (!isAdminLoggedIn()) {
    redirectToLogin();
}

// Check for session timeout (30 minutes)
if (isSessionTimedOut(1800)) {
    destroyAdminSession();
    redirectToLogin('timeout=1');
}

// Update last activity time
updateSessionActivity();
