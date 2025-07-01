<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
            '_id' => $roomId,
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

$roomIdForQuery = isset($room['_id']) ? $room['_id'] : $roomId;

// Handle form submission
$bookingMessage = '';
$bookingError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // var_dump($_POST);
    // exit;

    // Get form data
    $studentId = $_POST['student_id'] ?? '';
    $fullName = $_POST['full_name'] ?? '';
    $bookingDate = $_POST['booking_date'] ?? '';
    $startTime = $_POST['start_time'] ?? '';
    $endTime = $_POST['end_time'] ?? '';
    $purpose = $_POST['purpose'] ?? '';
    $numPeople = $_POST['num_people'] ?? '';
    $roomId = $_GET['room_id'] ?? '';

    // Validate required fields
    if (empty($studentId) || empty($fullName) || empty($bookingDate) || empty($startTime) || empty($endTime) || empty($purpose) || empty($numPeople) || empty($roomId)) {
        $bookingError = 'Please fill in all required fields.';
    } else {
        // Prepare booking data
        $status = (stripos($room['type'], 'Discussion Room') !== false) ? 'approved' : 'pending';

        $bookingData = [
            'student_id' => $studentId,
            'full_name' => $fullName,
            'room_id' => $roomId,
            'booking_date' => $bookingDate,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'purpose' => $purpose,
            'num_people' => (int)$numPeople,
            'created_at' => new MongoDB\BSON\UTCDateTime(),
            'status' => $status
        ];

        // Optionally, add day_of_week for easier queries
        $bookingData['day_of_week'] = date('l', strtotime($bookingDate));

        try {
            $db->bookings->insertOne($bookingData);
            if ($status === 'approved') {
                $bookingMessage = 'Your discussion room booking is auto-approved! You may use the room at the selected time.';
            } else {
                $bookingMessage = 'Booking request submitted successfully! We will confirm your booking shortly.';
            }
            if ($bookingMessage) {
                header("refresh:3;url=roomdetails.php?room_id=" . urlencode($roomId) . "&date=" . urlencode($bookingDate));
            }
        } catch (Exception $e) {
            $bookingError = 'Failed to save booking: ' . $e->getMessage();
        }
    }
}

// Set Malaysia timezone & "now"
date_default_timezone_set('Asia/Kuala_Lumpur');
$currentDate = date('Y-m-d');
$currentTime = date('H:i');

// If both date & time are passed, use them; otherwise fall back to "now"
$selectedDate = $_GET['date'] ?? date('Y-m-d');
$selectedTime = $_GET['time'] ?? date('H:i');
$selectedHour      = (int) substr($selectedTime, 0, 2);
$defaultStartTime  = sprintf('%02d:00', $selectedHour);

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

$cardClickable = true; // or your actual logic

$onclick = ($cardClickable)
    ? 'onclick="bookRoom(\'' . htmlspecialchars($room['_id']) . '\')"'
    : '';
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
</style>

<div class="main-container confirm-booking">
    <div class="content-wrapper confirm-booking">
        <div class="booking-container">
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
                            <div class="summary-value clickable" id="room-name-link">
                                <?php echo htmlspecialchars($room['room_name']); ?>
                            </div>
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
                            <option value="" selected>Select end time</option>
                            <?php
                            // End time starts from start time + 1 hour, up to closing time
                            $defaultEndHour = min($endHour, $selectedHour + 1);

                            // Generate time options from start time + 1 hour to closing time
                            for ($hour = $defaultEndHour; $hour <= $endHour; $hour++) {
                                $time = sprintf('%02d:00', $hour);
                                $displayTime = date('g:i A', strtotime($time));
                                // Do NOT set selected here unless you want to pre-select a value
                                echo "<option value=\"$time\">$displayTime</option>";
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
                <button type="submit" class="submit-button">Submit Booking</button>
            </div>

            <!-- Cancel Link -->
            <div class="cancel-section">
                <a href="roomdetails.php?room_id=<?php echo urlencode($roomId); ?>" class="cancel-link">← Cancel</a>
            </div>
        </div>
    </div>
</div>


<form id="booking-form" method="POST" style="display: none;">
    <input type="hidden" name="student_id" id="hidden_student_id">
    <input type="hidden" name="full_name" id="hidden_full_name">
    <input type="hidden" name="booking_date" id="hidden_booking_date">
    <input type="hidden" name="start_time" id="hidden_start_time">
    <input type="hidden" name="end_time" id="hidden_end_time">
    <input type="hidden" name="purpose" id="hidden_purpose">
    <input type="hidden" name="num_people" id="hidden_num_people">
</form>
<script>
// Auto-fill hidden form when visible inputs change
document.getElementById('student_id').addEventListener('input', function() {
    document.getElementById('hidden_student_id').value = this.value;
});

document.getElementById('full_name').addEventListener('input', function() {
    document.getElementById('hidden_full_name').value = this.value;
});

document.getElementById('booking_date').addEventListener('change', function() {
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
        const isAvailable = checkTimeSlotAvailability(selectedDate, startTime, endTime);
        
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
    var roomId = "<?php echo htmlspecialchars($roomId); ?>";
    var url = "handlers/check_availability.php?room_id=" + encodeURIComponent(roomId)
        + "&date=" + encodeURIComponent(date)
        + "&start_time=" + encodeURIComponent(startTime)
        + "&end_time=" + encodeURIComponent(endTime);

    var xhr = new XMLHttpRequest();
    xhr.open("GET", url, false); // synchronous for simplicity, you can use async with callbacks/promises
    xhr.send();

    if (xhr.status === 200) {
        var result = JSON.parse(xhr.responseText);
        return result.available;
    }
    return false;
}

// Initialize calendar when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeCalendar();
    // 1) Populate the "end_time" dropdown based on the PHP-selected start_time
    updateEndTimeOptions();

    // 2) Seed hidden fields for form submission
    document.getElementById('hidden_booking_date').value = document.getElementById('booking_date').value;
    document.getElementById('hidden_start_time').value   = document.getElementById('start_time').value;
    document.getElementById('hidden_end_time').value     = document.getElementById('end_time').value;

    // 3) (Optional) Check availability immediately
    checkAvailability();
});

// When the user picks a new start_time, rebuild end_time
document.getElementById('start_time').addEventListener('change', function() {
    document.getElementById('hidden_start_time').value = this.value;
    updateEndTimeOptions();
    checkAvailability();
});

// When the user changes the date, reload the page with new params
document.getElementById('booking_date').addEventListener('change', function() {
    window.location.search = `room_id=${encodeURIComponent('<?php echo $roomId;?>')}` +
                             `&date=${this.value}&time=${document.getElementById('start_time').value}`;
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

function bookRoom(roomId) {
    const dateInput = document.querySelector('.date-picker-input');
    const dt = dateInput ? dateInput.value : '';
    let url = 'booking.php?room_id=' + encodeURIComponent(roomId);

    if (dt) {
        const [date, time] = dt.split('T');
        url += `&date=${encodeURIComponent(date)}&time=${encodeURIComponent(time)}`;
    }
    window.location.href = url;
}

document.querySelector('.submit-button').addEventListener('click', function(e) {
    // Prevent the default form submission
    e.preventDefault();

    // Copy all visible field values to the hidden form
    document.getElementById('hidden_student_id').value = document.getElementById('student_id').value;
    document.getElementById('hidden_full_name').value = document.getElementById('full_name').value;
    document.getElementById('hidden_booking_date').value = document.getElementById('booking_date').value;
    document.getElementById('hidden_start_time').value = document.getElementById('start_time').value;
    document.getElementById('hidden_end_time').value = document.getElementById('end_time').value;
    document.getElementById('hidden_purpose').value = document.getElementById('purpose').value;
    document.getElementById('hidden_num_people').value = document.getElementById('num_people').value;

    // Now submit the hidden form
    document.getElementById('booking-form').submit();
});

document.getElementById('room-name-link').addEventListener('click', function() {
    // Get selected date and time from the form
    var date = document.getElementById('booking_date').value;
    var time = document.getElementById('start_time').value;
    var roomId = "<?php echo urlencode($roomId); ?>";
    var url = "roomdetails.php?room_id=" + roomId;
    if (date) {
        url += "&date=" + encodeURIComponent(date);
    }
    if (time) {
        url += "&time=" + encodeURIComponent(time);
    }
    window.location.href = url;
});

function checkRoomAvailability(roomId, date, startTime, endTime, callback) {
    var url = "handlers/check_availability.php?room_id=" + encodeURIComponent(roomId)
        + "&date=" + encodeURIComponent(date)
        + "&start_time=" + encodeURIComponent(startTime)
        + "&end_time=" + encodeURIComponent(endTime);

    fetch(url)
        .then(response => response.json())
        .then(data => callback(data));
}
</script>

