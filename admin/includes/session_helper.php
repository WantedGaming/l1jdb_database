<?php
/**
 * Session Helper Functions
 * Provides safe session management for the admin area
 */

/**
 * Safely start a session if not already started
 * @return bool True if session was started or already active
 */
function ensureSessionStarted() {
    if (session_status() === PHP_SESSION_NONE) {
        return session_start();
    }
    return true;
}

/**
 * Check if user is authenticated as admin
 * @return bool True if user is logged in as admin
 */
function isAdminLoggedIn() {
    ensureSessionStarted();
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Check if session has timed out (30 minutes default)
 * @param int $timeout Session timeout in seconds (default: 1800 = 30 minutes)
 * @return bool True if session has timed out
 */
function isSessionTimedOut($timeout = 1800) {
    ensureSessionStarted();
    return isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout);
}

/**
 * Update session activity timestamp
 */
function updateSessionActivity() {
    ensureSessionStarted();
    $_SESSION['last_activity'] = time();
}

/**
 * Safely destroy session and cleanup
 */
function destroyAdminSession() {
    ensureSessionStarted();
    
    // Clear session variables
    $_SESSION = array();
    
    // Delete session cookie if it exists
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
}

/**
 * Redirect to login page with optional message
 * @param string $message Optional message parameter
 */
function redirectToLogin($message = '') {
    $url = '/l1jdb_database/admin/login.php';
    if (!empty($message)) {
        $url .= '?' . http_build_query(['msg' => $message]);
    }
    header('Location: ' . $url);
    exit();
}

/**
 * Set admin login session
 * @param string $username Admin username
 */
function setAdminLogin($username) {
    ensureSessionStarted();
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = $username;
    $_SESSION['login_time'] = time();
    updateSessionActivity();
}
