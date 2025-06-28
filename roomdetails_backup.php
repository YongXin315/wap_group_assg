<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'db.php';

// Get room ID from URL parameter
$roomId = $_GET['room_id'] ?? '';

if (empty($roomId)) {
    header('Location: roomavailability.php');
    exit();
}

// Get room details from database
try {
    $room = $db->rooms->findOne(['_id' => $roomId]);
    if (!$room) {
        // Fallback to static data if room not found
        $room = [
            'room_name' => $roomId,
            'type' => 'Computer Lab',
            'block' => 'Block D',
            'floor' => 'Level 7.14',
            'capacity' => '21-30 persons',
            'amenities' => 'Whiteboard, TV screen, Air-conditioning, Computers',
            'class_timetable' => []
        ];
    }
} catch (Exception $e) {
    // Fallback to static data if database error
    $room = [
        'room_name' => $roomId,
        'type' => 'Computer Lab',
        'block' => 'Block D',
        'floor' => 'Level 7.14',
        'capacity' => '21-30 persons',
        'amenities' => 'Whiteboard, TV screen, Air-conditioning, Computers',
        'class_timetable' => []
    ];
}

// Get current date and time
$currentDateTime = date('F j, Y g:i:s A');
$currentDate = date('Y-m-d');
$currentMonth = date('F Y');

// Get selected date from URL parameter or use today
$selectedDate = $_GET['date'] ?? $currentDate;

// Generate time slots for today's schedule
$timeSlots = [
    '8:00 AM - 9:00 AM', '9:00 AM - 10:00 AM', '10:00 AM - 11:00 AM', '11:00 AM - 12:00 PM',
    '12:00 PM - 1:00 PM', '1:00 PM - 2:00 PM', '2:00 PM - 3:00 PM', '3:00 PM - 4:00 PM',
    '4:00 PM - 5:00 PM', '5:00 PM - 6:00 PM', '6:00 PM - 7:00 PM', '7:00 PM - 8:00 PM'
];

// Generate evening time slots
$eveningTimeSlots = [
    '8:00 PM - 9:00 PM', '9:00 PM - 10:00 PM', '10:00 PM - 11:00 PM', '11:00 PM - 12:00 AM',
    '12:00 AM - 1:00 AM', '1:00 AM - 2:00 AM', '2:00 AM - 3:00 AM', '3:00 AM - 4:00 AM',
    '4:00 AM - 5:00 AM', '5:00 AM - 6:00 AM', '6:00 AM - 7:00 AM', '7:00 AM - 8:00 AM'
];

// Get the day of week for the selected date
$selectedDayOfWeek = date('l', strtotime($selectedDate));

// Query class_timetable for this room and day
$schedule = [];
$classSchedules = $db->class_timetable->find([
    'room_id' => $room['_id'],
    'day_of_week' => $selectedDayOfWeek
]);
foreach ($classSchedules as $class) {
    $schedule[] = [
        'start_time' => $class['start_time'],
        'end_time' => $class['end_time'],
        'type' => 'Class'
    ];
}

// Query bookings for this room and day (if you have a bookings collection)
$bookings = $db->bookings->find([
    'room_id' => $room['_id'],
    'day_of_week' => $selectedDayOfWeek
]);
foreach ($bookings as $booking) {
    $schedule[] = [
        'start_time' => $booking['start_time'],
        'end_time' => $booking['end_time'],
        'type' => 'Booking'
    ];
}

// Sort by start_time
usort($schedule, function($a, $b) {
    return strcmp($a['start_time'], $b['start_time']);
});

// Get availability for selected date
// Get availability for selected date (inline logic)`n$dayOfWeek = date('l', strtotime($selectedDate));`n$dayOfMonth = date('j', strtotime($selectedDate));`n`nif ($dayOfWeek === 'Sunday' || $dayOfWeek === 'Saturday') {`n    $availabilityStatus = array_fill(0, 12, 'Available'); // Available on weekends`n} elseif ($dayOfMonth % 3 === 0) {`n    $availabilityStatus = ['Available', 'Booked', 'In Use', 'Available', 'Available', 'Booked', 'Booked', 'Booked', 'Booked', 'Available', 'Available', 'Booked'];`n} else {`n    $availabilityStatus = ['Available', 'Available', 'Available', 'Booked', 'Available', 'Available', 'Booked', 'Available', 'Available', 'Available', 'Available', 'Available'];`n}
$eveningAvailability = array_fill(0, 12, 'Available');

// Check if room is currently available for selected date
$isAvailable = !in_array('Booked', $availabilityStatus) && !in_array('In Use', $availabilityStatus);
?>

<?php include_once 'component/header.php'; ?>

<div class="main-container room-details">
    <div class="content-wrapper room-details">
        <div class="content-container room-details">
            <!-- Breadcrumb -->
            <div class="breadcrumb-section">
                <div class="breadcrumb-item">Live View</div>
                <div class="breadcrumb-separator">/</div>
                <div class="breadcrumb-item current"><?php echo htmlspecialchars($room['room_name']); ?></div>
            </div>

            <!-- Room Title -->
            <div class="room-title-section">
                <div class="room-title"><?php echo htmlspecialchars($room['room_name']); ?></div>
            </div>

            <!-- Room Info Section -->
            <div class="section-title">
                <div class="section-title-text">Room Info</div>
            </div>

            <!-- Availability Status -->
            <div class="availability-status">
                <div class="status-left">
                    <div class="status-icon">
                        <div class="icon-background">
                            <div class="icon-inner"></div>
                        </div>
                    </div>
                    <div class="status-text">
                        <div class="status-label <?php echo $isAvailable ? 'available' : 'occupied'; ?>">
                            <?php echo $isAvailable ? 'Available' : 'Occupied'; ?>
                        </div>
                    </div>
                </div>
                <div class="status-indicator">
                    <div class="indicator-dot <?php echo $isAvailable ? 'available' : 'occupied'; ?>"></div>
                </div>
            </div>

            <!-- Room Details -->
            <div class="room-details">
                <div class="detail-row">
                    <div class="detail-item">
                        <div class="detail-label">Block</div>
                        <div class="detail-value"><?php echo htmlspecialchars($room['block']); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Floor</div>
                        <div class="detail-value"><?php echo htmlspecialchars($room['floor']); ?></div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-item">
                        <div class="detail-label">Type</div>
                        <div class="detail-value"><?php echo htmlspecialchars($room['type']); ?></div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Capacity</div>
                        <div class="detail-value"><?php echo htmlspecialchars($room['capacity']); ?></div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-item amenities">
                        <div class="detail-label">Amenities</div>
                        <div class="detail-value"><?php echo htmlspecialchars($room['amenities']); ?></div>
                    </div>
                </div>
            </div>

            <!-- Today's Booking Schedule -->
            <div class="section-title">
                <div class="section-title-text"><?php echo date('F j, Y', strtotime($selectedDate)); ?> Booking Schedule</div>
            </div>

            <!-- Book Room Button -->
            <div class="book-button-container">
                <button class="book-room-button" onclick="bookThisRoom('<?php echo htmlspecialchars($room['room_name']); ?>')">
                    Book this room
                </button>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Availability Timeline -->
            <div class="section-title">
                <div class="section-title-text">Availability Timeline</div>
            </div>

            <!-- Calendar -->
            <div class="calendar-container">
                <div class="calendar-header">
                    <div class="calendar-nav">
                        <div class="nav-icon">‹</div>
                    </div>
                    <div class="calendar-title">Next 7 Days</div>
                    <div class="calendar-nav">
                        <div class="nav-icon">›</div>
                    </div>
                </div>

                <div class="calendar-grid">
                    <!-- Day headers -->
                    <div class="calendar-days">
                        <div class="day-header">S</div>
                        <div class="day-header">M</div>
                        <div class="day-header">T</div>
                        <div class="day-header">W</div>
                        <div class="day-header">T</div>
                        <div class="day-header">F</div>
                        <div class="day-header">S</div>
                    </div>

                    <!-- Calendar dates - Next 7 days only -->
                    <div class="calendar-week">
                        <?php
                        $currentDay = date('j');
                        $currentMonth = date('n');
                        $currentYear = date('Y');
                        
                        // Generate next 7 days
                        for ($i = 0; $i < 7; $i++) {
                            $date = date('Y-m-d', strtotime("+$i days"));
                            $dayNumber = date('j', strtotime($date));
                            $dayOfWeek = date('w', strtotime($date));
                            $isToday = $i == 0;
                            $isBooked = $i == 2; // Sample: 3rd day from today is booked
                            
                            $dayClass = 'calendar-day selectable';
                            if ($isToday) $dayClass .= ' today';
                            if ($isBooked) $dayClass .= ' booked';
                            
                            echo '<div class="' . $dayClass . '" data-date="' . $date . '" onclick="selectDate(\'' . $date . '\')">' . $dayNumber . '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="back-button-container">
                <button class="back-button" onclick="goBack()">
                    ← Back to Live View
                </button>
            </div>
        </div>
    </div>

    <!-- Extended Schedule Section -->
    <div class="extended-schedule">
        <div class="section-title">
            <div class="section-title-text"><?php echo date('F j, Y', strtotime($selectedDate)); ?> Booking Schedule</div>
        </div>

        <!-- Day Schedule -->
        <div class="day-schedule-container">
            <div class="schedule-grid">
                <!-- Day time slots header -->
                <div class="schedule-header">
                    <?php foreach ($timeSlots as $slot): ?>
                        <div class="time-slot-header"><?php echo str_replace(' - ', '<br/>-<br/>', $slot); ?></div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Day availability status -->
                <div class="schedule-status" id="extended-schedule-status">
                    <?php foreach ($availabilityStatus as $status): ?>
                        <div class="status-cell <?php echo strtolower(str_replace(' ', '-', $status)); ?>">
                            <?php echo $status; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Evening Schedule -->
        <div class="evening-schedule-container">
            <div class="schedule-grid">
                <!-- Evening time slots header -->
                <div class="schedule-header">
                    <?php foreach ($eveningTimeSlots as $slot): ?>
                        <div class="time-slot-header"><?php echo str_replace(' - ', '<br/>-<br/>', $slot); ?></div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Evening availability status -->
                <div class="schedule-status" id="evening-schedule-status">
                    <?php foreach ($eveningAvailability as $status): ?>
                        <div class="status-cell <?php echo strtolower(str_replace(' ', '-', $status)); ?>">
                            <?php echo $status; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Bottom Book Button -->
        <div class="bottom-book-button-container">
            <button class="book-room-button" onclick="bookThisRoom('<?php echo htmlspecialchars($room['room_name']); ?>')">
                Book this room
            </button>
        </div>
    </div>
</div>

<script>
function bookThisRoom(roomName) {
    // Get the selected date from the calendar
    const selectedDateElement = document.querySelector('.calendar-day.selected');
    let selectedDate = '';
    
    if (selectedDateElement) {
        selectedDate = selectedDateElement.getAttribute('data-date');
    }
    
    // Build the URL with room_id and date parameters
    let url = 'booking.php?room_id=' + encodeURIComponent(roomName);
    if (selectedDate) {
        url += '&date=' + encodeURIComponent(selectedDate);
    }
    
    window.location.href = url;
}

function goBack() {
    window.location.href = 'roomavailability.php';
}

function selectDate(date) {
    // Check if the selected date is within the next 7 days
    const selectedDate = new Date(date);
    const today = new Date();
    const maxDate = new Date();
    maxDate.setDate(today.getDate() + 7);
    
    // Reset today to start of day for accurate comparison
    today.setHours(0, 0, 0, 0);
    selectedDate.setHours(0, 0, 0, 0);
    maxDate.setHours(0, 0, 0, 0);
    
    // Check if date is in the future and within 7 days
    if (selectedDate < today || selectedDate > maxDate) {
        console.log('Cannot select date:', date, '- Date is outside the allowed range');
        return;
    }
    
    // Remove previous selection
    document.querySelectorAll('.calendar-day.selected').forEach(day => {
        day.classList.remove('selected');
    });
    
    // Add selection to clicked date
    const selectedDay = document.querySelector(`[data-date="${date}"]`);
    if (selectedDay) {
        selectedDay.classList.add('selected');
    }
    
    // Update the schedule for the selected date
    updateSchedule(date);
    
    console.log('Selected date:', date);
}

function updateSchedule(selectedDate) {
    // Update the section title in the main content area
    const sectionTitle = document.querySelector('.content-container .section-title-text');
    const dateObj = new Date(selectedDate);
    const formattedDate = dateObj.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    sectionTitle.textContent = formattedDate + ' Booking Schedule';
    
    // Update the extended schedule section title
    const extendedSectionTitle = document.querySelector('.extended-schedule .section-title-text');
    extendedSectionTitle.textContent = formattedDate + ' Booking Schedule';
    
    // Generate new availability status based on the selected date
    const availabilityStatus = generateAvailabilityForDate(selectedDate);
    
    // Update the extended schedule grid
    updateExtendedScheduleGrid(availabilityStatus);
    
    // Update availability status
    updateAvailabilityStatus(availabilityStatus);
}

function generateAvailabilityForDate(date) {
    // This simulates the PHP logic for generating availability
    const dateObj = new Date(date);
    const dayOfWeek = dateObj.getDay();
    const dayOfMonth = dateObj.getDate();
    
    if (dayOfWeek === 0 || dayOfWeek === 6) { // Weekend
        return Array(12).fill('Available');
    } else if (dayOfMonth % 3 === 0) { // Every 3rd day has some bookings
        return ['Available', 'Booked', 'In Use', 'Available', 'Available', 'Booked', 'Booked', 'Booked', 'Booked', 'Available', 'Available', 'Booked'];
    } else {
        return ['Available', 'Available', 'Available', 'Booked', 'Available', 'Available', 'Booked', 'Available', 'Available', 'Available', 'Available', 'Available'];
    }
}

function updateExtendedScheduleGrid(availabilityStatus) {
    const extendedScheduleStatus = document.getElementById('extended-schedule-status');
    extendedScheduleStatus.innerHTML = '';
    
    availabilityStatus.forEach(status => {
        const statusCell = document.createElement('div');
        statusCell.className = `status-cell ${status.toLowerCase().replace(' ', '-')}`;
        statusCell.textContent = status;
        extendedScheduleStatus.appendChild(statusCell);
    });
}

function updateAvailabilityStatus(availabilityStatus) {
    const isAvailable = !availabilityStatus.includes('Booked') && !availabilityStatus.includes('In Use');
    
    // Update status label
    const statusLabel = document.querySelector('.status-label');
    statusLabel.textContent = isAvailable ? 'Available' : 'Occupied';
    statusLabel.className = `status-label ${isAvailable ? 'available' : 'occupied'}`;
    
    // Update status indicator
    const indicatorDot = document.querySelector('.indicator-dot');
    indicatorDot.className = `indicator-dot ${isAvailable ? 'available' : 'occupied'}`;
}
</script>

<?php include_once 'component/footer.php'; ?> 



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Room Booking</title>
    <style>
        /* General Layout Styles */
        .main-container {
            display: inline-flex;
            align-items: flex-start;
            justify-content: flex-start;
            height: 1101px;
            width: 100%;
            padding-left: 160px;
            padding-right: 160px;
            padding-top: 20px;
            padding-bottom: 20px;
        }

        .content-wrapper {
            display: inline-flex;
            width: 100%;
            justify-content: center;
            align-items: flex-start;
        }

        .content-container {
            width: 480px;
            max-width: 960px;
            height: 1081px;
            padding-top: 20px;
            padding-bottom: 20px;
            overflow: hidden;
            display: inline-flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .header-section {
            width: 100%;
            display: inline-flex;
            justify-content: space-between;
        }

        .header-left .title {
            color: #171212;
            font-size: 32px;
            font-weight: 700;
            font-family: Inter;
            line-height: 40px;
        }

        /* Selected Booking Summary Section */
        .summary-section {
            padding: 16px;
            color: #171212;
            font-size: 18px;
            font-weight: 700;
            font-family: Inter;
            line-height: 23px;
        }

        .summary-card {
            width: 100%;
            padding: 16px;
            display: flex;
            justify-content: space-between;
        }

        .summary-card .details {
            font-size: 14px;
            font-family: Inter;
            color: #171212;
        }

        .summary-card .label {
            font-size: 14px;
            color: #876363;
        }

        /* Room Details Section */
        .room-details-section {
            padding-top: 16px;
            padding-bottom: 8px;
            display: flex;
            flex-direction: column;
        }

        .details-card {
            display: flex;
            align-items: center;
            padding: 15px;
            background: white;
            border-radius: 12px;
            outline: 1px solid #E5DBDB;
            outline-offset: -1px;
            margin-top: 16px;
        }

        /* Button Styles */
        .button {
            background-color: #C3272B;
            color: white;
            padding: 12px 16px;
            border-radius: 20px;
            text-align: center;
            font-size: 14px;
            font-weight: 700;
            font-family: Inter;
            cursor: pointer;
        }

        .cancel-button {
            color: #876363;
            font-size: 21px;
            font-weight: 700;
            text-align: center;
            margin-top: 16px;
            cursor: pointer;
        }

        /* Form Input Styles */
        .input-field {
            height: 56px;
            padding: 15px;
            background: white;
            overflow: hidden;
            border-radius: 12px;
            outline: 1px solid #E5DBDB;
            outline-offset: -1px;
            color: #876363;
            font-size: 16px;
            font-family: Inter;
        }

        .input-placeholder {
            color: #876363;
            font-size: 16px;
            font-family: Inter;
            font-weight: 400;
            line-height: 24px;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <div class="content-wrapper">
            <div class="content-container">
                <!-- Header Section -->
                <div class="header-section">
                    <div class="header-left">
                        <div class="title">Confirm Room Booking</div>
                    </div>
                </div>

                <!-- Selected Booking Summary Section -->
                <div class="summary-section">
                    <div>Selected Booking Summary</div>
                    <div class="summary-card">
                        <div class="label">Selected Room</div>
                        <div class="details">Computer Lab 21</div>
                    </div>
                    <div class="summary-card">
                        <div class="label">Selected Date</div>
                        <div class="details">June 20, 2025</div>
                    </div>
                </div>

                <!-- Student Details Section -->
                <div class="room-details-section">
                    <div>Student Details</div>

                    <div class="details-card">
                        <div class="label">Student ID</div>
                        <div class="input-field">
                            <div class="input-placeholder">e.g., 0312345</div>
                        </div>
                    </div>

                    <div class="details-card">
                        <div class="label">Full Name</div>
                        <div class="input-field">
                            <div class="input-placeholder">Enter your full name</div>
                        </div>
                    </div>
                </div>

                <!-- Booking Details Section -->
                <div class="room-details-section">
                    <div>Booking Details</div>

                    <div class="details-card">
                        <div class="label">Start Time</div>
                        <div class="input-field">
                            <div class="input-placeholder">Enter the Start Time</div>
                        </div>
                    </div>

                    <div class="details-card">
                        <div class="label">End Time</div>
                        <div class="input-field">
                            <div class="input-placeholder">Enter the End Time</div>
                        </div>
                    </div>

                    <div class="details-card">
                        <div class="label">Purpose</div>
                        <div class="input-field">
                            <div class="input-placeholder">Enter the Purpose</div>
                        </div>
                    </div>

                    <div class="details-card">
                        <div class="label">Number of People</div>
                        <div class="input-field">
                            <div class="input-placeholder">Enter the Number of People</div>
                        </div>
                    </div>
                </div>

                <!-- Submit Booking Button -->
                <div class="button">Submit Booking</div>

                <!-- Cancel Button -->
                <div class="cancel-button">← Cancel</div>
            </div>
        </div>
    </div>
</body>

</html>
