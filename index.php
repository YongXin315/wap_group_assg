<?php
session_start();
require 'db.php'; // This now connects to MongoDB

// Remove this function as it's already defined in header.php
// function isLoggedIn() {
//     return isset($_SESSION['student_id']);
// }

// Include the header component which handles session, DB connection, and HTML head
require_once 'component/header.php';

// Get rooms from MongoDB
$rooms = [];
try {
    $roomsCollection = $db->rooms;
    $cursor = $roomsCollection->find();
    foreach ($cursor as $room) {
        $rooms[] = $room;
    }
} catch (Exception $e) {
    // Handle error
    echo "<script>console.error('Error fetching rooms: " . addslashes($e->getMessage()) . "');</script>";
}
?>

<section id="home" class="hero">
    <div class="hero-content">
        <h1>Book Study & Meeting Rooms at Taylor's</h1>
        <p>Find and easily book reservations for all students</p>
        <a href="#search" class="cta-button">Get Started</a>
    </div>
</section>

<section id="search" class="search-section">
    <div class="container">
        <h2>Find Available Rooms</h2>
        <form class="search-form" method="GET" action="#rooms">
            <div class="form-group">
                <label for="room-type">Room</label>
                <select id="room-type" name="room_type">
                    <option value="">Select a Room</option>
                    <?php foreach ($roomTypes as $type): ?>
                        <option value="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="date-time">When</label>
                <input type="datetime-local" id="date-time" name="datetime">
            </div>
            <button type="submit" class="search-button">
                <i class="fas fa-search"></i> Search Available Rooms
            </button>
        </form>
    </div>
</section>

<section id="rooms" class="rooms-section">
    <div class="container">
        <h2>Available Rooms</h2>
        <div class="rooms-grid">
            <?php foreach ($rooms as $room):
                $icon = 'fas fa-door-open';
                if (isset($room['type'])) {
                    switch ($room['type']) {
                        case 'Computer Lab': $icon = 'fas fa-desktop'; break;
                        case 'Classroom': $icon = 'fas fa-chalkboard-teacher'; break;
                        case 'Discussion Room': $icon = 'fas fa-users'; break;
                        case 'Lecture Theatre': $icon = 'fas fa-theater-masks'; break;
                    }
                }
            ?>
            <div class="room-card" data-room-type="<?php echo isset($room['type']) ? htmlspecialchars($room['type']) : ''; ?>">
                <div class="room-image">
                    <i class="<?php echo $icon; ?>"></i>
                </div>
                <div class="room-content">
                    <h3 class="room-title"><?php echo isset($room['room_name']) ? htmlspecialchars($room['room_name']) : ''; ?></h3>
                    <div class="room-details">
                        Block <?php echo isset($room['block']) ? htmlspecialchars($room['block']) : ''; ?>, 
                        Floor <?php echo isset($room['floor']) ? htmlspecialchars($room['floor']) : ''; ?><br>
                        Capacity: <?php echo isset($room['min_occupancy']) ? $room['min_occupancy'] : '0'; ?>-<?php echo isset($room['max_occupancy']) ? $room['max_occupancy'] : '0'; ?> people<br>
                        <small><?php echo isset($room['amenities']) ? htmlspecialchars($room['amenities']) : ''; ?></small>
                    </div>
                    <div class="room-availability">Available for booking</div>
                    <?php if ($isLoggedIn): ?>
                        <button class="book-button" onclick="bookRoom('<?php echo isset($room['room_id']) ? htmlspecialchars($room['room_id']) : ''; ?>')">Book This Room</button>
                    <?php else: ?>
                        <button class="book-button" onclick="alert('Please log in to book a room.'); window.location.href='index.php';">Login to Book</button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php
// Include the footer component which contains the footer HTML and JavaScript
require_once 'component/footer.php';
?>