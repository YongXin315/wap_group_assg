<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timeout duration in seconds (e.g., 7 days = 604800 seconds)
$timeout = 604800;

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../login.php');
    exit();
}

// Check for session timeout
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout) {
    session_unset();     // Unset session variables
    session_destroy();   // Destroy the session
    header('Location: ../login.php');
    exit();
}

// Update last activity time stamp
$_SESSION['LAST_ACTIVITY'] = time();
?>
