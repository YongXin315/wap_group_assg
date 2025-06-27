<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'db.php';

// Get current date and time
$currentDateTime = date('F j, Y g:i:s A');
?>

<?php include_once 'component/header.php'; ?>

<div class="main-container">
    <div class="content-wrapper">
        <div class="content-container">
            <!-- Header Section -->
            <div class="header-section">
                <div class="header-left">
                    <div class="title">Taylor's Room Booking System</div>
                    <div class="datetime"><?php echo $currentDateTime; ?></div>
                </div>
                <div class="date-picker-section">
                    <div class="date-picker-label">
                        <div class="date-picker-label-text">When</div>
                    </div>
                    <input type="datetime-local" class="date-picker-input" value="<?php echo date('Y-m-d\TH:i'); ?>">
                </div>
            </div>

            <!-- Statistics Section -->
            <div class="stats-section">
                <?php
                try {
                    $totalRooms = $db->rooms->countDocuments();
                    $availableRooms = $db->rooms->countDocuments(['class_timetable' => []]);
                    $occupiedRooms = $totalRooms - $availableRooms;
                    $utilizationRate = $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100) : 0;
                } catch (Exception $e) {
                    $totalRooms = 20;
                    $availableRooms = 15;
                    $occupiedRooms = 5;
                    $utilizationRate = 25;
                }
                ?>
                <div class="stat-card available">
                    <div class="stat-label">Available Rooms</div>
                    <div class="stat-value"><?php echo $availableRooms; ?></div>
                </div>
                <div class="stat-card occupied">
                    <div class="stat-label">Occupied Rooms</div>
                    <div class="stat-value"><?php echo $occupiedRooms; ?></div>
                </div>
                <div class="stat-card total">
                    <div class="stat-label">Total Rooms</div>
                    <div class="stat-value"><?php echo $totalRooms; ?></div>
                </div>
                <div class="stat-card utilization">
                    <div class="stat-label">Utilization Rate</div>
                    <div class="stat-value"><?php echo $utilizationRate; ?>%</div>
                </div>
            </div>

            <!-- Room Status Overview -->
            <div class="section-title">
                <div class="section-title-text">Room Status Overview</div>
            </div>

            <!-- Filter Tabs -->
            <div class="filter-tabs">
                <div class="filter-tab active">
                    <div class="filter-tab-text">All</div>
                </div>
                <div class="filter-tab">
                    <div class="filter-tab-text">Available</div>
                </div>
                <div class="filter-tab">
                    <div class="filter-tab-text">Occupied</div>
                </div>
                <div class="filter-tab">
                    <div class="filter-tab-text">Maintenance</div>
                </div>
            </div>

            <!-- Search Section -->
            <div class="search-section">
                <div class="search-container">
                    <div class="search-box">
                        <div class="search-icon">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M19.5306 18.4694L14.8366 13.7762C17.6629 10.383 17.3204 5.36693 14.0591 2.38935C10.7978 -0.588237 5.77134 -0.474001 2.64867 2.64867C-0.474001 5.77134 -0.588237 10.7978 2.38935 14.0591C5.36693 17.3204 10.383 17.6629 13.7762 14.8366L18.4694 19.5306C18.7624 19.8237 19.2376 19.8237 19.5306 19.5306C19.8237 19.2376 19.8237 18.7624 19.5306 18.4694ZM1.75 8.5C1.75 4.77208 4.77208 1.75 8.5 1.75C12.2279 1.75 15.25 4.77208 15.25 8.5C15.25 12.2279 12.2279 15.25 8.5 15.25C4.77379 15.2459 1.75413 12.2262 1.75 8.5Z" fill="#876363"/>
                            </svg>
                        </div>
                        <input type="text" class="search-input" placeholder="Search by room code or name">
                    </div>
                </div>
            </div>

            <?php
            try {
                // Get all room types from database
                $roomTypes = $db->rooms->distinct('type');
                
                if (empty($roomTypes)) {
                    // Fallback room types if database is empty
                    $roomTypes = ['Discussion Room', 'Lecture Hall', 'Computer Lab', 'Study Room'];
                }
                
                // Display rooms by type
                foreach ($roomTypes as $roomType) {
                    echo '<div class="room-type-filter">';
                    echo '<div class="room-type-tab">';
                    echo '<div class="room-type-text">' . htmlspecialchars($roomType) . '</div>';
                    echo '</div>';
                    echo '</div>';

                    echo '<div class="rooms-section">';
                    echo '<div class="rooms-container">';
                    echo '<div class="rooms-row">';
                    
                    // Get rooms of this type
                    $rooms = $db->rooms->find(['type' => $roomType]);
                    $roomCount = 0;
                    
                    foreach ($rooms as $room) {
                        $isAvailable = empty($room['class_timetable']);
                        $statusClass = $isAvailable ? 'available' : 'occupied';
                        $statusText = $isAvailable ? 'Available' : 'Occupied';
                        $details = $isAvailable ? 'No Upcoming Bookings' : 'Current Session: 10:00 AM - 12:00 PM';
                        
                        // Use room_id if it exists, otherwise use room_name as fallback
                        $roomId = isset($room['room_id']) ? $room['room_id'] : (isset($room['room_name']) ? $room['room_name'] : 'unknown');
                        
                        echo '<div class="room-card ' . $statusClass . '" onclick="bookRoom(\'' . htmlspecialchars($roomId) . '\')">';
                        echo '<div class="room-info">';
                        echo '<div class="room-name">' . htmlspecialchars($room['room_name']) . '</div>';
                        echo '<div class="room-status">';
                        echo '<div class="status-' . ($isAvailable ? 'available' : 'occupied') . '">' . $statusText . '</div>';
                        echo '<div class="status-details">' . $details . '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                        
                        $roomCount++;
                    }
                    
                    // If no rooms found for this type, show a message
                    if ($roomCount == 0) {
                        echo '<div class="no-rooms-message">No ' . htmlspecialchars($roomType) . ' rooms found.</div>';
                    }
                    
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
                
            } catch (Exception $e) {
                // Fallback to static data if database error
                $roomTypes = ['Discussion Room', 'Lecture Hall', 'Computer Lab', 'Study Room'];
                
                foreach ($roomTypes as $roomType) {
                    echo '<div class="room-type-filter">';
                    echo '<div class="room-type-tab">';
                    echo '<div class="room-type-text">' . htmlspecialchars($roomType) . '</div>';
                    echo '</div>';
                    echo '</div>';

                    echo '<div class="rooms-section">';
                    echo '<div class="rooms-container">';
                    echo '<div class="rooms-row">';
                    
                    // Generate some sample rooms for each type
                    $sampleRooms = [
                        ['name' => $roomType . ' 1', 'status' => 'available', 'details' => 'No Upcoming Bookings'],
                        ['name' => $roomType . ' 2', 'status' => 'occupied', 'details' => 'Current Session: 10:00 AM - 12:00 PM'],
                        ['name' => $roomType . ' 3', 'status' => 'available', 'details' => 'Next Upcoming Bookings: 2:00 PM - 4:00 PM']
                    ];
                    
                    foreach ($sampleRooms as $room) {
                        echo '<div class="room-card ' . $room['status'] . '">';
                        echo '<div class="room-info">';
                        echo '<div class="room-name">' . $room['name'] . '</div>';
                        echo '<div class="room-status">';
                        echo '<div class="status-' . $room['status'] . '">' . ucfirst($room['status']) . '</div>';
                        echo '<div class="status-details">' . $room['details'] . '</div>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                    
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            }
            ?>
        </div>
    </div>
</div>

<script>
    function bookRoom(roomId) {
        alert('Booking functionality for room ' + roomId + ' will be implemented soon!');
    }

    // Add filter functionality
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            // Add filter logic here
        });
    });

    // Add search functionality
    document.querySelector('.search-input').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        // Add search logic here
    });
</script>

<?php include_once 'component/footer.php'; ?> 