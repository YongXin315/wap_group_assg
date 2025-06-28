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
    $room = $db->rooms->findOne(['room_name' => $roomId]);
    if (!$room) {
        // Fallback to static data if room not found
        $room = [
            'room_name' => $roomId,
            'type' => 'Classroom',
            'block' => 'Block D',
            'floor' => 'Level 8',
            'capacity' => '30-40 persons',
            'amenities' => 'Whiteboard, Projector, Air-conditioning',
            'class_timetable' => []
        ];
    } else {
        // Convert MongoDB document to array and ensure all required fields exist with fallback values
        $roomArray = $room->getArrayCopy();
        $room = array_merge([
            'room_name' => $roomId,
            'type' => 'Classroom',
            'block' => 'Block D',
            'floor' => 'Level 8',
            'capacity' => '30-40 persons',
            'amenities' => 'Whiteboard, Projector, Air-conditioning',
            'class_timetable' => []
        ], $roomArray);
    }
} catch (Exception $e) {
    // Fallback to static data if database error
    $room = [
        'room_name' => $roomId,
        'type' => 'Classroom',
        'block' => 'Block D',
        'floor' => 'Level 8',
        'capacity' => '30-40 persons',
        'amenities' => 'Whiteboard, Projector, Air-conditioning',
        'class_timetable' => []
    ];
}

// Handle form submission
$bookingMessage = '';
$bookingError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentId = $_POST['student_id'] ?? '';
    $fullName = $_POST['full_name'] ?? '';
    $bookingDate = $_POST['booking_date'] ?? '';
    $startTime = $_POST['start_time'] ?? '';
    $endTime = $_POST['end_time'] ?? '';
    $purpose = $_POST['purpose'] ?? '';
    $numPeople = $_POST['num_people'] ?? '';
    
    if (empty($studentId) || empty($fullName) || empty($bookingDate) || empty($startTime) || empty($endTime) || empty($purpose) || empty($numPeople)) {
        $bookingError = 'Please fill in all required fields.';
    } else {
        // Here you would typically save the booking to the database
        // For now, we'll just show a success message
        $bookingMessage = 'Booking request submitted successfully! We will confirm your booking shortly.';
        
        // Redirect back to room details after 3 seconds
        header("refresh:3;url=roomdetails.php?room_id=" . urlencode($roomId) . "&date=" . urlencode($bookingDate));
    }
}

// Get current date and time
$currentDate = date('Y-m-d');
$currentTime = date('H:i');
$selectedDate = $_GET['date'] ?? date('Y-m-d');
$selectedTime = $_GET['time'] ?? date('H:i');

// Extract hour from selected time for default start time
$selectedHour = (int)date('H', strtotime($selectedTime));
$defaultStartTime = sprintf('%02d:00', $selectedHour);

// Get day of week for the selected date
$selectedDayOfWeek = date('l', strtotime($selectedDate));
$isCurrentDate = ($selectedDate === $currentDate);

// Determine time range based on room type and day of week
function getTimeRange($roomType, $dayOfWeek, $isCurrentDate, $currentHour) {
    $isDiscussionRoom = (stripos($roomType, 'discussion') !== false);
    
    if ($isDiscussionRoom) {
        if ($dayOfWeek === 'Saturday') {
            $startHour = 9; // 9 AM
            $endHour = 17;  // 5 PM
        } else {
            $startHour = 8; // 8 AM
            $endHour = 21;  // 9 PM
        }
    } else {
        $startHour = 8; // 8 AM
        $endHour = 22;  // 10 PM
    }
    
    // If it's current date, start from current hour or next hour
    if ($isCurrentDate) {
        $startHour = max($startHour, $currentHour);
        // If current time is past closing time, start from opening time next day
        if ($currentHour >= $endHour) {
            $startHour = $startHour; // This will be handled in the calling code
        }
    }
    
    return ['start' => $startHour, 'end' => $endHour];
}

$timeRange = getTimeRange($room['type'], $selectedDayOfWeek, $isCurrentDate, (int)date('H'));
$startHour = $timeRange['start'];
$endHour = $timeRange['end'];

// Adjust start hour if a specific time was passed
if (isset($_GET['time'])) {
    $startHour = max($startHour, $selectedHour);
}

// If current time is past closing time and it's today, don't allow booking
if ($isCurrentDate && (int)date('H') >= $endHour) {
    $startHour = $endHour; // This will result in no available times
}

// Function to get availability for a specific date
function getAvailabilityForDate($date, $roomName) {
    $dayOfWeek = date('l', strtotime($date));
    $dayOfMonth = date('j', strtotime($date));
    
    if ($dayOfWeek === 'Sunday' || $dayOfWeek === 'Saturday') {
        $availabilityStatus = array_fill(0, 12, 'Available'); // Available on weekends
    } elseif ($dayOfMonth % 3 === 0) {
        $availabilityStatus = ['Available', 'Booked', 'In Use', 'Available', 'Available', 'Booked', 'Booked', 'Booked', 'Booked', 'Available', 'Available', 'Booked'];
    } else {
        $availabilityStatus = ['Available', 'Available', 'Available', 'Booked', 'Available', 'Available', 'Booked', 'Available', 'Available', 'Available', 'Available', 'Available'];
    }
    
    return $availabilityStatus;
}

$eveningAvailability = array_fill(0, 12, 'Available');
?>

<?php include_once 'component/header.php'; ?>

<style>
.summary-value.clickable {
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    display: inline-block;
}

.summary-value.clickable:hover {
    color: #2196F3;
    text-decoration: underline;
    transform: translateY(-1px);
}

.summary-value.clickable::after {
    content: "View Details";
    position: absolute;
    top: -25px;
    left: 50%;
    transform: translateX(-50%);
    background: rgba(33, 150, 243, 0.9);
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.3s ease;
    white-space: nowrap;
    z-index: 1000;
}

.summary-value.clickable:hover::after {
    opacity: 1;
}

.summary-item {
    position: relative;
}

.main-container {
    align-self: stretch;
    height: 1205px;
    min-height: 800px;
    background: white;
    overflow: hidden;
    flex-direction: column;
    justify-content: flex-start;
    align-items: flex-start;
    display: inline-flex;
    width: 100%;
}

.content-wrapper {
    align-self: stretch;
    height: 566px;
    flex-direction: column;
    justify-content: flex-start;
    align-items: flex-start;
    display: flex;
}

.main-content {
    align-self: stretch;
    height: 586px;
    padding-left: 24px;
    padding-right: 24px;
    padding-top: 20px;
    padding-bottom: 20px;
    justify-content: center;
    align-items: flex-start;
    gap: 4px;
    display: inline-flex;
}

.content-container {
    flex: 1 1 0;
    height: 546px;
    max-width: 920px;
    overflow: hidden;
    flex-direction: column;
    justify-content: flex-start;
    align-items: flex-start;
    display: inline-flex;
}

.sidebar {
    width: 360px;
    height: 519px;
    overflow: hidden;
    flex-direction: column;
    justify-content: flex-start;
    align-items: flex-start;
    display: inline-flex;
}
</style>

<div class="main-container">
    <div class="content-wrapper">
        <div class="main-content">
            <div class="content-container">
                <!-- Page Title -->
                <div class="page-title-section">
                    <div class="page-title">Confirm Room Booking</div>
                </div>

                <!-- Booking Summary Section -->
                <div class="summary-section">
                    <div class="section-title">Selected Booking Summary</div>
                    <div class="summary-content">
                        <div class="summary-row">
                            <div class="summary-item">
                                <div class="summary-label">Selected Room</div>
                                <div class="summary-value clickable" onclick="viewDetails('<?php echo htmlspecialchars($room['room_name']); ?>')"><?php echo htmlspecialchars($room['room_name']); ?></div>
                            </div>
                            <div class="summary-item">
                                <div class="summary-label">Selected Date</div>
                                <div class="summary-value-container">
                                    <div class="summary-value" id="summary-date-value"><?php echo date('F j, Y', strtotime($selectedDate)); ?></div>
                                    <div class="calendar-preview" id="calendar-preview">
                                        <div class="calendar-header">
                                            <div class="calendar-nav" onclick="previousMonth()">‹</div>
                                            <div class="calendar-title" id="calendar-title">Next 7 Days</div>
                                            <div class="calendar-nav" onclick="nextMonth()">›</div>
                                        </div>
                                        <div class="calendar-grid">
                                            <div class="calendar-days">
                                                <div class="day-header">S</div>
                                                <div class="day-header">M</div>
                                                <div class="day-header">T</div>
                                                <div class="day-header">W</div>
                                                <div class="day-header">T</div>
                                                <div class="day-header">F</div>
                                                <div class="day-header">S</div>
                                            </div>
                                            <div class="calendar-week" id="calendar-dates">
                                                <!-- Calendar dates will be populated by JavaScript -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Student Details Section -->
                <div class="student-details-section">
                    <div class="section-title">Student Details</div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="student_id" class="form-label">Student ID</label>
                            <input type="text" id="student_id" name="student_id" class="form-input" 
                                   placeholder="e.g., 0312345" value="<?php echo htmlspecialchars($_SESSION['student_id'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" id="full_name" name="full_name" class="form-input" 
                                   placeholder="Enter your full name" value="<?php echo htmlspecialchars($_SESSION['student_name'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>

                <!-- Booking Details Section -->
                <div class="booking-details-section">
                    <div class="section-title">Booking Details</div>
                    
                    <!-- Date Selection -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="booking_date" class="form-label">Date</label>
                            <input type="date" id="booking_date" name="booking_date" class="form-input" 
                                   value="<?php echo htmlspecialchars($selectedDate); ?>" 
                                   min="<?php echo $currentDate; ?>" required>
                        </div>
                    </div>
                    
                    <!-- Time Selection -->
                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_time" class="form-label">Start Time</label>
                            <select id="start_time" name="start_time" class="form-select" required>
                                <option value="">Select start time</option>
                                <?php
                                // Generate time options based on room type and day
                                for ($hour = $startHour; $hour <= $endHour; $hour++) {
                                    $time = sprintf('%02d:00', $hour);
                                    $displayTime = date('g:i A', strtotime($time));
                                    $selected = ($time === $defaultStartTime && $hour >= $startHour) ? 'selected' : '';
                                    echo "<option value=\"$time\" $selected>$displayTime</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="end_time" class="form-label">End Time</label>
                            <select id="end_time" name="end_time" class="form-select" required>
                                <option value="">Select end time</option>
                                <?php
                                // End time starts from start time + 1 hour, up to closing time
                                $defaultEndHour = min($endHour, $selectedHour + 1);
                                
                                // Generate time options from start time + 1 hour to closing time
                                for ($hour = $defaultEndHour; $hour <= $endHour; $hour++) {
                                    $time = sprintf('%02d:00', $hour);
                                    $displayTime = date('g:i A', strtotime($time));
                                    $selected = ($hour === $defaultEndHour) ? 'selected' : '';
                                    echo "<option value=\"$time\" $selected>$displayTime</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Availability Status -->
                    <div class="form-row">
                        <div class="availability-status-display" id="availability-status">
                            <div class="status-message">Select a date and time to check availability</div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="purpose" class="form-label">Purpose</label>
                            <input type="text" id="purpose" name="purpose" class="form-input" 
                                   placeholder="Enter the Purpose" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="num_people" class="form-label">Number of People</label>
                            <input type="number" id="num_people" name="num_people" class="form-input" 
                                   min="1" max="50" placeholder="Enter number of people" required>
                        </div>
                    </div>
                </div>

                <!-- Success/Error Messages -->
                <?php if ($bookingMessage): ?>
                    <div class="success-message">
                        <?php echo htmlspecialchars($bookingMessage); ?>
                    </div>
                <?php endif; ?>

                <?php if ($bookingError): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($bookingError); ?>
                    </div>
                <?php endif; ?>

                <!-- Submit Button -->
                <div class="submit-section">
                    <button type="submit" class="submit-button" form="booking-form">Submit Booking</button>
                </div>

                <!-- Cancel Link -->
                <div class="cancel-section">
                    <a href="roomdetails.php?room_id=<?php echo urlencode($roomId); ?>" class="cancel-link">← Cancel</a>
                </div>
            </div>
            <div class="sidebar">
                <!-- Sidebar content -->
            </div>
        </div>
    </div>
    <div class="extended-schedule">
        <!-- Extended schedule content -->
    </div>
    <div class="bottom-button-container">
        <!-- Bottom button -->
    </div>
</div>

<!-- Hidden Form for Submission -->
<form id="booking-form" method="POST" style="display: none;">
    <input type="hidden" name="student_id" id="hidden_student_id">
    <input type="hidden" name="full_name" id="hidden_full_name">
    <input type="hidden" name="booking_date" id="hidden_booking_date">
    <input type="hidden" name="start_time" id="hidden_start_time">
    <input type="hidden" name="end_time" id="hidden_end_time">
    <input type="hidden" name="purpose" id="hidden_purpose">
    <input type="hidden" name="num_people" id="hidden_num_people">
</form>

<?php include_once 'component/footer.php'; ?> 

<script>
// Auto-fill hidden form when visible inputs change
document.getElementById('student_id').addEventListener('input', function() {
    document.getElementById('hidden_student_id').value = this.value;
});

document.getElementById('full_name').addEventListener('input', function() {
    document.getElementById('hidden_full_name').value = this.value;
});

document.getElementById('booking_date').addEventListener('input', function() {
    document.getElementById('hidden_booking_date').value = this.value;
    updateSummaryDate();
    updateCalendar();
    checkAvailability();
    updateTimeOptions();
});

function updateSummaryDate() {
    const selectedDate = document.getElementById('booking_date').value;
    if (selectedDate) {
        const dateObj = new Date(selectedDate);
        const formattedDate = dateObj.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
        document.getElementById('summary-date-value').textContent = formattedDate;
    }
}

function updateTimeOptions() {
    const selectedDate = document.getElementById('booking_date').value;
    const currentDate = new Date().toISOString().split('T')[0];
    const currentHour = new Date().getHours();
    const isCurrentDate = (selectedDate === currentDate);
    
    // Get room type from PHP variable
    const roomType = '<?php echo htmlspecialchars($room['type']); ?>';
    
    // Get day of week for selected date
    const dateObj = new Date(selectedDate);
    const dayOfWeek = dateObj.toLocaleDateString('en-US', { weekday: 'long' });
    
    // Determine time range based on room type and day of week
    function getTimeRange(roomType, dayOfWeek, isCurrentDate, currentHour) {
        const isDiscussionRoom = roomType.toLowerCase().includes('discussion');
        
        let startHour, endHour;
        
        if (isDiscussionRoom) {
            if (dayOfWeek === 'Saturday') {
                startHour = 9; // 9 AM
                endHour = 17;  // 5 PM
            } else {
                startHour = 8; // 8 AM
                endHour = 21;  // 9 PM
            }
        } else {
            startHour = 8; // 8 AM
            endHour = 22;  // 10 PM
        }
        
        // If it's current date, start from current hour
        if (isCurrentDate) {
            startHour = Math.max(startHour, currentHour);
        }
        
        return { start: startHour, end: endHour };
    }
    
    const timeRange = getTimeRange(roomType, dayOfWeek, isCurrentDate, currentHour);
    const startHour = timeRange.start;
    const endHour = timeRange.end;
    
    // Get start time select
    const startTimeSelect = document.getElementById('start_time');
    const endTimeSelect = document.getElementById('end_time');
    
    // Clear existing options
    startTimeSelect.innerHTML = '<option value="">Select start time</option>';
    endTimeSelect.innerHTML = '<option value="">Select end time</option>';
    
    // Generate start time options
    for (let hour = startHour; hour <= endHour; hour++) {
        const time = hour.toString().padStart(2, '0') + ':00';
        const displayTime = new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
        
        const option = document.createElement('option');
        option.value = time;
        option.textContent = displayTime;
        if (hour === startHour) {
            option.selected = true;
        }
        startTimeSelect.appendChild(option);
    }
    
    // Generate end time options
    for (let hour = startHour + 1; hour <= endHour; hour++) {
        const time = hour.toString().padStart(2, '0') + ':00';
        const displayTime = new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        });
        
        const option = document.createElement('option');
        option.value = time;
        option.textContent = displayTime;
        if (hour === startHour + 1) {
            option.selected = true;
        }
        endTimeSelect.appendChild(option);
    }
    
    // Update hidden form values
    document.getElementById('hidden_start_time').value = startTimeSelect.value;
    document.getElementById('hidden_end_time').value = endTimeSelect.value;
    
    // Update end time options based on selected start time
    updateEndTimeOptions();
}

document.getElementById('purpose').addEventListener('input', function() {
    document.getElementById('hidden_purpose').value = this.value;
});

document.getElementById('num_people').addEventListener('input', function() {
    document.getElementById('hidden_num_people').value = this.value;
});

// Calendar functionality
let currentCalendarDate = new Date();

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
        
        for (let day = 0; day < 7; day++) {
            const currentDate = new Date(startDate);
            currentDate.setDate(startDate.getDate() + (week * 7) + day);
            
            const dayDiv = document.createElement('div');
            const dayNumber = currentDate.getDate();
            const isCurrentMonth = currentDate.getMonth() === currentCalendarDate.getMonth();
            const isToday = currentDate.toDateString() === new Date().toDateString();
            const isSelected = currentDate.toDateString() === new Date(document.getElementById('booking_date').value).toDateString();
            const isPast = currentDate < new Date().setHours(0, 0, 0, 0);
            
            dayDiv.className = 'calendar-day';
            if (!isCurrentMonth) dayDiv.className += ' other-month';
            if (isToday) dayDiv.className += ' today';
            if (isSelected) dayDiv.className += ' selected';
            if (isPast) dayDiv.className += ' past';
            
            dayDiv.textContent = dayNumber;
            dayDiv.onclick = function() {
                if (!isPast) {
                    selectCalendarDate(currentDate);
                }
            };
            
            weekDiv.appendChild(dayDiv);
        }
        
        calendarDates.appendChild(weekDiv);
    }
}

function selectCalendarDate(date) {
    const dateString = date.toISOString().split('T')[0];
    document.getElementById('booking_date').value = dateString;
    document.getElementById('hidden_booking_date').value = dateString;
    
    // Update summary date
    updateSummaryDate();
    
    // Update calendar selection
    updateCalendar();
    
    // Update time options
    updateTimeOptions();
    
    // Check availability
    checkAvailability();
}

function previousMonth() {
    currentCalendarDate.setMonth(currentCalendarDate.getMonth() - 1);
    updateCalendar();
}

function nextMonth() {
    currentCalendarDate.setMonth(currentCalendarDate.getMonth() + 1);
    updateCalendar();
}

// Availability checking
function checkAvailability() {
    const selectedDate = document.getElementById('booking_date').value;
    const startTime = document.getElementById('start_time').value;
    const endTime = document.getElementById('end_time').value;
    
    const statusDisplay = document.getElementById('availability-status');
    const statusMessage = statusDisplay.querySelector('.status-message');
    
    if (!selectedDate || !startTime || !endTime) {
        statusMessage.textContent = 'Select a date and time to check availability';
        statusMessage.className = 'status-message';
        return;
    }
    
    // Show checking status
    statusMessage.textContent = 'Checking availability...';
    statusMessage.className = 'status-message checking';
    
    // Simulate availability check (replace with actual database query)
    setTimeout(() => {
        const availabilityStatus = getAvailabilityForDate($selectedDate, $room['room_name']);
        const isAvailable = !in_array('Booked', $availabilityStatus) && !in_array('In Use', $availabilityStatus);
        
        if (isAvailable) {
            statusMessage.textContent = '✓ Time slot is available for booking';
            statusMessage.className = 'status-message available';
        } else {
            statusMessage.textContent = '✗ Time slot is not available (already booked or in use)';
            statusMessage.className = 'status-message unavailable';
        }
    }, 500);
}

function checkTimeSlotAvailability(date, startTime, endTime) {
    // This function would typically query the database
    // For now, we'll use the same logic as in roomdetails.php
    
    const dateObj = new Date(date);
    const dayOfWeek = dateObj.getDay();
    const dayOfMonth = dateObj.getDate();
    
    // Convert times to hour numbers for comparison
    const startHour = parseInt(startTime.split(':')[0]);
    const endHour = parseInt(endTime.split(':')[0]);
    
    // Sample availability logic
    if (dayOfWeek === 0 || dayOfWeek === 6) { // Weekend
        return true; // Available on weekends
    } else if (dayOfMonth % 3 === 0) { // Every 3rd day has some bookings
        // Check if the selected time range conflicts with booked times
        const bookedHours = [9, 11, 13, 14, 15, 16, 19]; // Sample booked hours
        for (let hour = startHour; hour < endHour; hour++) {
            if (bookedHours.includes(hour)) {
                return false;
            }
        }
        return true;
    } else {
        // Check if the selected time range conflicts with booked times
        const bookedHours = [11, 14]; // Sample booked hours
        for (let hour = startHour; hour < endHour; hour++) {
            if (bookedHours.includes(hour)) {
                return false;
            }
        }
        return true;
    }
}

// Initialize calendar when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeCalendar();
    
    // Set default values for hidden form
    document.getElementById('hidden_booking_date').value = document.getElementById('booking_date').value;
    document.getElementById('hidden_start_time').value = document.getElementById('start_time').value;
    document.getElementById('hidden_end_time').value = document.getElementById('end_time').value;
});

document.getElementById('start_time').addEventListener('input', function() {
    document.getElementById('hidden_start_time').value = this.value;
    updateEndTimeOptions();
    checkAvailability();
});

document.getElementById('end_time').addEventListener('input', function() {
    document.getElementById('hidden_end_time').value = this.value;
    checkAvailability();
});

function updateEndTimeOptions() {
    const selectedDate = document.getElementById('booking_date').value;
    const startTime = document.getElementById('start_time').value;
    const currentDate = new Date().toISOString().split('T')[0];
    const currentHour = new Date().getHours();
    const isCurrentDate = (selectedDate === currentDate);
    
    // Get room type from PHP variable
    const roomType = '<?php echo htmlspecialchars($room['type']); ?>';
    
    // Get day of week for selected date
    const dateObj = new Date(selectedDate);
    const dayOfWeek = dateObj.toLocaleDateString('en-US', { weekday: 'long' });
    
    // Determine time range based on room type and day of week
    function getTimeRange(roomType, dayOfWeek, isCurrentDate, currentHour) {
        const isDiscussionRoom = roomType.toLowerCase().includes('discussion');
        
        let startHour, endHour;
        
        if (isDiscussionRoom) {
            if (dayOfWeek === 'Saturday') {
                startHour = 9; // 9 AM
                endHour = 17;  // 5 PM
            } else {
                startHour = 8; // 8 AM
                endHour = 21;  // 9 PM
            }
        } else {
            startHour = 8; // 8 AM
            endHour = 22;  // 10 PM
        }
        
        return { start: startHour, end: endHour };
        }

    const timeRange = getTimeRange(roomType, dayOfWeek, isCurrentDate, currentHour);
    const endHour = timeRange.end;
    
    // Get end time select
    const endTimeSelect = document.getElementById('end_time');
    
    // Clear existing options
    endTimeSelect.innerHTML = '<option value="">Select end time</option>';
    
    if (startTime) {
        // Extract hour from start time
        const startHour = parseInt(startTime.split(':')[0]);
        const minEndHour = startHour + 1; // At least 1 hour after start time
        
        // Generate end time options from start time + 1 hour to closing time
        for (let hour = minEndHour; hour <= endHour; hour++) {
            const time = hour.toString().padStart(2, '0') + ':00';
            const displayTime = new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
            
            const option = document.createElement('option');
            option.value = time;
            option.textContent = displayTime;
            if (hour === minEndHour) {
                option.selected = true;
            }
            endTimeSelect.appendChild(option);
        }
        
        // Update hidden form value
        document.getElementById('hidden_end_time').value = endTimeSelect.value;
    }
}

function viewDetails(roomName) {
    // Get the selected date from the booking form
    const selectedDate = document.getElementById('booking_date').value;
    
    // Redirect to room details page with room name and selected date
    let url = 'roomdetails.php?room_id=' + encodeURIComponent(roomName);
    if (selectedDate) {
        url += '&date=' + encodeURIComponent(selectedDate);
    }
    window.location.href = url;
}
</script>

<div class="breadcrumb-section">
    <div class="breadcrumb-item">
        <div class="text">Live View</div>
    </div>
    <div class="breadcrumb-item">
        <div class="text">/</div>
    </div>
    <div class="breadcrumb-item current">
        <div class="text"><?php echo htmlspecialchars($room['room_name']); ?></div>
    </div>
</div>

<div class="room-details">
    <div class="detail-row">
        <div class="detail-item block">
            <div class="detail-label">
                <div class="text">Block</div>
            </div>
            <div class="detail-value">
                <div class="text"><?php echo htmlspecialchars($room['block']); ?></div>
            </div>
        </div>
        <div class="detail-item floor">
            <div class="detail-label">
                <div class="text">Floor</div>
            </div>
            <div class="detail-value">
                <div class="text"><?php echo htmlspecialchars($room['floor']); ?></div>
            </div>
        </div>
    </div>
    <!-- Repeat for Type, Capacity, Amenities -->
</div>

<div class="schedule-container">
    <div class="schedule-grid">
        <div class="schedule-header">
            <div class="schedule-header-row">
                <?php foreach ($timeSlots as $slot): ?>
                    <div class="time-slot-header">
                        <div class="text"><?php echo str_replace(' - ', '<br/>-<br/>', $slot); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="schedule-status">
            <div class="schedule-status-row">
                <?php foreach ($availabilityStatus as $status): ?>
                    <div class="status-cell <?php echo strtolower(str_replace(' ', '-', $status)); ?>">
                        <div class="text"><?php echo $status; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="timeline-container">
    <?php foreach ($timeSlots as $index => $slot): ?>
        <div class="timeline-item">
            <div class="timeline-icon">
                <div class="timeline-line"></div>
            </div>
            <div class="timeline-content">
                <div class="timeline-time">
                    <div class="text"><?php echo $slot; ?></div>
                </div>
                <div class="timeline-status">
                    <div class="text"><?php echo $availabilityStatus[$index]; ?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

