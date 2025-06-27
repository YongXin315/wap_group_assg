<?php
session_start();

// inactivity timeout (1 day for admin)
$timeout = 86400;

// if admin not logged in, redirect to login
if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}

// check inactivity
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