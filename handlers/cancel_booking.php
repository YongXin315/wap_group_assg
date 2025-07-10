<?php
session_start();
require_once '../db.php';

// Add MongoDB BSON use statements
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['student_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Get booking ID from POST data
$bookingId = $_POST['booking_id'] ?? '';
$studentId = $_SESSION['student_id'];

if (empty($bookingId)) {
    echo json_encode(['success' => false, 'message' => 'Missing booking ID']);
    exit;
}

try {
    // Find the booking and verify it belongs to the current user
    $booking = $db->bookings->findOne([
        '_id' => new ObjectId($bookingId),
        'student_id' => $studentId
    ]);
    
    if (!$booking) {
        echo json_encode(['success' => false, 'message' => 'Booking not found or unauthorized']);
        exit;
    }
    
    // Check if booking is already cancelled
    if (isset($booking['status']) && $booking['status'] === 'cancelled') {
        echo json_encode(['success' => false, 'message' => 'Booking is already cancelled']);
        exit;
    }
    
    // Update booking status to cancelled
    $result = $db->bookings->updateOne(
        ['_id' => new ObjectId($bookingId)],
        [
            '$set' => [
                'status' => 'cancelled',
                'cancelled_at' => new UTCDateTime()
            ]
        ]
    );
    
    if ($result->getModifiedCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to cancel booking']);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>