<?php
require 'vendor/autoload.php'; // Include Composer autoload

// Replace with your own MongoDB URI
$uri = "mongodb+srv://rootadmin:rootadmin@cluster0.ge5ruc5.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";

// Connect MongoDB Client
$client = new MongoDB\Client($uri);

// Select database and collection
$db = $client->wap_system;
$collection = $db->class_timetable;

$class_timetable = [
    [
        'room_id' => 'LT1',
        'day_of_week' => 'Monday',
        'start_time' => '09:00',
        'end_time' => '11:00'
    ],
    [
        'room_id' => 'LT1',
        'day_of_week' => 'Monday',
        'start_time' => '12:00',
        'end_time' => '13:00'
    ],
    [
        'room_id' => 'LT1',
        'day_of_week' => 'Tuesday',
        'start_time' => '10:00',
        'end_time' => '12:00'
    ],
    [
        'room_id' => 'LT1',
        'day_of_week' => 'Tuesday',
        'start_time' => '15:00',
        'end_time' => '17:00'
    ],
    [
        'room_id' => 'LT1',
        'day_of_week' => 'Wednesday',
        'start_time' => '09:00',
        'end_time' => '11:00'
    ],
    [
        'room_id' => 'LT1',
        'day_of_week' => 'Wednesday',
        'start_time' => '10:00',
        'end_time' => '13:00'
    ],
    [
        'room_id' => 'LT1',
        'day_of_week' => 'Thursday',
        'start_time' => '9:00',
        'end_time' => '12:00'
    ],
    [
        'room_id' => 'LT1',
        'day_of_week' => 'Friday',
        'start_time' => '10:00',
        'end_time' => '13:00'
    ],
    [
        'room_id' => 'C7.01',
        'day_of_week' => 'Monday',
        'start_time' => '10:00',
        'end_time' => '13:00'
    ],
    [
        'room_id' => 'C7.01',
        'day_of_week' => 'Monday',
        'start_time' => '14:00',
        'end_time' => '16:00'
    ],
    [
        'room_id' => 'C7.01',
        'day_of_week' => 'Tuesday',
        'start_time' => '12:00',
        'end_time' => '16:00'
    ],
    [
        'room_id' => 'C7.01',
        'day_of_week' => 'Wednesday',
        'start_time' => '08:00',
        'end_time' => '13:00'
    ],
    [
        'room_id' => 'C7.01',
        'day_of_week' => 'Thursday',
        'start_time' => '10:00',
        'end_time' => '16:00'
    ],
    [
        'room_id' => 'C7.01',
        'day_of_week' => 'Friday',
        'start_time' => '12:00',
        'end_time' => '14:00'
    ],
    [
        'room_id' => 'D8.01',
        'day_of_week' => 'Monday',
        'start_time' => '12:00',
        'end_time' => '14:00'
    ],
    [
        'room_id' => 'D8.01',
        'day_of_week' => 'Tuesday',
        'start_time' => '10:00',
        'end_time' => '12:00'
    ],
    [
        'room_id' => 'D8.01',
        'day_of_week' => 'Tuesday',
        'start_time' => '13:00',
        'end_time' => '15:00'
    ],
    [
        'room_id' => 'D8.01',
        'day_of_week' => 'Wednesday',
        'start_time' => '10:00',
        'end_time' => '16:00'
    ],
    [
        'room_id' => 'D8.01',
        'day_of_week' => 'Thursday',
        'start_time' => '09:00',
        'end_time' => '10:00'
    ],
    [
        'room_id' => 'D8.01',
        'day_of_week' => 'Thursday',
        'start_time' => '12:00',
        'end_time' => '15:00'
    ],
    [
        'room_id' => 'D8.01',
        'day_of_week' => 'Friday',
        'start_time' => '10:00',
        'end_time' => '14:00'
    ],
];

// Insert many documents
try {
    $result = $collection->insertMany($class_timetable);
    echo "Inserted " . $result->getInsertedCount() . " class timetable(s).";
} catch (MongoDB\Driver\Exception\BulkWriteException $e) {
    echo "Insert failed: " . $e->getMessage();
}
?>
