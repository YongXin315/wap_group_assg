<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set timezone to Malaysia
date_default_timezone_set('Asia/Kuala_Lumpur');

// Get current date and time in Malaysia timezone
$currentDateTime = date('F j, Y g:i:s A');

require_once 'db.php';

// Get selected date/time from POST or URL parameters or use current time
if (isset($_GET['selected_datetime'])) {
    $selectedDateTime = $_GET['selected_datetime'];
} else {
    $selectedDateTime = date('Y-m-d\TH:i');
}

$selectedDate = $_GET['date'] ?? date('Y-m-d');
$selectedTime = $_GET['time'] ?? date('H:i');

$selectedTimestamp = strtotime($selectedDateTime);
$selectedDayOfWeek = date('l', $selectedTimestamp);
$selectedTimeFormatted = date('H:i', $selectedTimestamp);  // Use this for comparison
?>

<?php include_once 'component/header.php'; ?>

<style>
body, html {
    min-height: 100vh;
    height: 100%;
}
.page-vertical-wrapper {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}
.main-container {
    flex: 1 0 auto;
    background: white;
    overflow: hidden;
    flex-direction: column;
    justify-content: flex-start;
    align-items: flex-start;
    display: flex;
}
.content-wrapper {
    width: 100%;
    padding-left: 160px;
    padding-right: 160px;
    padding-top: 20px;
    padding-bottom: 20px;
    justify-content: center;
    align-items: flex-start;
    display: flex;
}
.content-container {
    flex: 1 1 0;
    max-width: 960px;
    overflow: hidden;
    flex-direction: column;
    justify-content: flex-start;
    align-items: flex-start;
    display: flex;
}
.room-name.clickable {
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.room-name.clickable:hover {
    color: #2196F3;
    text-decoration: underline;
}

.room-card {
    position: relative;
}
</style>

<div class="page-vertical-wrapper">
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
                        <form id="datetimeForm" method="GET">
                            <input type="datetime-local" name="selected_datetime" class="date-picker-input" 
                                   value="<?php echo $selectedDateTime; ?>" 
                                   min="<?php echo date('Y-m-d\TH:i'); ?>">
                        </form>
                    </div>
                </div>

                <!-- Statistics Section -->
                <div class="stats-section" id="statsSection">
                    <?php
                    try {
                        // Calculate room availability based on selected date/time
                        $totalRooms = $db->rooms->countDocuments();
                        
                        // Check for bookings and class timetables at the selected date/time
                        $bookedRooms = 0;
                        $classScheduledRooms = 0;

                        // Check for bookings
                        try {
                            $bookedRoomsQuery = $db->bookings->find([
                                'booking_date' => $selectedDate,  // Use specific date instead
                                'status' => 'approved'
                            ]);

                            $bookedRoomIds = [];
                            foreach ($bookedRoomsQuery as $booking) {
                                $startTime = isset($booking['start_time']) ? $booking['start_time'] : '';
                                $endTime = isset($booking['end_time']) ? $booking['end_time'] : '';
                                
                                if (!empty($startTime) && !empty($endTime)) {
                                    $startTimeFormatted = date('H:i', strtotime($startTime));
                                    $endTimeFormatted = date('H:i', strtotime($endTime));
                                    
                                    // Check if the selected time falls within the booking period
                                    if ($selectedTimeFormatted >= $startTimeFormatted && $selectedTimeFormatted < $endTimeFormatted) {
                                        $bookedRoomIds[] = $booking['room_id'];
                                    }
                                }
                            }
                            $bookedRooms = count(array_unique($bookedRoomIds));
                        } catch (Exception $e) {
                            $bookedRooms = 0;
                        }

                        // Check for class timetables
                        try {
                            $classTimetablesQuery = $db->class_timetable->find([
                                'day_of_week' => $selectedDayOfWeek
                            ]);

                            $classScheduledRoomIds = [];
                            foreach ($classTimetablesQuery as $classSchedule) {
                                $startTime = isset($classSchedule['start_time']) ? $classSchedule['start_time'] : '';
                                $endTime = isset($classSchedule['end_time']) ? $classSchedule['end_time'] : '';
                                
                                if (!empty($startTime) && !empty($endTime)) {
                                    $startTimeFormatted = date('H:i', strtotime($startTime));
                                    $endTimeFormatted = date('H:i', strtotime($endTime));
                                    
                                    // Check if the selected time falls within the class schedule
                                    if ($selectedTimeFormatted >= $startTimeFormatted && $selectedTimeFormatted < $endTimeFormatted) {
                                        $classScheduledRoomIds[] = $classSchedule['room_id'];
                                    }
                                }
                            }
                            $classScheduledRooms = count(array_unique($classScheduledRoomIds));
                        } catch (Exception $e) {
                            $classScheduledRooms = 0;
                        }

                        $availableRooms = $totalRooms - $bookedRooms - $classScheduledRooms;
                        $utilizationRate = $totalRooms > 0 ? round(($bookedRooms + $classScheduledRooms) / $totalRooms * 100) : 0;
                    } catch (Exception $e) {
                        $totalRooms = 20;
                        $availableRooms = 15;
                        $bookedRooms = 5;
                        $classScheduledRooms = 5;
                        $utilizationRate = 25;
                    }
                    ?>
                    <div class="stat-card available">
                        <div class="stat-label">Available Rooms</div>
                        <div class="stat-value"><?php echo $availableRooms; ?></div>
                    </div>
                    <div class="stat-card occupied">
                        <div class="stat-label">Occupied Rooms</div>
                        <div class="stat-value"><?php echo $bookedRooms + $classScheduledRooms; ?></div>
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
                    <div class="section-title-text">Room Status Overview for <?php echo date('l, F j, Y g:i A', strtotime($selectedDateTime)); ?></div>
                </div>

                <!-- Filter Tabs -->
                <div class="filter-tabs">
                    <div class="filter-tab active" data-filter="all">
                        <div class="filter-tab-text">All</div>
                    </div>
                    <div class="filter-tab" data-filter="available">
                        <div class="filter-tab-text">Available</div>
                    </div>
                    <div class="filter-tab" data-filter="occupied">
                        <div class="filter-tab-text">Occupied</div>
                    </div>
                    <div class="filter-tab" data-filter="maintenance">
                        <div class="filter-tab-text">Maintenance</div>
                    </div>
                </div>

                <div id="roomsContainer">
                <?php
                try {
                    // Get all room types from database
                    $roomTypes = $db->rooms->distinct('type');
                    
                    if (empty($roomTypes)) {
                        $roomTypes = ['Discussion Room', 'Lecture Hall', 'Computer Lab', 'Study Room'];
                    }
                    
                    // First pass: Check which room types have available rooms
                    $roomTypesWithAvailableRooms = [];
                    
                    foreach ($roomTypes as $roomType) {
                        // Get rooms of this type
                        $rooms = $db->rooms->find(['type' => $roomType]);
                        $hasAvailableRooms = false;
                        
                        foreach ($rooms as $room) {
                            // Check if room is available (not under maintenance, not booked, not in class)
                            $isAvailable = true;
                            
                            // Check maintenance status
                            if (isset($room['status']) && strtolower($room['status']) === 'under maintenance') {
                                $isAvailable = false;
                            }
                            
                            if ($isAvailable) {
                                // Check for approved bookings
                                try {
                                    $approvedBookings = $db->bookings->find([
                                        'room_id' => $room['_id'],
                                        'booking_date' => $selectedDate,
                                        'status' => 'approved'
                                    ]);
                                    
                                    foreach ($approvedBookings as $booking) {
                                        $startTime = isset($booking['start_time']) ? $booking['start_time'] : '';
                                        $endTime = isset($booking['end_time']) ? $booking['end_time'] : '';
                                        
                                        if (!empty($startTime) && !empty($endTime)) {
                                            $startTimeFormatted = date('H:i', strtotime($startTime));
                                            $endTimeFormatted = date('H:i', strtotime($endTime));
                                            
                                            if ($selectedTimeFormatted >= $startTimeFormatted && $selectedTimeFormatted < $endTimeFormatted) {
                                                $isAvailable = false;
                                                break;
                                            }
                                        }
                                    }
                                } catch (Exception $e) {
                                    // Continue checking
                                }
                                
                                // Check for class schedules
                                if ($isAvailable) {
                                    try {
                                        $classSchedules = $db->class_timetable->find([
                                            'room_name' => $room['room_name'],  // Changed from 'room_id' => $room['_id']
                                            'day_of_week' => $selectedDayOfWeek
                                        ]);
                                        
                                        foreach ($classSchedules as $classSchedule) {
                                            $startTime = isset($classSchedule['start_time']) ? $classSchedule['start_time'] : '';
                                            $endTime = isset($classSchedule['end_time']) ? $classSchedule['end_time'] : '';
                                            
                                            if (!empty($startTime) && !empty($endTime)) {
                                                $startTimeFormatted = date('H:i', strtotime($startTime));
                                                $endTimeFormatted = date('H:i', strtotime($endTime));
                                                
                                                if ($selectedTimeFormatted >= $startTimeFormatted && $selectedTimeFormatted < $endTimeFormatted) {
                                                    $isAvailable = false;
                                                    break;
                                                }
                                            }
                                        }
                                    } catch (Exception $e) {
                                        // Continue checking
                                    }
                                }
                            }
                            
                            if ($isAvailable) {
                                $hasAvailableRooms = true;
                                break; // Found at least one available room for this type
                            }
                        }
                        
                        if ($hasAvailableRooms) {
                            $roomTypesWithAvailableRooms[] = $roomType;
                        }
                    }
                    
                    // Second pass: Display only room types that have available rooms
                    foreach ($roomTypesWithAvailableRooms as $roomType) {
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
                            // Check if the room is under maintenance
                            if (isset($room['status']) && strtolower($room['status']) === 'under maintenance') {
                                $statusClass = 'maintenance';
                                $statusText = 'Under Maintenance';
                                $bookingDetails = 'Not available';
                                $cardClickable = false;
                            } else {
                                // Check if room is available at selected date/time
                                $isAvailable = true;
                                $bookingDetails = 'Available';
                                $statusClass = 'available';
                                $statusText = 'Available';

                                // Query for approved bookings for this specific room and day
                                try {
                                    $approvedBookings = $db->bookings->find([
                                        'room_id' => $room['_id'],
                                        'day_of_week' => $selectedDayOfWeek,
                                        'status' => 'approved'
                                    ]);
                                    
                                    foreach ($approvedBookings as $booking) {
                                        $startTime = isset($booking['start_time']) ? $booking['start_time'] : '';
                                        $endTime = isset($booking['end_time']) ? $booking['end_time'] : '';
                                        
                                        if (!empty($startTime) && !empty($endTime)) {
                                            $startTimeFormatted = date('H:i', strtotime($startTime));
                                            $endTimeFormatted = date('H:i', strtotime($endTime));
                                            
                                            if ($selectedTimeFormatted >= $startTimeFormatted && $selectedTimeFormatted < $endTimeFormatted) {
                                                $isAvailable = false;
                                                $statusClass = 'booked';
                                                $statusText = 'Booked';
                                                $bookingDetails = 'Booked by student: ' . $startTime . ' - ' . $endTime;
                                                break;
                                            }
                                        }
                                    }
                                } catch (Exception $e) {
                                    $isAvailable = true;
                                    $bookingDetails = 'Available';
                                }

                                // Check for pending bookings by current user
                                if ($isAvailable && isset($_SESSION['student_id'])) {
                                    try {
                                        $pendingBookings = $db->bookings->find([
                                            'room_id' => $room['_id'],
                                            'day_of_week' => $selectedDayOfWeek,
                                            'status' => 'pending',
                                            'student_id' => $_SESSION['student_id']
                                        ]);
                                        
                                        foreach ($pendingBookings as $booking) {
                                            $startTime = isset($booking['start_time']) ? $booking['start_time'] : '';
                                            $endTime = isset($booking['end_time']) ? $booking['end_time'] : '';
                                            
                                            if (!empty($startTime) && !empty($endTime)) {
                                                // Remove this buggy line:
                                                // $selectedTimeFormatted = date('H:i', strtotime($selectedTimeSlot));
                                                
                                                // Keep these lines as they are correct:
                                                $startTimeFormatted = date('H:i', strtotime($startTime));
                                                $endTimeFormatted = date('H:i', strtotime($endTime));
                                                
                                                if ($selectedTimeFormatted >= $startTimeFormatted && $selectedTimeFormatted < $endTimeFormatted) {
                                                    $statusClass = 'requested';
                                                    $statusText = 'Requested';
                                                    $bookingDetails = 'Requested: ' . $startTime . ' - ' . $endTime;
                                                    break;
                                                }
                                            }
                                        }
                                    } catch (Exception $e) {
                                        // Do nothing, just skip
                                    }
                                }
                                
                                // Also check class_timetable for scheduled classes
                                try {
                                    $classSchedules = $db->class_timetable->find([
                                        'room_id' => $room['_id'],
                                        'day_of_week' => $selectedDayOfWeek
                                    ]);
                                    foreach ($classSchedules as $classSchedule) {
                                        $startTime = isset($classSchedule['start_time']) ? $classSchedule['start_time'] : '';
                                        $endTime = isset($classSchedule['end_time']) ? $classSchedule['end_time'] : '';
                                        if (!empty($startTime) && !empty($endTime)) {
                                            $startTimeFormatted = date('H:i', strtotime($startTime));
                                            $endTimeFormatted = date('H:i', strtotime($endTime));
                                            if ($selectedTimeFormatted >= $startTimeFormatted && $selectedTimeFormatted < $endTimeFormatted) {
                                                $isAvailable = false;
                                                // Around line 248, change the status class to match filter expectations
                                                $statusClass = 'occupied'; // Instead of 'booked' for consistency
                                                $statusText = 'Occupied';
                                                $bookingDetails = 'Class scheduled: ' . $startTime . ' - ' . $endTime;
                                                break;
                                            }
                                        }
                                    }
                                } catch (Exception $e) {
                                    // Do nothing, just skip
                                }
                                
                                if ($isAvailable && $statusClass === 'available') {
                                    $nextTimes = [];

                                    // Check bookings
                                    $bookings = $db->bookings->find([
                                        'room_id' => $room['_id'],
                                        'day_of_week' => $selectedDayOfWeek,
                                        'status' => 'approved'
                                    ]);
                                    foreach ($bookings as $booking) {
                                        $startTime = isset($booking['start_time']) ? $booking['start_time'] : '';
                                        if (!empty($startTime)) {
                                            $startTimeFormatted = date('H:i', strtotime($startTime));
                                            if ($startTimeFormatted > $selectedTimeFormatted) {
                                                $nextTimes[] = $startTimeFormatted;
                                            }
                                        }
                                    }

                                    // Check class_timetable
                                    $classSchedules = $db->class_timetable->find([
                                        'room_id' => $room['_id'],
                                        'day_of_week' => $selectedDayOfWeek
                                    ]);
                                    foreach ($classSchedules as $classSchedule) {
                                        $startTime = isset($classSchedule['start_time']) ? $classSchedule['start_time'] : '';
                                        if (!empty($startTime)) {
                                            $startTimeFormatted = date('H:i', strtotime($startTime));
                                            if ($startTimeFormatted > $selectedTimeFormatted) {
                                                $nextTimes[] = $startTimeFormatted;
                                            }
                                        }
                                    }

                                    if (!empty($nextTimes)) {
                                        sort($nextTimes);
                                        $bookingDetails = 'Available until ' . $nextTimes[0];
                                    } else {
                                        $bookingDetails = 'Available for the rest of the day';
                                    }
                                }
                                
                                $cardClickable = ($statusClass === 'available' || $statusClass === 'requested');
                            }
                            
                            // Only add onclick if not under maintenance
                            $onclick = ($cardClickable)
                                ? 'onclick="bookRoom(\'' . htmlspecialchars($room['_id']) . '\')"'
                                : '';
                            
                            echo '<div class="room-card ' . $statusClass . '" ' . $onclick . '>';
                            echo '<div class="room-info">';
                            echo '<div class="room-name clickable" onclick="viewRoomDetails(\'' . htmlspecialchars($room['_id']) . '\', event)" title="Click to view room details">' . htmlspecialchars($room['room_name']) . '</div>';
                            echo '<div class="room-status">';
                            echo '<div class="status-' . $statusClass . '">' . $statusText . '</div>';
                            echo '<div class="status-details">' . $bookingDetails . '</div>';
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
                    echo '<div style="background: #ffebee; color: #c62828; padding: 10px; margin: 10px; border-radius: 5px;">';
                    echo '<strong>Database Error:</strong> ' . $e->getMessage();
                        echo '</div>';
                }
                ?>
                </div>
            </div>
        </div>
    </div>
    <?php include_once 'component/footer.php'; ?>
</div>

<script>
    function bookRoom(roomId) {
        var dateInput = document.querySelector('.date-picker-input');
        var selectedDateTime = dateInput ? dateInput.value : '';
        var url = 'booking.php?room_id=' + encodeURIComponent(roomId);

        if (selectedDateTime) {
            var parts = selectedDateTime.split('T');
            if (parts.length === 2) {
                url += '&date=' + encodeURIComponent(parts[0]) + '&time=' + encodeURIComponent(parts[1]);
            }
        } else {
            // fallback to now
            var now = new Date();
            var yyyy = now.getFullYear();
            var mm = String(now.getMonth() + 1).padStart(2, '0');
            var dd = String(now.getDate()).padStart(2, '0');
            var hh = String(now.getHours()).padStart(2, '0');
            var min = String(now.getMinutes()).padStart(2, '0');
            url += '&date=' + yyyy + '-' + mm + '-' + dd + '&time=' + hh + ':' + min;
        }
        window.location.href = url;
    }

    // Date/time picker functionality
    document.querySelector('.date-picker-input').addEventListener('change', function() {
        var selectedDateTime = this.value;
        if (selectedDateTime) {
            var url = new URL(window.location.href);
            url.searchParams.set('selected_datetime', selectedDateTime);
            window.history.replaceState({}, '', url);
        }
        document.getElementById('datetimeForm').submit();
    });

    // Add filter functionality
    document.querySelectorAll('.filter-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            filterRooms(filter);
        });
    });

    // Add search functionality
    document.querySelector('.search-input').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        searchRooms(searchTerm);
    });

    function filterRooms(filter) {
        const roomCards = document.querySelectorAll('.room-card');
        
        roomCards.forEach(card => {
            if (filter === 'all') {
                card.style.display = 'block';
            } else if (filter === 'available' && card.classList.contains('available')) {
                card.style.display = 'block';
            } else if (filter === 'occupied' && (card.classList.contains('booked') || card.classList.contains('occupied'))) {
                card.style.display = 'block';
            } else if (filter === 'maintenance' && card.classList.contains('maintenance')) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function searchRooms(searchTerm) {
        const roomCards = document.querySelectorAll('.room-card');
        
        roomCards.forEach(card => {
            const roomName = card.querySelector('.room-name').textContent.toLowerCase();
            if (roomName.includes(searchTerm)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
    });
    }

    function viewRoomDetails(roomId, event) {
        event.stopPropagation(); // Prevents triggering parent click events if any
        window.location.href = 'roomdetails.php?room_id=' + encodeURIComponent(roomId);
    }

    const initialDate      = '<?php echo $selectedDate; ?>';
    const initialStartTime = '<?php echo $selectedTime; ?>';
    const initialEndTime   = '<?php echo ($_GET['end_time'] ?? '') ; ?>';

    document.addEventListener('DOMContentLoaded', function() {
        initializeCalendar();
        updateTimeOptions();
        updateEndTimeOptions();

        // Restore GET-passed values
        if (initialStartTime) {
            document.getElementById('start_time').value = initialStartTime;
            updateEndTimeOptions();
        }
        if (initialEndTime) {
            document.getElementById('end_time').value = initialEndTime;
        }

        document.getElementById('hidden_booking_date').value = initialDate;
        document.getElementById('hidden_start_time').value   = document.getElementById('start_time').value;
        document.getElementById('hidden_end_time').value     = document.getElementById('end_time').value;

        checkAvailability();
    });

    var bookingDateInput = document.getElementById('booking_date');
    if (bookingDateInput) {
        bookingDateInput.addEventListener('change', function() {
            updateSummaryDate();
            updateCalendar();
            updateTimeOptions();
            updateEndTimeOptions();
            checkAvailability();
        });
    }
</script>

