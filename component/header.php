<?php
session_start();

require_once 'functions.php';

// Check if user is logged in
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
    <link href="style.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        /* Header Styles */
        .header {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0.5rem; 
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: 1.5rem; /* Increased left margin to move logo more left */
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
            margin-right: 1.5rem; /* Increased right margin to move nav more right */
            margin-left: auto; /* Pushes the nav menu to the right */
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: #c3272b;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .logo-text {
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .nav-menu {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
        }

        .nav-menu a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        .nav-menu a:hover {
            color: #c3272b;
        }

        .btn-login, .btn-logout {
            background: #c3272b;
            color: white !important;
            padding: 8px 16px;
            border-radius: 4px;
            text-decoration: none;
        }

        .welcome {
            color: #666;
            font-size: 14px;
        }

        /* Common Styles */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .section-padding {
            padding: 4rem 0;
        }

        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            font-size: 2.5rem;
            color: #333;
        }

        .btn-primary {
            background: #c3272b;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary:hover {
            background: #c3272b;
        }

        /* Content area needs margin-top to account for fixed header */
        .main-content {
            margin-top: 70px;
        }
    <!-- Initialize Flatpickr early to prevent flash of default date picker -->


    <?php initializeFlatpickr(); ?>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="nav-container">
            <div class="logo">
                <img src="images/Taylors_Logo.png" alt="Taylor's Logo">
                <div class="logo-text">Taylor's Room Booking System</div>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="landingpage.php#rooms">Room Availability</a></li>
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

    <!-- Main Content Container -->
    <div class="main-content">