<?php
require_once(__DIR__ . '/../middleware/admin_auth.php');
require_once(__DIR__ . '/../db.php');

header('Content-Type: application/json');

// Parse incoming JSON
$input = json_decode(file_get_contents('php://input'), true);
$bookingId = $input['id'] ?? null;
$action = strtolower(trim($input['action'] ?? ''));

// Validate
if (!$bookingId || !in_array($action, ['approved', 'cancelled'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

try {
    // Convert string ID to MongoDB ObjectId
    $result = $db->bookings->updateOne(
        ['_id' => new MongoDB\BSON\ObjectId($bookingId)],
        ['$set' => [
            'status' => $action,
            'updated_at' => new MongoDB\BSON\UTCDateTime()
        ]]
    );

    // Check if the booking was actually modified
    if ($result->getModifiedCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Booking not found or already updated']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
