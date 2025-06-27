<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Availability - Taylor's Room Booking System</title>
    <?php include_once 'assests/styles.php'; ?>
</head>
<body>
    <?php include_once 'component/header.php'; ?>
    
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['student_name']); ?>!</h1>
        <h2>Room Availability</h2>
        
        <div class="room-grid">
            <?php
            try {
                // Fetch all rooms from the database
                $rooms = $db->rooms->find();
                
                foreach ($rooms as $room) {
                    echo '<div class="room-card">';
                    echo '<h3>' . htmlspecialchars($room['room_name']) . '</h3>';
                    echo '<p><strong>Room ID:</strong> ' . htmlspecialchars($room['room_id']) . '</p>';
                    echo '<p><strong>Block:</strong> ' . htmlspecialchars($room['block']) . '</p>';
                    echo '<p><strong>Floor:</strong> ' . htmlspecialchars($room['floor']) . '</p>';
                    echo '<p><strong>Type:</strong> ' . htmlspecialchars($room['type']) . '</p>';
                    echo '<p><strong>Capacity:</strong> ' . $room['min_occupancy'] . '-' . $room['max_occupancy'] . ' people</p>';
                    echo '<p><strong>Amenities:</strong> ' . htmlspecialchars($room['amenities']) . '</p>';
                    
                    // Check if room has scheduled classes
                    if (isset($room['class_timetable']) && !empty($room['class_timetable'])) {
                        echo '<p><strong>Status:</strong> <span style="color: orange;">Has scheduled classes</span></p>';
                    } else {
                        echo '<p><strong>Status:</strong> <span style="color: green;">Available for booking</span></p>';
                    }
                    
                    echo '<button class="book-button" onclick="bookRoom(\'' . $room['room_id'] . '\')">Book This Room</button>';
                    echo '</div>';
                }
            } catch (Exception $e) {
                echo '<p style="color: red;">Error loading rooms: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }
            ?>
        </div>
        
        <div class="logout-section">
            <a href="logout.php" class="logout-button">Logout</a>
        </div>
    </div>

    <script>
        function bookRoom(roomId) {
            alert('Booking functionality for room ' + roomId + ' will be implemented soon!');
        }
    </script>

    <?php include_once 'component/footer.php'; ?>
</body>
</html> 