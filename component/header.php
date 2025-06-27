<?php
session_start();
require_once dirname(__DIR__) . '/db.php'; // Use relative path to parent directory

function isLoggedIn() {
    return isset($_SESSION['student_id']);
}

// Get room types for filter
$roomTypes = [];
try {
    $roomTypes = $db->rooms->distinct('type');
} catch (Exception $e) {
    // Handle error
    echo "<script>console.error('Error fetching room types: " . addslashes($e->getMessage()) . "');</script>";
}

$isLoggedIn = isLoggedIn();
$userName = '';
if ($isLoggedIn && isset($_SESSION['student_name'])) {
    $userName = $_SESSION['student_name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taylor's University - Room Booking System</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?php include_once dirname(__DIR__) . '/assests/styles.php'; ?>
</head>
<body>
    <header class="header">
        <nav class="nav-container">
            <div class="logo">
                <div class="logo-icon">T</div>
                <div class="logo-text">Taylor's Room Booking System</div>
            </div>
            <ul class="nav-menu">
                <li><a href="#home">Home</a></li>
                <li><a href="#rooms">Room Availability</a></li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="my_bookings.php">My Bookings</a></li>
                    <li><a href="logout.php" class="btn-logout">Logout</a></li>
                    <li class="welcome">Welcome, <?php echo htmlspecialchars($userName); ?></li>
                <?php else: ?>
                    <li><a href="signup.php">Sign Up</a></li>
                    <li><a href="index.php" class="btn-login">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>