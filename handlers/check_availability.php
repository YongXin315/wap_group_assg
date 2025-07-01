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

// Get room details
$room = $db->rooms->findOne(['_id' => $roomId]);
if (!$room) {
    echo json_encode(['available' => false, 'reason' => 'Room not found']);
    exit;
}

$isDiscussionRoom = (stripos($room['type'], 'discussion') !== false);

// Step 2 & 3: Query Existing Approved Bookings and Compare Time for Overlaps
$bookingConflicts = $db->bookings->find([
    'room_id' => $roomId,
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
            echo json_encode([
                'available' => false, 
                'reason' => 'Time slot conflicts with existing approved booking',
                'conflict_details' => [
                    'existing_start' => $booking['start_time'],
                    'existing_end' => $booking['end_time'],
                    'booking_id' => $booking['_id']
                ]
            ]);
            exit;
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
        'room_id' => $roomId,
        '$or' => [
            ['date' => $date], // Specific date entries
            ['day_of_week' => $dayOfWeek] // Recurring weekly entries
        ]
    ]);
    
    foreach ($classConflicts as $class) {
        // Handle both specific date and recurring day entries
        $classDate = isset($class['date']) ? $class['date'] : $date;
        $classStart = $classDate . ' ' . $class['start_time'];
        $classEnd = $classDate . ' ' . $class['end_time'];
        
        try {
            $classStartDateTime = new DateTime($classStart);
            $classEndDateTime = new DateTime($classEnd);
            
            // The overlap detection logic:
            // if (($selectedStartDateTime < $classEndDateTime) && 
            //     ($selectedEndDateTime > $classStartDateTime)) {
            //     // Conflict detected!
            // }
            // Use same overlapping logic for class timetable
            if (($selectedStartDateTime < $classEndDateTime) && 
                ($selectedEndDateTime > $classStartDateTime)) {
                echo json_encode([
                    'available' => false, 
                    'reason' => 'Time slot conflicts with scheduled class',
                    'conflict_details' => [
                        'class_start' => $class['start_time'],
                        'class_end' => $class['end_time'],
                        'class_name' => $class['class_name'] ?? 'Scheduled Class',
                        'day_of_week' => $dayOfWeek
                    ]
                ]);
                exit;
            }
        } catch (Exception $e) {
            // Skip invalid class entries
            continue;
        }
    }
}

// Step 5: No conflicts found - slot is available
echo json_encode([
    'available' => true,
    'message' => 'Time slot is available for booking',
    'selected_time' => [
        'date' => $date,
        'start_time' => $startTime,
        'end_time' => $endTime
    ]
]);
?>