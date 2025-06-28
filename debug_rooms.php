<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set timezone to Malaysia
date_default_timezone_set('Asia/Kuala_Lumpur');

require_once 'db.php';

// Get selected date/time from POST or use current time
$selectedDateTime = isset($_POST['selected_datetime']) ? $_POST['selected_datetime'] : date('Y-m-d\TH:i');
$selectedDate = date('Y-m-d', strtotime($selectedDateTime));
$selectedTime = date('H:i', strtotime($selectedDateTime));
$selectedDayOfWeek = date('l', strtotime($selectedDateTime));

echo "<h2>Debug Information</h2>";
echo "<p><strong>Selected DateTime:</strong> $selectedDateTime</p>";
echo "<p><strong>Selected Date:</strong> $selectedDate</p>";
echo "<p><strong>Selected Time:</strong> $selectedTime</p>";
echo "<p><strong>Selected Day of Week:</strong> $selectedDayOfWeek</p>";

echo "<h3>Database Connection Test</h3>";
try {
    $client->listDatabases();
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h3>Room Data Analysis</h3>";
try {
    $rooms = $db->rooms->find();
    $roomCount = 0;
    
    foreach ($rooms as $room) {
        $roomCount++;
        echo "<div style='border: 1px solid #ccc; margin: 10px; padding: 10px;'>";
        echo "<h4>Room: " . $room['room_name'] . " (ID: " . $room['room_id'] . ")</h4>";
        echo "<p><strong>Type:</strong> " . $room['type'] . "</p>";
        
        // Check availability logic
        $isAvailable = true;
        $bookingDetails = 'Available';
        
        // Query for bookings for this specific room and day
        try {
            $bookings = $db->bookings->find([
                'room_id' => $room['room_id'],
                'day_of_week' => $selectedDayOfWeek
            ]);
            
            if ($bookings->count() > 0) {
                echo "<p><strong>Bookings found:</strong></p>";
                echo "<ul>";
                
                foreach ($bookings as $index => $booking) {
                    echo "<li>Booking $index:";
                    echo "<ul>";
                    
                    // Debug booking structure
                    foreach ($booking as $key => $value) {
                        echo "<li><strong>$key:</strong> " . (is_array($value) ? json_encode($value) : $value) . "</li>";
                    }
                    
                    // Check if this booking matches our time criteria
                    if (isset($booking['start_time']) && isset($booking['end_time'])) {
                        $startTime = $booking['start_time'];
                        $endTime = $booking['end_time'];
                        
                        // Convert times to comparable format
                        $selectedTimeFormatted = date('H:i', strtotime($selectedTime));
                        $startTimeFormatted = date('H:i', strtotime($startTime));
                        $endTimeFormatted = date('H:i', strtotime($endTime));
                        
                        $timeMatch = ($selectedTimeFormatted >= $startTimeFormatted && $selectedTimeFormatted < $endTimeFormatted);
                        
                        echo "<li><strong>Time Comparison:</strong></li>";
                        echo "<li>&nbsp;&nbsp;Selected: $selectedTimeFormatted</li>";
                        echo "<li>&nbsp;&nbsp;Start: $startTimeFormatted</li>";
                        echo "<li>&nbsp;&nbsp;End: $endTimeFormatted</li>";
                        echo "<li><strong>Time Match:</strong> " . ($timeMatch ? 'YES' : 'NO') . "</li>";
                        
                        if ($timeMatch) {
                            $isAvailable = false;
                            $bookingDetails = 'Booked: ' . $startTime . ' - ' . $endTime;
                        }
                    }
                    
                    echo "</ul></li>";
                }
                echo "</ul>";
            } else {
                echo "<p><strong>Bookings:</strong> No bookings found for this room on $selectedDayOfWeek</p>";
            }
        } catch (Exception $e) {
            echo "<p><strong>Bookings Error:</strong> " . $e->getMessage() . "</p>";
        }
        
        $statusClass = $isAvailable ? 'available' : 'occupied';
        $statusText = $isAvailable ? 'Available' : 'Occupied';
        
        echo "<p><strong>Final Status:</strong> <span style='color: " . ($isAvailable ? 'green' : 'red') . "; font-weight: bold;'>$statusText</span></p>";
        echo "<p><strong>Details:</strong> $bookingDetails</p>";
        echo "</div>";
    }
    
    if ($roomCount == 0) {
        echo "<p style='color: red;'>No rooms found in database!</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error querying rooms: " . $e->getMessage() . "</p>";
}

echo "<h3>Test Different Times</h3>";
$testTimes = [
    'Monday 09:30',
    'Monday 10:30', 
    'Tuesday 11:30',
    'Wednesday 14:30',
    'Thursday 12:30',
    'Friday 15:30'
];

foreach ($testTimes as $testTime) {
    $testDateTime = date('Y-m-d\TH:i', strtotime("next $testTime"));
    $testDayOfWeek = date('l', strtotime("next $testTime"));
    $testTimeSlot = date('H:i', strtotime("next $testTime"));
    
    echo "<p><strong>Test Time:</strong> $testTime ($testDayOfWeek $testTimeSlot)</p>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3, h4 { color: #333; }
ul { margin: 5px 0; }
li { margin: 2px 0; }
</style> 