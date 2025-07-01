<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['student_id']) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Get room types for filter
$roomTypes = [];
try {
    if (isset($db)) {
        $roomTypes = $db->rooms->distinct('type');
    }
} catch (Exception $e) {
    // Handle error
    echo "<script>console.error('Error fetching room types: " . addslashes($e->getMessage()) . "');</script>";
}

$isLoggedIn = isLoggedIn();
$isAdmin = isAdmin();
$userName = '';
if ($isAdmin && isset($_SESSION['admin_name'])) {
    $userName = $_SESSION['admin_name'];
} elseif ($isLoggedIn && isset($_SESSION['student_name'])) {
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
                <img src="images/Taylors_Logo.png" alt="Taylor's Logo">
                <div class="logo-text">Taylor's Room Booking System</div>
            </div>
            <ul class="nav-menu">
                <li><a href="index.php">Home</a></li>
                <li><a href="roomavailability.php">Room Availability</a></li>
                <?php if ($isAdmin): ?>
                    <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
                    <li><a href="manage_bookings.php">Manage Bookings</a></li>
                    <li><a href="#" class="btn-logout" onclick="confirmLogout()">Logout</a></li>
                    <li class="welcome">Welcome, <?php echo htmlspecialchars($userName); ?> (Admin)</li>
                <?php elseif ($isLoggedIn): ?>
                    <li><a href="mybookings.php">My Bookings</a></li>
                    <li><a href="#" class="btn-logout" onclick="confirmLogout()">Logout</a></li>
                    <li class="welcome">Welcome, <?php echo htmlspecialchars($userName); ?></li>
                <?php else: ?>
                    <li><a href="signup.php">Sign Up</a></li>
                    <li><a href="login.php" class="btn-login">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <script>
    function confirmLogout() {
        if (confirm('Are you sure you want to logout?')) {
            window.location.href = 'logout.php';
        }
    }
    </script>
</body>
</html>