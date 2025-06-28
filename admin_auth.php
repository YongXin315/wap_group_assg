<?php
session_start();

// Inactivity timeout (1 day for admin)
$timeout = 86400;

// If admin not logged in, redirect to login
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// Check inactivity
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    // Redirect with timeout flag
    header("Location: index.php?timeout=1");
    exit;
}
// Update last activity
$_SESSION['last_activity'] = time();
?>