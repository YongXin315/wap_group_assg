<?php
// ============================================================================
// SESSION AND AUTHENTICATION
// ============================================================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

// ============================================================================
// DATABASE CONNECTION AND DEPENDENCIES
// ============================================================================
require_once 'db.php';

// ============================================================================
// ROOM ID VALIDATION
// ============================================================================
$roomId = $_GET['room_id'] ?? '';

if (empty($roomId)) {
    header('Location: roomavailability.php');
    exit();
}

// ============================================================================
// ROOM DETAILS RETRIEVAL
// ============================================================================
try {
    $room = $db->rooms->findOne(['_id' => $roomId]);
    
    if (!$room) {
        // Fallback to static data if room not found
        $room = [
            '_id' => $roomId,
            'room_name' => $roomId,
            'type' => 'Classroom',
            'block' => 'Block D',
            'floor' => 'Level 8',
            'capacity' => '30-40 persons',
            'amenities' => 'Whiteboard, Projector, Air-conditioning',
            'class_timetable' => []
        ];
    } else {
        // Convert MongoDB document to array - don't overwrite actual data with fallbacks
        $room = $room->getArrayCopy();
    }
} catch (Exception $e) {
    // Fallback to static data if database error
    $room = [
        '_id' => $roomId,
        'room_name' => $roomId,
        'type' => 'Classroom',
        'block' => 'Block D',
        'floor' => 'Level 8',
        'capacity' => '30-40 persons',
        'amenities' => 'Whiteboard, Projector, Air-conditioning',
        'class_timetable' => []
    ];
}

// ============================================================================
// OCCUPANCY SETTINGS
// ============================================================================
// Set min/max occupancy from DB or fallback
if (isset($room['min_occupancy'])) {
    $minOccupancy = (int)$room['min_occupancy'];
} elseif (stripos($room['type'], 'Discussion Room') !== false && preg_match('/(\d+)[^\d]+(\d+)/', $room['capacity'], $matches)) {
    $minOccupancy = (int)$matches[1];
} else {
    $minOccupancy = 1;
}

if (isset($room['max_occupancy'])) {
    $maxOccupancy = (int)$room['max_occupancy'];
} elseif (stripos($room['type'], 'Discussion Room') !== false && preg_match('/(\d+)[^\d]+(\d+)/', $room['capacity'], $matches)) {
    $maxOccupancy = (int)$matches[2];
} else {
    $maxOccupancy = 50;
}

$roomIdForQuery = isset($room['_id']) ? $room['_id'] : $roomId;

// ============================================================================
// FORM SUBMISSION HANDLING
// ============================================================================
$bookingMessage = '';
$bookingError = '';
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $studentId = $_POST['student_id'] ?? '';
    $fullName = $_POST['full_name'] ?? '';
    $bookingDate = $_POST['booking_date'] ?? '';
    $startTime = $_POST['start_time'] ?? '';
    $endTime = $_POST['end_time'] ?? '';
    $purpose = $_POST['purpose'] ?? '';
    $numPeople = $_POST['num_people'] ?? '';
    $roomId = $_GET['room_id'] ?? '';
    $studentIdsRaw = $_POST['student_ids'] ?? '';
    
    // Accept both array and comma-separated string
    if (is_array($studentIdsRaw)) {
        $studentIdsArr = array_filter(array_map('trim', $studentIdsRaw));
    } else {
        $studentIdsArr = array_filter(array_map('trim', preg_split('/[\s,]+/', $studentIdsRaw)));
    }

    // Validate required fields
    if (empty($studentId) || empty($fullName) || empty($bookingDate) || empty($startTime) || empty($endTime) || empty($purpose) || empty($numPeople) || empty($roomId)) {
        $bookingError = 'Please fill in all required fields.';
    } else if ((int)$numPeople < $minOccupancy || (int)$numPeople > $maxOccupancy) {
        $bookingError = 'Number of people must be between ' . $minOccupancy . ' and ' . $maxOccupancy . '.';
    } else if (stripos($room['type'], 'Discussion Room') !== false) {
        // Student ID validation for Discussion Room
        $uniqueIds = array_unique($studentIdsArr);
        if (count($studentIdsArr) < $minOccupancy || count($studentIdsArr) > $maxOccupancy) {
            $bookingError = 'Please enter between ' . $minOccupancy . ' and ' . $maxOccupancy . ' student IDs.';
        } else if (count($uniqueIds) !== count($studentIdsArr)) {
            $bookingError = 'All student IDs must be unique.';
        } else {
            // Check if all student IDs exist in the database
            $invalidIds = [];
            foreach ($studentIdsArr as $sid) {
                $exists = $db->students->findOne(['student_id' => $sid]);
                if (!$exists) {
                    $invalidIds[] = $sid;
                }
            }
            if (!empty($invalidIds)) {
                $bookingError = 'The following student ID(s) do not exist: ' . implode(', ', $invalidIds) . '.';
            }
        }
    }
    
    // Check if user already booked a discussion room on the same date
    if (stripos($room['type'], 'Discussion Room') !== false && empty($bookingError)) {
        $existingBooking = $db->bookings->findOne([
            'student_id' => $studentId,
            'booking_date' => $bookingDate,
            'status' => ['$in' => ['approved', 'pending']],
            // Find any discussion room
            'room_id' => ['$in' => iterator_to_array($db->rooms->find([
                'type' => ['$regex' => 'Discussion Room', '$options' => 'i']
            ], ['projection' => ['_id' => 1]]))->map(fn($r) => $r['_id'])]
        ]);
        if ($existingBooking) {
            $bookingError = 'You have already booked a discussion room on this date. Only one discussion room booking is allowed per day.';
        }
    }
    
    if (empty($bookingError)) {
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

        if (stripos($room['type'], 'Discussion Room') !== false) {
            $bookingData['student_ids'] = $studentIdsArr;
        }

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

    if ($isAjax) {
        if ($bookingError) {
            echo json_encode(['success' => false, 'error' => $bookingError]);
        } else {
            echo json_encode([
                'success' => true,
                'message' => $bookingMessage,
                'redirect' => "roomdetails.php?room_id=" . urlencode($roomId) . "&date=" . urlencode($bookingDate)
            ]);
        }
        exit;
    }
}

// ============================================================================
// DATE AND TIME SETUP
// ============================================================================
// Set Malaysia timezone & "now"
date_default_timezone_set('Asia/Kuala_Lumpur');
$currentDate = date('Y-m-d');
$currentTime = date('H:i');

// If both date & time are passed, use them; otherwise fall back to "now"
$maxBookingDate = date('Y-m-d', strtotime($currentDate . ' +6 days'));
$selectedDateRaw = $_GET['date'] ?? date('Y-m-d');

// If the selected date is outside the allowed range, reset to today
if ($selectedDateRaw < $currentDate || $selectedDateRaw > $maxBookingDate) {
    $selectedDate = $currentDate;
} else {
    $selectedDate = $selectedDateRaw;
}

$selectedTime = $_GET['time'] ?? date('H:i');
$selectedHour = (int) substr($selectedTime, 0, 2);
$defaultStartTime = sprintf('%02d:00', $selectedHour);

// Get day of week for the selected date
$selectedDayOfWeek = date('l', strtotime($selectedDate));
$isCurrentDate = ($selectedDate === $currentDate);

// ============================================================================
// TIME RANGE CALCULATION
// ============================================================================
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
            $startHour = $endHour; // This will result in no available times
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

// ============================================================================
// HELPER FUNCTIONS
// ============================================================================
function to12Hour($time) {
    return date('g:i A', strtotime($time));
}
?>

<?php include_once 'component/header.php'; ?>

<!-- ============================================================================
     CSS STYLES
     ============================================================================ -->
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

/* Remove hover effect on selected date */
.calendar-day.selected:hover {
    background: inherit !important;
    color: inherit !important;
    cursor: default !important;
    box-shadow: none !important;
    text-decoration: none !important;
}
</style>

<!-- ============================================================================
     MAIN HTML STRUCTURE
     ============================================================================ -->
<div class="page-vertical-wrapper">
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
                                    <div class="summary-value" id="summary-date-value">
                                        <?php echo date('F j, Y', strtotime($selectedDate)); ?>
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
                                   placeholder="e.g., 0312345" 
                                   value="<?php echo htmlspecialchars($_SESSION['student_id'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" id="full_name" name="full_name" class="form-input" 
                                   placeholder="Enter your full name" 
                                   value="<?php echo htmlspecialchars($_SESSION['student_name'] ?? ''); ?>" required>
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
                                   min="<?php echo $currentDate; ?>"
                                   max="<?php echo date('Y-m-d', strtotime($currentDate . ' +6 days')); ?>" required>
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
                                min="<?php echo $minOccupancy; ?>"
                                max="<?php echo $maxOccupancy; ?>"
                                placeholder="Enter number of people" required>
                            <small>Allowed: <?php echo $minOccupancy; ?> to <?php echo $maxOccupancy; ?> people</small>
                        </div>
                    </div>
                </div>

                <!-- Student IDs Section (Discussion Rooms Only) -->
                <?php if (stripos($room['type'], 'Discussion Room') !== false): ?>
                <div class="form-row" id="student-ids-row" style="display:none;">
                    <div class="form-group">
                        <label class="form-label">Student IDs of All Attendees</label>
                        <div id="student-ids-container"></div>
                        <small id="student-ids-hint"></small>
                    </div>
                </div>
                <?php endif; ?>

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
</div>

<!-- Hidden Form for AJAX Submission -->
<form id="booking-form" method="POST" style="display: none;">
    <input type="hidden" name="student_id" id="hidden_student_id">
    <input type="hidden" name="full_name" id="hidden_full_name">
    <input type="hidden" name="booking_date" id="hidden_booking_date">
    <input type="hidden" name="start_time" id="hidden_start_time">
    <input type="hidden" name="end_time" id="hidden_end_time">
    <input type="hidden" name="purpose" id="hidden_purpose">
    <input type="hidden" name="num_people" id="hidden_num_people">
</form>

<!-- ============================================================================
     JAVASCRIPT FUNCTIONALITY
     ============================================================================ -->
<script>
// ============================================================================
// GLOBAL VARIABLES
// ============================================================================
const roomType = '<?php echo htmlspecialchars($room['type']); ?>';
const roomId = '<?php echo htmlspecialchars($roomId); ?>';

// ============================================================================
// EVENT LISTENERS
// ============================================================================

// Auto-fill hidden form when visible inputs change
document.getElementById('student_id').addEventListener('input', function() {
    document.getElementById('hidden_student_id').value = this.value;
});

document.getElementById('full_name').addEventListener('input', function() {
    document.getElementById('hidden_full_name').value = this.value;
});

document.getElementById('purpose').addEventListener('input', function() {
    document.getElementById('hidden_purpose').value = this.value;
});

document.getElementById('num_people').addEventListener('input', function() {
    document.getElementById('hidden_num_people').value = this.value;
    updateStudentIdFields();
});

// Date change event - update time options dynamically
document.getElementById('booking_date').addEventListener('change', function() {
    updateSummaryDate();
    updateTimeOptions();
    updateEndTimeOptions();
    checkAvailability();
});

// Start time change event
document.getElementById('start_time').addEventListener('change', function() {
    document.getElementById('hidden_start_time').value = this.value;
    updateEndTimeOptions();
    checkAvailability();
});

// Submit button event
document.querySelector('.submit-button').addEventListener('click', function(e) {
    e.preventDefault();
    submitBooking();
});

// Room name link click event
document.getElementById('room-name-link').addEventListener('click', function() {
    const date = document.getElementById('booking_date').value;
    const time = document.getElementById('start_time').value;
    let url = "roomdetails.php?room_id=" + roomId;
    if (date) {
        url += "&date=" + encodeURIComponent(date);
    }
    if (time) {
        url += "&time=" + encodeURIComponent(time);
    }
    window.location.href = url;
});

// ============================================================================
// CORE FUNCTIONS
// ============================================================================

/**
 * Update the summary date display
 */
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

/**
 * Update time options based on selected date and room type
 */
function updateTimeOptions() {
    console.log('updateTimeOptions called');
    
    const selectedDate = document.getElementById('booking_date').value;
    const today = new Date();
    const selected = new Date(selectedDate);
    const isCurrentDate = (selected.toDateString() === today.toDateString());
    let currentHour = today.getHours();
    
    // Get day of week for selected date
    const dayOfWeek = selected.toLocaleDateString('en-US', { weekday: 'long' });
    
    // Determine time range based on room type and day of week
    const timeRange = getTimeRange(roomType, dayOfWeek, isCurrentDate, currentHour);
    const startHour = timeRange.start;
    const endHour = timeRange.end;
    
    // Get select elements
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
}

/**
 * Update end time options based on selected start time
 */
function updateEndTimeOptions() {
    const selectedDate = document.getElementById('booking_date').value;
    const startTime = document.getElementById('start_time').value;
    const today = new Date();
    const selected = new Date(selectedDate);
    const isCurrentDate = (selected.toDateString() === today.toDateString());
    let currentHour = today.getHours();
    
    // Get day of week for selected date
    const dayOfWeek = selected.toLocaleDateString('en-US', { weekday: 'long' });
    
    // Determine time range
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

/**
 * Determine time range based on room type and day of week
 */
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

/**
 * Check availability for selected time slot
 */
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
    
    // Check availability via AJAX
    setTimeout(() => {
        const availabilityResult = checkTimeSlotAvailability(selectedDate, startTime, endTime);
        
        // Log the result for debugging
        console.log('Availability check result:', availabilityResult);
        
        if (availabilityResult.available) {
            statusMessage.textContent = '✓ Time slot is available for booking';
            statusMessage.className = 'status-message available';
        } else {
            // Display detailed conflict information
            let conflictMessage = '✗ Time slot is not available\n\n';
            
            if (availabilityResult.conflict_type === 'booking') {
                conflictMessage += '📅 CONFLICT WITH EXISTING BOOKING:\n';
                availabilityResult.conflicts.bookings.forEach(booking => {
                    conflictMessage += `• Booked by: ${booking.booked_by}\n`;
                    conflictMessage += `• Time: ${booking.existing_start} - ${booking.existing_end}\n`;
                    conflictMessage += `• Purpose: ${booking.purpose}\n\n`;
                });
            } else if (availabilityResult.conflict_type === 'timetable') {
                conflictMessage += '📚 CONFLICT WITH SCHEDULED CLASS:\n';
                availabilityResult.conflicts.timetable.forEach(classInfo => {
                    conflictMessage += `• Class: ${classInfo.class_name}\n`;
                    conflictMessage += `• Time: ${classInfo.class_start} - ${classInfo.class_end}\n`;
                    conflictMessage += `• Lecturer: ${classInfo.lecturer}\n\n`;
                });
            } else if (availabilityResult.conflict_type === 'both') {
                conflictMessage += '⚠️ CONFLICTS WITH BOTH BOOKING AND CLASS:\n\n';
                
                conflictMessage += '📅 EXISTING BOOKINGS:\n';
                availabilityResult.conflicts.bookings.forEach(booking => {
                    conflictMessage += `• Booked by: ${booking.booked_by}\n`;
                    conflictMessage += `• Time: ${booking.existing_start} - ${booking.existing_end}\n`;
                    conflictMessage += `• Purpose: ${booking.purpose}\n\n`;
                });
                
                conflictMessage += '📚 SCHEDULED CLASSES:\n';
                availabilityResult.conflicts.timetable.forEach(classInfo => {
                    conflictMessage += `• Class: ${classInfo.class_name}\n`;
                    conflictMessage += `• Time: ${classInfo.class_start} - ${classInfo.class_end}\n`;
                    conflictMessage += `• Lecturer: ${classInfo.lecturer}\n\n`;
                });
            } else {
                // Fallback for other types of conflicts
                conflictMessage += availabilityResult.reason || 'Unknown conflict';
            }
            
            statusMessage.textContent = conflictMessage;
            statusMessage.className = 'status-message unavailable';
        }
    }, 500);
}

/**
 * Check time slot availability via AJAX
 */
function checkTimeSlotAvailability(date, startTime, endTime) {
    const url = "handlers/check_availability.php?room_id=" + encodeURIComponent(roomId)
        + "&date=" + encodeURIComponent(date)
        + "&start_time=" + encodeURIComponent(startTime)
        + "&end_time=" + encodeURIComponent(endTime);

    const xhr = new XMLHttpRequest();
    xhr.open("GET", url, false); // synchronous for simplicity
    
    try {
        xhr.send();
        
        if (xhr.status === 200) {
            const result = JSON.parse(xhr.responseText);
            return result;
        } else {
            console.error('Availability check failed with status:', xhr.status);
            return { available: false, reason: 'Server error: ' + xhr.status };
        }
    } catch (error) {
        console.error('Error checking availability:', error);
        return { available: false, reason: 'Network error: ' + error.message };
    }
}

/**
 * Submit booking via AJAX
 */
function submitBooking() {
    // Collect form data
    const formData = new FormData();
    formData.append('student_id', document.getElementById('student_id').value);
    formData.append('full_name', document.getElementById('full_name').value);
    formData.append('booking_date', document.getElementById('booking_date').value);
    formData.append('start_time', document.getElementById('start_time').value);
    formData.append('end_time', document.getElementById('end_time').value);
    formData.append('purpose', document.getElementById('purpose').value);
    formData.append('num_people', document.getElementById('num_people').value);
    
    const studentIdInputs = document.querySelectorAll('input[name="student_ids[]"]');
    if (studentIdInputs.length > 0) {
        let ids = [];
        studentIdInputs.forEach(input => ids.push(input.value));
        formData.append('student_ids', ids.join(','));
    }

    fetch(window.location.href, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        // Remove old messages
        document.querySelectorAll('.success-message, .error-message').forEach(el => el.remove());

        if (data.success) {
            alert(data.message);
            // Redirect after a delay
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 2000);
        } else {
            alert(data.error);
            const msg = document.createElement('div');
            msg.className = 'error-message';
            msg.textContent = data.error;
            document.querySelector('.booking-container').prepend(msg);
        }
    });
}

/**
 * Update student ID fields for discussion rooms
 */
function updateStudentIdFields() {
    const numPeople = parseInt(document.getElementById('num_people').value) || 0;
    const min = <?php echo $minOccupancy; ?>;
    const max = <?php echo $maxOccupancy; ?>;
    const container = document.getElementById('student-ids-container');
    const row = document.getElementById('student-ids-row');
    const hint = document.getElementById('student-ids-hint');
    
    if (!container || !row || !hint) return; // Exit if elements don't exist
    
    container.innerHTML = '';
    hint.textContent = '';

    if (numPeople >= min && numPeople <= max) {
        row.style.display = '';
        for (let i = 1; i <= numPeople; i++) {
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'student_ids[]';
            input.className = 'form-input';
            input.placeholder = 'Student ID ' + i;
            input.required = true;
            container.appendChild(input);
        }
        hint.textContent = `Enter ${min} to ${max} student IDs (including yourself)`;
    } else {
        row.style.display = 'none';
    }
}

// ============================================================================
// INITIALIZATION
// ============================================================================

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Seed hidden fields for form submission
    document.getElementById('hidden_booking_date').value = document.getElementById('booking_date').value;
    document.getElementById('hidden_start_time').value = document.getElementById('start_time').value;
    document.getElementById('hidden_end_time').value = document.getElementById('end_time').value;

    // Initialize student ID fields if needed
updateStudentIdFields();
    
    // Check availability immediately
    checkAvailability();
});
</script>

<?php include_once 'component/footer.php'; ?>

