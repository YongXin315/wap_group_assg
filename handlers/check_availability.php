<?php
require_once '../db.php';

header('Content-Type: application/json');

// Step 1: Retrieve User Input
$roomId = $_GET['room_id'] ?? '';
$date = $_GET['date'] ?? '';
$startTime = $_GET['start_time'] ?? '';
$endTime = $_GET['end_time'] ?? '';

// Validate required parameters
if (!$roomId || !$date || !$startTime || !$endTime) {
    echo json_encode(['available' => false, 'reason' => 'Missing parameters']);
    exit;
}

// Combine date and time into datetime format
$selectedStart = $date . ' ' . $startTime;
$selectedEnd = $date . ' ' . $endTime;

// Convert to DateTime objects for safer comparison
try {
    $selectedStartDateTime = new DateTime($selectedStart);
    $selectedEndDateTime = new DateTime($selectedEnd);
} catch (Exception $e) {
    echo json_encode(['available' => false, 'reason' => 'Invalid date/time format']);
    exit;
}

// Get room details first
$room = $db->rooms->findOne(['_id' => $roomId]);
if (!$room) {
    echo json_encode(['available' => false, 'reason' => 'Room not found']);
    exit;
}

$isDiscussionRoom = (stripos($room['type'], 'discussion') !== false);

// Initialize conflict tracking
$conflicts = [
    'bookings' => [],
    'timetable' => []
];

// Step 2 & 3: Query Existing Approved Bookings and Compare Time for Overlaps
$bookingConflicts = $db->bookings->find([
    'room_id' => $roomId, // Use _id for bookings collection since that's what's stored
    'booking_date' => $date,
    'status' => 'approved'
]);

foreach ($bookingConflicts as $booking) {
    $existingStart = $date . ' ' . $booking['start_time'];
    $existingEnd = $date . ' ' . $booking['end_time'];
    
    try {
        $existingStartDateTime = new DateTime($existingStart);
        $existingEndDateTime = new DateTime($existingEnd);
        
        if (($selectedStartDateTime < $existingEndDateTime) && 
            ($selectedEndDateTime > $existingStartDateTime)) {
            
            $conflicts['bookings'][] = [
                'existing_start' => $booking['start_time'],
                'existing_end' => $booking['end_time'],
                'booking_id' => $booking['_id'],
                'booked_by' => $booking['full_name'] ?? 'Unknown',
                'purpose' => $booking['purpose'] ?? 'No purpose specified'
            ];
        }
    } catch (Exception $e) {
        // Skip invalid booking entries
        continue;
    }
}

// Step 4: Query Class Timetable for Same Day and Compare (for non-discussion rooms only)
if (!$isDiscussionRoom) {
    $dayOfWeek = date('l', strtotime($date)); // Get day name (Monday, Tuesday, etc.)
    
    $classConflicts = $db->class_timetable->find([
        'room_id' => $roomId,  // Use _id for class_timetable collection since it stores room's _id
        'day_of_week' => $dayOfWeek
    ]);
    
    foreach ($classConflicts as $class) {
        $classStart = $date . ' ' . $class['start_time'];
        $classEnd = $date . ' ' . $class['end_time'];
        
        try {
            $classStartDateTime = new DateTime($classStart);
            $classEndDateTime = new DateTime($classEnd);
            
            if (($selectedStartDateTime < $classEndDateTime) && 
                ($selectedEndDateTime > $classStartDateTime)) {
                
                $conflicts['timetable'][] = [
                    'class_start' => $class['start_time'],
                    'class_end' => $class['end_time'],
                    'class_name' => $class['class_name'] ?? 'Scheduled Class',
                    'day_of_week' => $dayOfWeek,
                    'subject' => $class['subject'] ?? 'Unknown Subject',
                    'lecturer' => $class['lecturer'] ?? 'Unknown Lecturer'
                ];
            }
        } catch (Exception $e) {
            // Skip invalid class entries
            continue;
        }
    }
}

// Check if there are any conflicts
if (!empty($conflicts['bookings']) || !empty($conflicts['timetable'])) {
    $reason = '';
    $conflictType = '';
    
    // Log the conflicts for debugging
    error_log("Availability check conflicts for room $roomId on $date from $startTime to $endTime:");
    if (!empty($conflicts['bookings'])) {
        error_log("Booking conflicts: " . json_encode($conflicts['bookings']));
    }
    if (!empty($conflicts['timetable'])) {
        error_log("Timetable conflicts: " . json_encode($conflicts['timetable']));
    }
    
    if (!empty($conflicts['bookings']) && !empty($conflicts['timetable'])) {
        $reason = 'Time slot conflicts with both existing booking and scheduled class';
        $conflictType = 'both';
        error_log("Conflict type: BOTH (booking and timetable)");
    } elseif (!empty($conflicts['bookings'])) {
        $reason = 'Time slot conflicts with existing booking';
        $conflictType = 'booking';
        error_log("Conflict type: BOOKING only");
    } elseif (!empty($conflicts['timetable'])) {
        $reason = 'Time slot conflicts with scheduled class';
        $conflictType = 'timetable';
        error_log("Conflict type: TIMETABLE only");
    }
    
    echo json_encode([
        'available' => false, 
        'reason' => $reason,
        'conflict_type' => $conflictType,
        'conflicts' => $conflicts,
        'detailed_message' => generateDetailedMessage($conflicts, $conflictType)
    ]);
    exit;
}

// Step 5: No conflicts found - slot is available

// Log that no conflicts were found
error_log("No conflicts found for room $roomId on $date from $startTime to $endTime - slot is available");

// Find the next unavailable time (booking or class) after the selected end time
$nextTimes = [];

// Next booking
$nextBooking = $db->bookings->find(
    [
        'room_id' => $roomId, // Use _id for bookings collection
        'booking_date' => $date,
        'status' => 'approved',
        'start_time' => ['$gt' => $endTime]
    ],
    ['sort' => ['start_time' => 1], 'limit' => 1]
);
foreach ($nextBooking as $b) {
    $nextTimes[] = $b['start_time'];
}

// Next class (for non-discussion rooms)
if (!$isDiscussionRoom) {
    $dayOfWeek = date('l', strtotime($date));
    $nextClass = $db->class_timetable->find(
        [
            'room_id' => $roomId, // Use _id for class_timetable collection since it stores room's _id
            'day_of_week' => $dayOfWeek,
            'start_time' => ['$gt' => $endTime]
        ],
        ['sort' => ['start_time' => 1], 'limit' => 1]
    );
    foreach ($nextClass as $c) {
        $nextTimes[] = $c['start_time'];
    }
}

$availableUntil = null;
if (!empty($nextTimes)) {
    sort($nextTimes);
    $availableUntil = $nextTimes[0];
}

echo json_encode([
    'available' => true,
    'message' => 'Time slot is available for booking',
    'selected_time' => [
        'date' => $date,
        'start_time' => $startTime,
        'end_time' => $endTime
    ],
    'available_until' => $availableUntil
]);

/**
 * Generate detailed message about conflicts
 */
function generateDetailedMessage($conflicts, $conflictType) {
    $messages = [];
    
    if (!empty($conflicts['bookings'])) {
        $bookingMessages = [];
        foreach ($conflicts['bookings'] as $booking) {
            $bookingMessages[] = sprintf(
                "Booking by %s (%s - %s) for: %s",
                $booking['booked_by'],
                $booking['existing_start'],
                $booking['existing_end'],
                $booking['purpose']
            );
        }
        $messages[] = "Existing Bookings:\n" . implode("\n", $bookingMessages);
    }
    
    if (!empty($conflicts['timetable'])) {
        $timetableMessages = [];
        foreach ($conflicts['timetable'] as $class) {
            $timetableMessages[] = sprintf(
                "%s (%s - %s) by %s",
                $class['class_name'],
                $class['class_start'],
                $class['class_end'],
                $class['lecturer']
            );
        }
        $messages[] = "Scheduled Classes:\n" . implode("\n", $timetableMessages);
    }
    
    return implode("\n\n", $messages);
}
?>