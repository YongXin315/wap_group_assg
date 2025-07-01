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
    } else {
        // Convert MongoDB document to array and ensure all required fields exist
        $roomArray = $room->getArrayCopy();
        $room = array_merge([
            'room_name' => $roomId,
            'type' => 'Computer Lab',
            'block' => 'Block D',
            'floor' => 'Level 7.14',
            'capacity' => '21-30 persons',
            'amenities' => 'Whiteboard, TV screen, Air-conditioning, Computers',
            'class_timetable' => []
        ], $roomArray);
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
    'day_of_week' => $selectedDayOfWeek,
    'status' => 'approved'
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

// Get availability for selected date based on actual bookings
$dayOfWeek = date('l', strtotime($selectedDate));
$availabilityStatus = array_fill(0, 12, 'Available'); // Default all slots as available

// Check for approved bookings and pending bookings by current user
$approvedBookings = $db->bookings->find([
    'room_id' => $room['_id'],
    'day_of_week' => $dayOfWeek,
    'status' => 'approved'
]);

$pendingBookings = $db->bookings->find([
    'room_id' => $room['_id'],
    'day_of_week' => $dayOfWeek,
    'status' => 'pending',
    'student_id' => $_SESSION['student_id']
]);

// Map time slots to array indices
$timeSlotMap = [
    '8:00 AM - 9:00 AM' => 0, '9:00 AM - 10:00 AM' => 1, '10:00 AM - 11:00 AM' => 2, '11:00 AM - 12:00 PM' => 3,
    '12:00 PM - 1:00 PM' => 4, '1:00 PM - 2:00 PM' => 5, '2:00 PM - 3:00 PM' => 6, '3:00 PM - 4:00 PM' => 7,
    '4:00 PM - 5:00 PM' => 8, '5:00 PM - 6:00 PM' => 9, '6:00 PM - 7:00 PM' => 10, '7:00 PM - 8:00 PM' => 11
];

// Process approved bookings first (they take precedence)
foreach ($approvedBookings as $booking) {
    $startTime = isset($booking['start_time']) ? $booking['start_time'] : '';
    $endTime = isset($booking['end_time']) ? $booking['end_time'] : '';
    
    if (!empty($startTime) && !empty($endTime)) {
        $timeSlot = $startTime . ' - ' . $endTime;
        if (isset($timeSlotMap[$timeSlot])) {
            $availabilityStatus[$timeSlotMap[$timeSlot]] = 'Booked';
        }
    }
}

// Process pending bookings by current user (only if slot is still available)
foreach ($pendingBookings as $booking) {
    $startTime = isset($booking['start_time']) ? $booking['start_time'] : '';
    $endTime = isset($booking['end_time']) ? $booking['end_time'] : '';
    
    if (!empty($startTime) && !empty($endTime)) {
        $timeSlot = $startTime . ' - ' . $endTime;
        if (isset($timeSlotMap[$timeSlot]) && $availabilityStatus[$timeSlotMap[$timeSlot]] === 'Available') {
            $availabilityStatus[$timeSlotMap[$timeSlot]] = 'Requested';
        }
    }
}

$eveningAvailability = array_fill(0, 12, 'Available');

// Check if room is currently available for selected date
$isAvailable = !in_array('Booked', $availabilityStatus) && !in_array('In Use', $availabilityStatus);
?>

<?php include_once 'component/header.php'; ?>

<style>
/* Reset and Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background-color: #f8f9fa;
    color: #171212;
    line-height: 1.5;
}

/* Main Layout */
.main-container {
    width: 100%;
    min-height: 100vh;
    background: white;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.content-wrapper {
    width: 100%;
    max-width: 1400px;
    padding: 20px 24px;
    display: flex;
    gap: 24px;
    align-items: center;
}

.main-content {
    max-width: 1100px;
    margin: 0 auto;
    display: flex;
    flex-direction: row;
    align-items: flex-start;
    gap: 48px;
}

.content-container {
    flex: 1 1 0;
    max-width: 700px;
    margin: 0 auto;
    padding: 0 24px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Breadcrumb */
.breadcrumb-section {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 16px 0;
}

.breadcrumb-item {
    display: flex;
    align-items: center;
}

.breadcrumb-item .text {
    color: #876363;
    font-size: 16px;
    font-weight: 500;
    line-height: 24px;
}

.breadcrumb-item .text a {
    color: #876363;
    text-decoration: none;
    transition: color 0.2s ease;
}

.breadcrumb-item .text a:hover {
    color: #171212;
}

.breadcrumb-item.current .text {
    color: #171212;
}

/* Room Title */
.room-title-section {
    padding: 24px 0 32px 0;
}

.room-title .text {
    color: #171212;
    font-size: 36px;
    font-weight: 700;
    line-height: 44px;
}

/* Section Titles */
.section-title {
    padding: 32px 0 20px 0;
}

.section-title .text {
    color: #171212;
    font-size: 24px;
    font-weight: 700;
    line-height: 32px;
}

/* Availability Status */
.availability-status {
    height: 72px;
    padding: 0 24px;
    background: white;
    border: 1px solid #E5E8EB;
    border-radius: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.status-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.status-icon {
    width: 48px;
    height: 48px;
    background: #F5F0F0;
    border-radius: 12px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.status-text {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.status-label {
    color: #34C759;
    font-size: 18px;
    font-weight: 600;
    line-height: 24px;
}

.status-label.occupied {
    color: #FF3B30;
}

.status-subtitle .text {
    color: #876363;
    font-size: 12px;
    font-weight: 400;
    line-height: 18px;
}

.status-indicator {
    display: flex;
    align-items: center;
}

.indicator-dot {
    width: 32px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.indicator-dot .dot {
    width: 14px;
    height: 14px;
    background: #08875C;
    border-radius: 7px;
}

.indicator-dot.occupied .dot {
    background: #FF3B30;
}

/* Room Details */
.room-details {
    display: flex;
    flex-direction: column;
    gap: 32px;
    padding: 32px;
    background: white;
    border: 1px solid #E5E8EB;
    border-radius: 16px;
    margin-bottom: 32px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.detail-row {
    display: flex;
    gap: 48px;
    padding: 24px 0;
    border-top: 1px solid #E5E8EB;
}

.detail-row:first-child {
    border-top: none;
    padding-top: 0;
}

.detail-row:last-child {
    padding-bottom: 0;
}

.detail-item {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 12px;
    min-width: 200px;
}

.detail-label .text {
    color: #876363;
    font-size: 14px;
    font-weight: 500;
    line-height: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.detail-value .text {
    color: #171212;
    font-size: 16px;
    font-weight: 500;
    line-height: 24px;
}

/* Schedule Section */
.schedule-section {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.schedule-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    border-bottom: 1px solid #E5E8EB;
}

.schedule-header-row {
    display: flex;
    background: white;
}

.time-slot-header {
    flex: 1;
    padding: 12px 16px;
    text-align: center;
    min-width: 100px;
}

.time-slot-header .text {
    color: #171212;
    font-size: 14px;
    font-weight: 500;
    line-height: 21px;
}

.schedule-status-row {
    display: flex;
}

.status-cell {
    flex: 1;
    height: 72px;
    padding: 8px 16px;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    min-width: 100px;
}

.status-cell .text {
    font-size: 14px;
    font-weight: 400;
    line-height: 21px;
}

.status-cell.available .text {
    color: #34C759;
}

.status-cell.booked .text {
    color: #34C759;
    font-weight: 600;
}

.status-cell.requested .text {
    color: #FF9500;
    font-weight: 500;
}

.status-cell.in-use .text {
    color: #007AFF;
}

/* Timeline */
.timeline-container {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 12px 0;
}

.timeline-item {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 12px 0;
}

.timeline-icon {
    width: 40px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    padding-top: 12px;
}

.timeline-line {
    width: 2px;
    height: 32px;
    background: #E5DBDB;
}

.timeline-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 12px 0;
}

.timeline-time .text {
    color: #171212;
    font-size: 16px;
    font-weight: 500;
    line-height: 24px;
}

.timeline-status .text {
    color: #876363;
    font-size: 16px;
    font-weight: 400;
    line-height: 24px;
}

/* Buttons */
.button-container {
    display: flex;
    justify-content: flex-end;
    padding: 12px 0;
}

.book-button {
    height: 40px;
    padding: 0 16px;
    background: #E82933;
    border: none;
    border-radius: 20px;
    color: white;
    font-size: 14px;
    font-weight: 700;
    line-height: 21px;
    cursor: pointer;
    transition: background-color 0.2s ease;
    min-width: 120px;
}

.book-button:hover {
    background: #d42428;
}

/* Sidebar */
.sidebar {
    width: 340px;
    min-width: 280px;
    max-width: 360px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

/* Calendar */
.calendar-container {
    padding: 16px;
    background: white;
    border: 1px solid #E5E8EB;
    border-radius: 12px;
}

.calendar {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 4px 0;
}

.calendar-nav {
    width: 40px;
    height: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    border-radius: 8px;
    transition: background-color 0.2s ease;
}

.calendar-nav:hover {
    background: #F5F0F0;
}

.nav-icon {
    font-size: 18px;
    color: #171212;
}

.calendar-title .text {
    color: #171212;
    font-size: 16px;
    font-weight: 700;
    line-height: 20px;
}

.calendar-grid {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.calendar-days {
    display: flex;
}

.calendar-day {
    width: 47px;
    height: 48px;
    display: flex;
    justify-content: center;
    align-items: center;
    padding-bottom: 2px;
}

.calendar-day .text {
    color: #171212;
    font-size: 13px;
    font-weight: 700;
    line-height: 20px;
}

.calendar-dates {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.calendar-week {
    display: flex;
}

.calendar-date {
    width: 47px;
    height: 48px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    border-radius: 24px;
    transition: all 0.2s ease;
}

.calendar-date:hover {
    background: #F5F0F0;
}

.calendar-date .date {
    width: 100%;
    height: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 24px;
}

.calendar-date .text {
    text-align: center;
    font-size: 14px;
    font-weight: 500;
    line-height: 21px;
}

/* Extended Schedule */
.extended-schedule {
    display: flex;
    flex-direction: column;
    gap: 20px;
    padding: 20px 0;
}

.extended-schedule-title {
    padding: 20px 0 12px 0;
}

.extended-schedule-title .text {
    color: #171212;
    font-size: 22px;
    font-weight: 700;
    line-height: 28px;
}

.extended-schedule-container {
    padding: 12px 0;
}

.extended-schedule-grid {
    background: white;
    border: 1px solid #E5DBDB;
    border-radius: 12px;
    overflow: hidden;
}

.extended-schedule-header {
    border-bottom: 1px solid #E5E8EB;
}

.extended-schedule-header-row {
    display: flex;
    background: white;
}

.extended-time-slot-header {
    flex: 1;
    padding: 12px 16px;
    text-align: center;
    min-width: 100px;
}

.extended-time-slot-header .text {
    color: #171212;
    font-size: 14px;
    font-weight: 500;
    line-height: 21px;
}

.extended-schedule-status-row {
    display: flex;
}

.extended-status-cell {
    flex: 1;
    height: 72px;
    padding: 8px 16px;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    min-width: 100px;
}

.extended-status-cell .text {
    font-size: 14px;
    font-weight: 400;
    line-height: 21px;
}

.extended-status-cell.available .text {
    color: #34C759;
}

.extended-status-cell.booked .text {
    color: #34C759;
    font-weight: 600;
}

.extended-status-cell.requested .text {
    color: #FF9500;
    font-weight: 500;
}

.extended-status-cell.in-use .text {
    color: #007AFF;
}

/* Bottom Button */
.bottom-button-container {
    display: flex;
    justify-content: center;
    padding: 20px 0;
}

.bottom-button-wrapper {
    display: flex;
    justify-content: flex-end;
    padding: 12px 0;
}

.bottom-book-button {
    height: 40px;
    padding: 0 16px;
    background: #C3272B;
    border: none;
    border-radius: 20px;
    color: white;
    font-size: 14px;
    font-weight: 700;
    line-height: 21px;
    cursor: pointer;
    transition: background-color 0.2s ease;
    min-width: 120px;
}

.bottom-book-button:hover {
    background: #b02428;
}

/* Footer */
.footer {
    width: 100%;
    background: white;
    border-top: 1px solid #E5E8EB;
    margin-top: auto;
}

.footer-content {
    max-width: 960px;
    margin: 0 auto;
    padding: 40px 20px;
}

.footer-copyright .text {
    text-align: center;
    color: #915457;
    font-size: 16px;
    font-weight: 400;
    line-height: 24px;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .content-wrapper {
        flex-direction: column;
        gap: 20px;
    }
    
    .main-content {
        flex-direction: column;
        gap: 20px;
    }
    
    .sidebar {
        width: 100%;
        max-width: 600px;
        align-self: center;
    }
}

@media (max-width: 768px) {
    .content-wrapper {
        padding: 16px;
    }
    
    .detail-row {
        flex-direction: column;
        gap: 16px;
    }
    
    .schedule-header-row,
    .schedule-status-row,
    .extended-schedule-header-row,
    .extended-schedule-status-row {
        flex-wrap: wrap;
    }
    
    .time-slot-header,
    .status-cell,
    .extended-time-slot-header,
    .extended-status-cell {
        min-width: 80px;
        flex: 1 1 80px;
    }
}
</style>

<div class="main-container">
    <div class="content-wrapper">
        <div class="main-content">
            <div class="content-container">
            <!-- Breadcrumb -->
            <div class="breadcrumb-section">
                    <div class="breadcrumb-item">
                        <div class="text"><a href="roomavailability.php">Live View</a></div>
                    </div>
                    <div class="breadcrumb-item">
                        <div class="text">/</div>
                    </div>
                    <div class="breadcrumb-item current">
                        <div class="text"><?php echo htmlspecialchars($room['room_name']); ?></div>
                    </div>
            </div>

            <!-- Room Title -->
            <div class="room-title-section">
                    <div class="room-title">
                        <div class="text"><?php echo htmlspecialchars($room['room_name']); ?></div>
                    </div>
            </div>

            <!-- Room Info Section -->
            <div class="section-title">
                    <div class="text">Room Info</div>
            </div>

            <!-- Availability Status -->
            <div class="availability-status">
                <div class="status-left">
                    <div class="status-icon">
                            <div class="icon-inner"></div>
                    </div>
                    <div class="status-text">
                            <div class="status-label <?php echo $isAvailable ? '' : 'occupied'; ?>">
                                <?php echo $isAvailable ? 'Currently Available' : 'Currently Occupied'; ?>
                        </div>
                    </div>
                </div>
                <div class="status-indicator">
                        <div class="indicator-dot <?php echo $isAvailable ? '' : 'occupied'; ?>">
                            <div class="dot"></div>
                        </div>
                </div>
            </div>

            <!-- Room Details -->
            <div class="room-details">
                <div class="detail-row">
                    <div class="detail-item">
                            <div class="detail-label">
                                <div class="text">Block</div>
                            </div>
                            <div class="detail-value">
                                <div class="text"><?php echo htmlspecialchars($room['block']); ?></div>
                            </div>
                    </div>
                    <div class="detail-item">
                            <div class="detail-label">
                                <div class="text">Floor</div>
                            </div>
                            <div class="detail-value">
                                <div class="text"><?php echo htmlspecialchars($room['floor']); ?></div>
                            </div>
                    </div>
                </div>
                <div class="detail-row">
                    <div class="detail-item">
                            <div class="detail-label">
                                <div class="text">Type</div>
                            </div>
                            <div class="detail-value">
                                <div class="text"><?php echo htmlspecialchars($room['type']); ?></div>
                            </div>
                    </div>
                    <div class="detail-item">
                            <div class="detail-label">
                                <div class="text">Capacity</div>
                            </div>
                            <div class="detail-value">
                                <div class="text"><?php echo htmlspecialchars($room['capacity']); ?></div>
                            </div>
                    </div>
                </div>
                <div class="detail-row">
                        <div class="detail-item">
                            <div class="detail-label">
                                <div class="text">Amenities</div>
                    </div>
                            <div class="detail-value">
                                <div class="text"><?php echo htmlspecialchars($room['amenities']); ?></div>
                </div>
            </div>
            </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Availability Timeline -->
            <div class="section-title">
                    <div class="text">Availability Timeline</div>
            </div>

            <!-- Calendar -->
            <div class="calendar-container">
                    <div class="calendar">
                <div class="calendar-header">
                            <div class="calendar-nav" onclick="previousMonth()">
                        <div class="nav-icon">‹</div>
                    </div>
                            <div class="calendar-title">
                                <div class="text" id="calendar-title"><?php echo $currentMonth; ?></div>
                            </div>
                            <div class="calendar-nav" onclick="nextMonth()">
                        <div class="nav-icon">›</div>
                    </div>
                </div>

                <div class="calendar-grid">
                    <!-- Day headers -->
                    <div class="calendar-days">
                                <div class="calendar-day">
                                    <div class="text">S</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="text">M</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="text">T</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="text">W</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="text">T</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="text">F</div>
                                </div>
                                <div class="calendar-day">
                                    <div class="text">S</div>
                                </div>
                    </div>

                            <!-- Calendar dates -->
                            <div class="calendar-dates" id="calendar-dates">
                                <!-- Calendar dates will be populated by JavaScript -->
                            </div>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
                <div class="button-container">
                    <button class="book-button" onclick="goBack()">
                        <div class="text">← Back to Live View</div>
                </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Extended Schedule Section -->
    <div class="extended-schedule">
        <div class="extended-schedule-title">
            <div class="text"><?php echo date('F j, Y', strtotime($selectedDate)); ?> Booking Schedule</div>
        </div>

        <!-- Day Schedule -->
        <div class="extended-schedule-container">
            <div class="extended-schedule-grid">
                <div class="extended-schedule-header">
                    <div class="extended-schedule-header-row">
                    <?php foreach ($timeSlots as $slot): ?>
                            <div class="extended-time-slot-header">
                                <div class="text"><?php echo str_replace(' - ', '<br/>-<br/>', $slot); ?></div>
                            </div>
                    <?php endforeach; ?>
                </div>
                </div>
                <div class="extended-schedule-status">
                    <div class="extended-schedule-status-row">
                    <?php foreach ($availabilityStatus as $status): ?>
                            <div class="extended-status-cell <?php echo strtolower(str_replace(' ', '-', $status)); ?>">
                                <div class="text"><?php echo $status; ?></div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Evening Schedule -->
        <div class="extended-schedule-container">
            <div class="extended-schedule-grid">
                <div class="extended-schedule-header">
                    <div class="extended-schedule-header-row">
                    <?php foreach ($eveningTimeSlots as $slot): ?>
                            <div class="extended-time-slot-header">
                                <div class="text"><?php echo str_replace(' - ', '<br/>-<br/>', $slot); ?></div>
                            </div>
                    <?php endforeach; ?>
                </div>
                </div>
                <div class="extended-schedule-status">
                    <div class="extended-schedule-status-row">
                    <?php foreach ($eveningAvailability as $status): ?>
                            <div class="extended-status-cell <?php echo strtolower(str_replace(' ', '-', $status)); ?>">
                                <div class="text"><?php echo $status; ?></div>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
                </div>
            </div>
        </div>

        <!-- Bottom Book Button -->
    <div class="bottom-button-container">
        <div class="bottom-button-wrapper">
            <button class="bottom-book-button" onclick="bookThisRoom('<?php echo htmlspecialchars($room['room_name']); ?>')">
                <div class="text">Book this room</div>
            </button>
        </div>
    </div>
</div>

<!-- Footer -->
<div class="footer">
    <div class="footer-content">
        <div class="footer-copyright">
            <div class="text">© 2025 Taylor's University. All rights reserved.</div>
        </div>
    </div>
</div>

<script>
// Calendar functionality
let currentCalendarDate = new Date();
let selectedCalendarDate = new Date('<?php echo $selectedDate; ?>');

function initializeCalendar() {
    updateCalendar();
}

function updateCalendar() {
    const calendarDates = document.getElementById('calendar-dates');
    const calendarTitle = document.getElementById('calendar-title');
    
    // Update title
    const monthYear = currentCalendarDate.toLocaleDateString('en-US', { 
        month: 'long', 
        year: 'numeric' 
    });
    calendarTitle.textContent = monthYear;
    
    // Generate calendar dates
    calendarDates.innerHTML = '';
    
    // Get the first day of the month and number of days
    const firstDay = new Date(currentCalendarDate.getFullYear(), currentCalendarDate.getMonth(), 1);
    const lastDay = new Date(currentCalendarDate.getFullYear(), currentCalendarDate.getMonth() + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());
    
    // Generate 6 weeks of dates
    for (let week = 0; week < 6; week++) {
        const weekDiv = document.createElement('div');
        weekDiv.className = 'calendar-week';
        weekDiv.style.display = 'flex';
        
        for (let day = 0; day < 7; day++) {
            const currentDate = new Date(startDate);
            currentDate.setDate(startDate.getDate() + (week * 7) + day);
            
            const dayDiv = document.createElement('div');
            const dayNumber = currentDate.getDate();
            const isCurrentMonth = currentDate.getMonth() === currentCalendarDate.getMonth();
            const isToday = currentDate.toDateString() === new Date().toDateString();
            const isSelected = currentDate.toDateString() === selectedCalendarDate.toDateString();
            const isPast = currentDate < new Date().setHours(0, 0, 0, 0);
            
            dayDiv.className = 'calendar-date';
            dayDiv.style.width = '47px';
            dayDiv.style.height = '48px';
            dayDiv.style.flexDirection = 'column';
            dayDiv.style.justifyContent = 'flex-start';
            dayDiv.style.alignItems = 'center';
            dayDiv.style.display = 'inline-flex';
            dayDiv.style.cursor = 'pointer';
            
            if (!isCurrentMonth) {
                dayDiv.style.opacity = '0.45';
            }
            if (isToday) {
                dayDiv.style.background = '#C3272B';
                dayDiv.style.borderRadius = '24px';
            }
            if (isSelected && !isToday) {
                dayDiv.style.background = '#F5F0F0';
                dayDiv.style.borderRadius = '24px';
            }
            if (isPast) {
                dayDiv.style.opacity = '0.45';
                dayDiv.style.cursor = 'not-allowed';
            }
            
            const dateDiv = document.createElement('div');
            dateDiv.className = 'date';
            dateDiv.style.alignSelf = 'stretch';
            dateDiv.style.flex = '1 1 0';
            dateDiv.style.borderRadius = '24px';
            dateDiv.style.justifyContent = 'center';
            dateDiv.style.alignItems = 'center';
            dateDiv.style.display = 'inline-flex';
            
            const textDiv = document.createElement('div');
            textDiv.className = 'text';
            textDiv.style.textAlign = 'center';
            textDiv.style.fontSize = '14px';
            textDiv.style.fontFamily = 'Inter';
            textDiv.style.fontWeight = '500';
            textDiv.style.lineHeight = '21px';
            textDiv.style.wordWrap = 'break-word';
            
            if (isToday) {
                textDiv.style.color = 'white';
            } else if (isCurrentMonth) {
                textDiv.style.color = '#171212';
            } else {
                textDiv.style.color = 'rgba(22.95, 17.85, 17.85, 0.45)';
            }
            
            textDiv.textContent = dayNumber;
            
            dateDiv.appendChild(textDiv);
            dayDiv.appendChild(dateDiv);
            
            // Allow clicking on today or any future date (no 7-day limit)
            if (!isPast) {
                dayDiv.onclick = function() {
                    selectCalendarDate(new Date(currentDate));
                };
            }
            
            weekDiv.appendChild(dayDiv);
        }
        
        calendarDates.appendChild(weekDiv);
    }
}

function selectCalendarDate(date) {
    // Check if the selected date is today or in the future
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const selectedDate = new Date(date);
    selectedDate.setHours(0, 0, 0, 0);
    
    // Allow selection of today or any future date (no 7-day limit)
    if (selectedDate < today) {
        console.log('Cannot select past date:', date);
        return;
    }
    
    selectedCalendarDate = new Date(date);
    
    // Format date as YYYY-MM-DD without timezone issues
    const year = selectedCalendarDate.getFullYear();
    const month = String(selectedCalendarDate.getMonth() + 1).padStart(2, '0');
    const day = String(selectedCalendarDate.getDate()).padStart(2, '0');
    const dateString = `${year}-${month}-${day}`;
    
    // Update URL without page reload
    const url = new URL(window.location);
    url.searchParams.set('date', dateString);
    window.history.pushState({}, '', url);
    
    // Update the schedule for the selected date
    updateSchedule(dateString);
    
    // Update calendar selection
    updateCalendar();
    
    console.log('Selected date:', dateString);
}

function previousMonth() {
    currentCalendarDate.setMonth(currentCalendarDate.getMonth() - 1);
    updateCalendar();
}

function nextMonth() {
    currentCalendarDate.setMonth(currentCalendarDate.getMonth() + 1);
    updateCalendar();
}

function bookThisRoom(roomName) {
    // Get the selected date from the calendar
    const dateString = selectedCalendarDate.toISOString().split('T')[0];
    
    // Build the URL with room_id and date parameters
    let url = 'booking.php?room_id=' + encodeURIComponent(roomName);
    if (dateString) {
        url += '&date=' + encodeURIComponent(dateString);
    }
    
    window.location.href = url;
}

function goBack() {
    window.location.href = 'roomavailability.php';
}

function updateSchedule(selectedDate) {
    // Update the extended schedule section title
    const extendedSectionTitle = document.querySelector('.extended-schedule-title .text');
    if (extendedSectionTitle) {
    const dateObj = new Date(selectedDate);
    const formattedDate = dateObj.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    extendedSectionTitle.textContent = formattedDate + ' Booking Schedule';
    }
    
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
    const extendedScheduleStatus = document.querySelector('.extended-schedule-status-row');
    if (extendedScheduleStatus) {
    extendedScheduleStatus.innerHTML = '';
    
    availabilityStatus.forEach(status => {
        const statusCell = document.createElement('div');
            statusCell.className = `extended-status-cell ${status.toLowerCase().replace(' ', '-')}`;
            statusCell.innerHTML = '<div class="text">' + status + '</div>';
        extendedScheduleStatus.appendChild(statusCell);
    });
}
}

function updateTimeline(availabilityStatus) {
    const timelineContainer = document.querySelector('.timeline-container');
    if (timelineContainer) {
        timelineContainer.innerHTML = '';
        
        const timeSlots = [
            '8:00 AM - 9:00 AM', '9:00 AM - 10:00 AM', '10:00 AM - 11:00 AM', '11:00 AM - 12:00 PM',
            '12:00 PM - 1:00 PM', '1:00 PM - 2:00 PM', '2:00 PM - 3:00 PM', '3:00 PM - 4:00 PM',
            '4:00 PM - 5:00 PM', '5:00 PM - 6:00 PM', '6:00 PM - 7:00 PM', '7:00 PM - 8:00 PM'
        ];
        
        timeSlots.forEach((slot, index) => {
            const timelineItem = document.createElement('div');
            timelineItem.className = 'timeline-item';
            timelineItem.style.alignSelf = 'stretch';
            timelineItem.style.flex = '1 1 0';
            timelineItem.style.justifyContent = 'flex-start';
            timelineItem.style.alignItems = 'flex-start';
            timelineItem.style.gap = '8px';
            timelineItem.style.display = 'inline-flex';
            
            const timelineIcon = document.createElement('div');
            timelineIcon.className = 'timeline-icon';
            timelineIcon.style.width = '40px';
            timelineIcon.style.alignSelf = 'stretch';
            timelineIcon.style.paddingTop = '12px';
            timelineIcon.style.flexDirection = 'column';
            timelineIcon.style.justifyContent = 'flex-start';
            timelineIcon.style.alignItems = 'center';
            timelineIcon.style.gap = '4px';
            timelineIcon.style.display = 'inline-flex';
            
            const timelineLine = document.createElement('div');
            timelineLine.className = 'timeline-line';
            timelineLine.style.width = '2px';
            timelineLine.style.height = '32px';
            timelineLine.style.background = '#E5DBDB';
            
            timelineIcon.appendChild(timelineLine);
            
            const timelineContent = document.createElement('div');
            timelineContent.className = 'timeline-content';
            timelineContent.style.flex = '1 1 0';
            timelineContent.style.alignSelf = 'stretch';
            timelineContent.style.paddingTop = '12px';
            timelineContent.style.paddingBottom = '12px';
            timelineContent.style.flexDirection = 'column';
            timelineContent.style.justifyContent = 'flex-start';
            timelineContent.style.alignItems = 'flex-start';
            timelineContent.style.display = 'inline-flex';
            
            const timelineTime = document.createElement('div');
            timelineTime.className = 'timeline-time';
            timelineTime.style.alignSelf = 'stretch';
            timelineTime.style.flexDirection = 'column';
            timelineTime.style.justifyContent = 'flex-start';
            timelineTime.style.alignItems = 'flex-start';
            timelineTime.style.display = 'flex';
            
            const timeText = document.createElement('div');
            timeText.className = 'text';
            timeText.style.alignSelf = 'stretch';
            timeText.style.color = '#171212';
            timeText.style.fontSize = '16px';
            timeText.style.fontFamily = 'Inter';
            timeText.style.fontWeight = '500';
            timeText.style.lineHeight = '24px';
            timeText.style.wordWrap = 'break-word';
            timeText.textContent = slot;
            
            timelineTime.appendChild(timeText);
            
            const timelineStatus = document.createElement('div');
            timelineStatus.className = 'timeline-status';
            timelineStatus.style.alignSelf = 'stretch';
            timelineStatus.style.flexDirection = 'column';
            timelineStatus.style.justifyContent = 'flex-start';
            timelineStatus.style.alignItems = 'flex-start';
            timelineStatus.style.display = 'flex';
            
            const statusText = document.createElement('div');
            statusText.className = 'text';
            statusText.style.alignSelf = 'stretch';
            statusText.style.color = '#876363';
            statusText.style.fontSize = '16px';
            statusText.style.fontFamily = 'Inter';
            statusText.style.fontWeight = '400';
            statusText.style.lineHeight = '24px';
            statusText.style.wordWrap = 'break-word';
            statusText.textContent = availabilityStatus[index];
            
            timelineStatus.appendChild(statusText);
            timelineContent.appendChild(timelineTime);
            timelineContent.appendChild(timelineStatus);
            
            timelineItem.appendChild(timelineIcon);
            timelineItem.appendChild(timelineContent);
            
            timelineContainer.appendChild(timelineItem);
        });
    }
}

function updateAvailabilityStatus(availabilityStatus) {
    // Don't update the main availability status - it should always show current status
    // The main status shows if the room is currently available today, not for selected date
    return;
}

// Initialize calendar when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeCalendar();
});
</script>

<?php include_once 'component/footer.php'; ?>
