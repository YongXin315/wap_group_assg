<?php
session_start();
require_once '../db.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Get parameters
$studentId = $_GET['student_id'] ?? '';
$date = $_GET['date'] ?? '';
$startTime = $_GET['start_time'] ?? '';
$endTime = $_GET['end_time'] ?? '';

if (empty($studentId) || empty($date) || empty($startTime) || empty($endTime)) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

try {
    // Check for pending bookings by this student
    $pendingBooking = $db->bookings->findOne([
        'student_id' => $studentId,
        'status' => 'pending'
    ]);
    
    if ($pendingBooking) {
        $pendingDate = $pendingBooking['booking_date'];
        $pendingStartTime = $pendingBooking['start_time'];
        $pendingEndTime = $pendingBooking['end_time'];
        
        // Check if dates match and times overlap
        if ($pendingDate === $date) {
            // Check for time overlap
            $pendingStart = strtotime($pendingStartTime);
            $pendingEnd = strtotime($pendingEndTime);
            $selectedStart = strtotime($startTime);
            $selectedEnd = strtotime($endTime);
            
            // Check if times overlap
            if (($selectedStart < $pendingEnd) && ($selectedEnd > $pendingStart)) {
                echo json_encode([
                    'success' => true,
                    'hasConflict' => true,
                    'pendingBooking' => [
                        'full_name' => $pendingBooking['full_name'],
                        'booking_date' => $pendingBooking['booking_date'],
                        'start_time' => $pendingBooking['start_time'],
                        'end_time' => $pendingBooking['end_time']
                    ]
                ]);
                exit;
            }
        }
    }
    
    // No conflict found
    echo json_encode([
        'success' => true,
        'hasConflict' => false
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 