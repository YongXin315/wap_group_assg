<?php
session_start();

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
            '_id' => $roomId,
            'room_name' => $roomId,
            'type' => 'Computer Lab',
            'block' => 'Block D',
            'floor' => 'Level 7.14',
            'capacity' => '21-30 persons',
            'amenities' => 'Whiteboard, TV screen, Air-conditioning, Computers',
            'class_timetable' => [],
            'status' => 'Available' // Added status field
        ];
    } else {
        // Convert MongoDB document to array and ensure all required fields exist
        $roomArray = $room->getArrayCopy();
        $room = array_merge([
            '_id' => $roomId,
            'room_name' => $roomId,
            'type' => 'Computer Lab',
            'block' => 'Block D',
            'floor' => 'Level 7.14',
            'capacity' => '21-30 persons',
            'amenities' => 'Whiteboard, TV screen, Air-conditioning, Computers',
            'class_timetable' => [],
            'status' => 'Available' // Added status field
        ], $roomArray);
    }
} catch (Exception $e) {
    // Fallback to static data if database error
    $room = [
        '_id' => $roomId,
        'room_name' => $roomId,
        'type' => 'Computer Lab',
        'block' => 'Block D',
        'floor' => 'Level 7.14',
        'capacity' => '21-30 persons',
        'amenities' => 'Whiteboard, TV screen, Air-conditioning, Computers',
        'class_timetable' => [],
        'status' => 'Available' // Added status field
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
    '08:00 - 09:00', '09:00 - 10:00', '10:00 - 11:00', '11:00 - 12:00',
    '12:00 - 13:00', '13:00 - 14:00', '14:00 - 15:00', '15:00 - 16:00',
    '16:00 - 17:00', '17:00 - 18:00', '18:00 - 19:00', '19:00 - 20:00'
];

// Generate evening time slots
$eveningTimeSlots = [
    '20:00 - 21:00', '21:00 - 22:00', '22:00 - 23:00', '23:00 - 00:00',
    '00:00 - 01:00', '01:00 - 02:00', '02:00 - 03:00', '03:00 - 04:00',
    '04:00 - 05:00', '05:00 - 06:00', '06:00 - 07:00', '07:00 - 08:00'
];

// Get the day of week for the selected date
$selectedDayOfWeek = date('l', strtotime($selectedDate));

// Query class_timetable for this room and day
$schedule = [];
$classSchedules = $db->class_timetable->find([
    'room_id' => $room['_id'],  // Use _id for class_timetable collection since it stores room's _id
    'day_of_week' => $selectedDayOfWeek
])->toArray();
foreach ($classSchedules as $class) {
    $schedule[] = [
        'start_time' => $class['start_time'],
        'end_time' => $class['end_time'],
        'type' => 'Class'
    ];
}

// Query bookings for this room and day (if you have a bookings collection)
$bookings = $db->bookings->find([
    'room_id' => $room['_id'],  // Use _id for bookings collection since that's what's stored
    'booking_date' => $selectedDate, 
    'status' => 'approved'
])->toArray();
foreach ($bookings as $booking) {
    $startTime = $booking['start_time'];
    $endTime = $booking['end_time'];
    $timeSlot = $startTime . ' - ' . $endTime;
    $schedule[] = [
        'start_time' => $startTime,
        'end_time' => $endTime,
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
    'booking_date' => $selectedDate,
    'status' => 'approved'
])->toArray();

$pendingBookings = $db->bookings->find([
    'room_id' => $room['_id'],
    'booking_date' => $selectedDate,
    'status' => 'pending',
    'student_id' => $_SESSION['student_id']
])->toArray();

// Mark slots as Occupied by class timetable
foreach ($timeSlots as $idx => $slot) {
    list($slotStart, $slotEnd) = explode(' - ', $slot);
    $slotStartDateTime = new DateTime($selectedDate . ' ' . $slotStart);
    $slotEndDateTime = new DateTime($selectedDate . ' ' . $slotEnd);

    // Check for class overlap
    foreach ($classSchedules as $class) {
        $classStartDateTime = new DateTime($selectedDate . ' ' . $class['start_time']);
        $classEndDateTime = new DateTime($selectedDate . ' ' . $class['end_time']);
        if ($slotStartDateTime < $classEndDateTime && $slotEndDateTime > $classStartDateTime) {
            $availabilityStatus[$idx] = 'Occupied';
            break; // No need to check further if already occupied
        }
    }
}

// Mark slots as Booked by bookings (if not already Occupied)
foreach ($timeSlots as $idx => $slot) {
    if ($availabilityStatus[$idx] === 'Occupied') continue;
    list($slotStart, $slotEnd) = explode(' - ', $slot);
    $slotStartDateTime = new DateTime($selectedDate . ' ' . $slotStart);
    $slotEndDateTime = new DateTime($selectedDate . ' ' . $slotEnd);

    foreach ($approvedBookings as $booking) {
        $bookingStartDateTime = new DateTime($selectedDate . ' ' . $booking['start_time']);
        $bookingEndDateTime = new DateTime($selectedDate . ' ' . $booking['end_time']);
        if ($slotStartDateTime < $bookingEndDateTime && $slotEndDateTime > $bookingStartDateTime) {
            $availabilityStatus[$idx] = 'Booked';
            break;
        }
    }
}

// Also process evening time slots for class schedules
$eveningAvailability = array_fill(0, 12, 'Available');

// Process evening class schedules first
foreach ($classSchedules as $class) {
    $classStart = isset($class['start_time']) ? $class['start_time'] : '';
    $classEnd = isset($class['end_time']) ? $class['end_time'] : '';
    if (!empty($classStart) && !empty($classEnd)) {
        foreach ($eveningTimeSlots as $idx => $slot) {
            list($slotStart, $slotEnd) = explode(' - ', $slot);
            // If the slot and the class overlap, mark as occupied
            if (
                ($classStart < $slotEnd) && ($classEnd > $slotStart)
            ) {
                $eveningAvailability[$idx] = 'Occupied';
            }
        }
    }
}

// Process evening approved bookings
foreach ($approvedBookings as $booking) {
    $startTime = isset($booking['start_time']) ? $booking['start_time'] : '';
    $endTime = isset($booking['end_time']) ? $booking['end_time'] : '';
    
    if (!empty($startTime) && !empty($endTime)) {
        $timeSlot = $startTime . ' - ' . $endTime;
        if (isset($eveningTimeSlotMap[$timeSlot]) && $eveningAvailability[$eveningTimeSlotMap[$timeSlot]] !== 'Occupied') {
            $eveningAvailability[$eveningTimeSlotMap[$timeSlot]] = 'Booked';
        }
    }
}

// Process evening pending bookings by current user
foreach ($pendingBookings as $booking) {
    $startTime = isset($booking['start_time']) ? $booking['start_time'] : '';
    $endTime = isset($booking['end_time']) ? $booking['end_time'] : '';
    
    if (!empty($startTime) && !empty($endTime)) {
        $timeSlot = $startTime . ' - ' . $endTime;
        if (isset($eveningTimeSlotMap[$timeSlot]) && $eveningAvailability[$eveningTimeSlotMap[$timeSlot]] === 'Available') {
            $eveningAvailability[$eveningTimeSlotMap[$timeSlot]] = 'Requested';
        }
    }
}

// Check if room is currently available for selected date
$isAvailable = true;
date_default_timezone_set('Asia/Kuala_Lumpur');
$currentDate = date('Y-m-d');
$currentTime = date('H:i');
$currentDateTime = new DateTime("$currentDate $currentTime");

function normalizeTime($time) {
    $parts = explode(':', $time);
    return sprintf('%02d:%02d', (int)$parts[0], (int)($parts[1] ?? 0));
}

if ($selectedDate === $currentDate) {
    // Check bookings and class schedules in a single loop
    foreach (array_merge($approvedBookings, $classSchedules) as $entry) {
        $startField = isset($entry['booking_date']) ? $entry['start_time'] : $entry['start_time'];
        $endField = isset($entry['booking_date']) ? $entry['end_time'] : $entry['end_time'];
        $dateField = isset($entry['booking_date']) ? $entry['booking_date'] : $selectedDate;
        $start = new DateTime("$dateField " . normalizeTime($startField));
        $end = new DateTime("$dateField " . normalizeTime($endField));
        if ($currentDateTime >= $start && $currentDateTime < $end) {
            $isAvailable = false;
            break;
        }
    }
} else {
    $isAvailable = in_array('Available', $availabilityStatus) || in_array('Available', $eveningAvailability);
}

// Add this function for display conversion if not present
if (!function_exists('to12Hour')) {
    function to12Hour($time) {
        return date('g:i A', strtotime($time));
    }
}

$isUnderMaintenance = (isset($room['status']) && strtolower($room['status']) !== 'available');
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
    color: #FF3B30;
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
    color: #FF3B30;
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
                            <div class="status-label <?php echo $isUnderMaintenance ? 'occupied' : ($isAvailable ? '' : 'occupied'); ?>">
                                <?php
                                if ($isUnderMaintenance) {
                                    echo 'Under Maintenance';
                                } else {
                                    echo $isAvailable ? 'Currently Available' : 'Currently Occupied';
                                }
                                ?>
                        </div>
                    </div>
                </div>
                <div class="status-indicator">
                        <div class="indicator-dot <?php echo $isUnderMaintenance ? 'occupied' : ($isAvailable ? '' : 'occupied'); ?>">
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
<?php if (!$isUnderMaintenance): ?>
    <div class="extended-schedule">
        <div class="extended-schedule-title">
            <div class="text"><?php echo date('F j, Y', strtotime($selectedDate)); ?> Schedule</div>
        </div>

        <!-- Day Schedule -->
        <div class="extended-schedule-container">
            <div class="extended-schedule-grid">
                <div class="extended-schedule-header">
                    <div class="extended-schedule-header-row">
                    <?php foreach ($timeSlots as $slot): ?>
                            <?php list($start, $end) = explode(' - ', $slot); ?>
                            <div class="extended-time-slot-header">
                                <div class="text"><?php echo to12Hour($start) . ' - ' . to12Hour($end); ?></div>
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
                            <?php list($start, $end) = explode(' - ', $slot); ?>
                            <div class="extended-time-slot-header">
                                <div class="text"><?php echo to12Hour($start) . ' - ' . to12Hour($end); ?></div>
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
<?php endif; ?>

        <!-- Bottom Book Button -->
<?php if (!$isUnderMaintenance): ?>
    <div class="bottom-button-container">
        <div class="bottom-button-wrapper">
            <button class="bottom-book-button" onclick="bookThisRoom('<?php echo htmlspecialchars($room['_id']); ?>')">
                <div class="text">Book this room</div>
            </button>
        </div>
    </div>
<?php endif; ?>
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
    // Format date as YYYY-MM-DD without timezone issues
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const dateString = `${year}-${month}-${day}`;

    // Reload the page with the new date parameter
    const url = new URL(window.location);
    url.searchParams.set('date', dateString);
    window.location.href = url.toString();
}

function previousMonth() {
    currentCalendarDate.setMonth(currentCalendarDate.getMonth() - 1);
    updateCalendar();
}

function nextMonth() {
    currentCalendarDate.setMonth(currentCalendarDate.getMonth() + 1);
    updateCalendar();
}

function bookThisRoom(roomId) {
    // Get the selected date from the calendar
    const dateString = selectedCalendarDate.toISOString().split('T')[0];
    
    // Build the URL with room_id and date parameters
    let url = 'booking.php?room_id=' + encodeURIComponent(roomId);
    if (dateString) {
        url += '&date=' + encodeURIComponent(dateString);
    }
    
    window.location.href = url;
}

function goBack() {
    window.location.href = 'roomavailability.php';
}

// Initialize calendar when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeCalendar();
});
</script>

<?php include_once 'component/footer.php'; ?>
