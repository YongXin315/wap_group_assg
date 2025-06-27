<?php
require 'vendor/autoload.php'; // Include Composer autoload

// Replace with your own MongoDB URI
$uri = "mongodb+srv://rootadmin:rootadmin@cluster0.ge5ruc5.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";

// Connect MongoDB Client
$client = new MongoDB\Client($uri);

// Select database and collection
$db = $client->taylors;
$collection = $db->rooms;

$rooms = [
    [
        '_id' => 'DR3.1',
        'room_name' => 'Discussion Room 3.1',
        'block' => 'C',
        'floor' => '3',
        'type' => 'Discussion Room',
        'min_occupancy' => 4,
        'max_occupancy' => 8,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ],
    [
        '_id' => 'DR3.2',
        'room_name' => 'Discussion Room 3.2',
        'block' => 'C',
        'floor' => '3',
        'type' => 'Discussion Room',
        'min_occupancy' => 4,
        'max_occupancy' => 8,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ],
    [
        '_id' => 'DR3.3',
        'room_name' => 'Discussion Room 3.3',
        'block' => 'C',
        'floor' => '3',
        'type' => 'Discussion Room',
        'min_occupancy' => 4,
        'max_occupancy' => 8,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ],
    [
        '_id' => 'DR3.4',
        'room_name' => 'Discussion Room 3.4',
        'block' => 'C',
        'floor' => '3',
        'type' => 'Discussion Room',
        'min_occupancy' => 4,
        'max_occupancy' => 8,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ],
    [
        '_id' => 'DR4.1',
        'room_name' => 'Discussion Room 4.1',
        'block' => 'C',
        'floor' => '4',
        'type' => 'Discussion Room',
        'min_occupancy' => 4,
        'max_occupancy' => 8,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ],
    [
        '_id' => 'DR4.2',
        'room_name' => 'Discussion Room 4.2',
        'block' => 'C',
        'floor' => '4',
        'type' => 'Discussion Room',
        'min_occupancy' => 4,
        'max_occupancy' => 8,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ],
    [
        '_id' => 'DR4.3',
        'room_name' => 'Discussion Room 4.3',
        'block' => 'C',
        'floor' => '4',
        'type' => 'Discussion Room',
        'min_occupancy' => 4,
        'max_occupancy' => 8,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ],
    [
        '_id' => 'DR5.1',
        'room_name' => 'Discussion Room 5.1',
        'block' => 'C',
        'floor' => '5',
        'type' => 'Discussion Room',
        'min_occupancy' => 4,
        'max_occupancy' => 8,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ],
    [
        '_id' => 'DR5.2',
        'room_name' => 'Discussion Room 5.2',
        'block' => 'C',
        'floor' => '4',
        'type' => 'Discussion Room',
        'min_occupancy' => 4,
        'max_occupancy' => 8,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ],
    [
        '_id' => 'C7.01',
        'room_name' => 'Computer Lab C7.01',
        'block' => 'C',
        'floor' => '7.01',
        'type' => 'Computer Lab',
        'min_occupancy' => 1,
        'max_occupancy' => 30,
        'amenities' => 'Whiteboard, TV with Wireless Display, 30 Computers',
        'status' => 'Available'
    ],
    [
        '_id' => 'C7.02',
        'room_name' => 'Computer Lab C7.02',
        'block' => 'C',
        'floor' => '7.02',
        'type' => 'Computer Lab',
        'min_occupancy' => 1,
        'max_occupancy' => 30,
        'amenities' => 'Whiteboard, TV with Wireless Display, 30 Computers',
        'status' => 'Available'
    ],
    [
        '_id' => 'C7.03',
        'room_name' => 'Computer Lab C7.03',
        'block' => 'C',
        'floor' => '7.03',
        'type' => 'Computer Lab',
        'min_occupancy' => 1,
        'max_occupancy' => 30,
        'amenities' => 'Whiteboard, TV with Wireless Display, 30 Computers',
        'status' => 'Available'
    ],
    [
        '_id' => 'C7.04',
        'room_name' => 'Computer Lab C7.04',
        'block' => 'C',
        'floor' => '7.04',
        'type' => 'Computer Lab',
        'min_occupancy' => 1,
        'max_occupancy' => 30,
        'amenities' => 'Whiteboard, TV with Wireless Display, 30 Computers',
        'status' => 'Available'
    ],
    [
        '_id' => 'D8.01',
        'room_name' => 'Classroom D8.01',
        'block' => 'D',
        'floor' => '8.01',
        'type' => 'Classroom',
        'min_occupancy' => 1,
        'max_occupancy' => 30,
        'amenities' => 'Whiteboard, TV with Wireless Display, Flipped Chairs',
        'status' => 'Available'
    ],
    [
        '_id' => 'D7.01',
        'room_name' => 'Classroom D7.01',
        'block' => 'D',
        'floor' => '7.01',
        'type' => 'Classroom',
        'min_occupancy' => 1,
        'max_occupancy' => 30,
        'amenities' => 'Whiteboard, TV with Wireless Display, Flipped Chairs',
        'status' => 'Available'
    ],
    [
        '_id' => 'D6.01',
        'room_name' => 'Classroom D6.01',
        'block' => 'D',
        'floor' => '6.01',
        'type' => 'Classroom',
        'min_occupancy' => 1,
        'max_occupancy' => 30,
        'amenities' => 'Whiteboard, TV with Wireless Display, Flipped Chairs',
        'status' => 'Available'
    ],
    [
        '_id' => 'D4.01',
        'room_name' => 'Classroom D4.01',
        'block' => 'D',
        'floor' => '4.01',
        'type' => 'Classroom',
        'min_occupancy' => 1,
        'max_occupancy' => 30,
        'amenities' => 'Whiteboard, TV with Wireless Display, Rolling Chairs',
        'status' => 'Available'
    ],
    [
        '_id' => 'D3.02',
        'room_name' => 'Classroom D3.02',
        'block' => 'D',
        'floor' => '3.02',
        'type' => 'Classroom',
        'min_occupancy' => 1,
        'max_occupancy' => 30,
        'amenities' => 'Whiteboard, TV with Wireless Display, Rolling Chairs',
        'status' => 'Available'
    ],
    [
        '_id' => 'E6.01',
        'room_name' => 'Classroom E6.01',
        'block' => 'E',
        'floor' => '6.01',
        'type' => 'Classroom',
        'min_occupancy' => 1,
        'max_occupancy' => 30,
        'amenities' => 'Whiteboard, TV with Wireless Display, Desks and Chairs',
        'status' => 'Available'
    ],
    [
        '_id' => 'E7.01',
        'room_name' => 'Classroom E7.01',
        'block' => 'E',
        'floor' => '7.01',
        'type' => 'Classroom',
        'min_occupancy' => 1,
        'max_occupancy' => 30,
        'amenities' => 'Whiteboard, TV with Wireless Display, Desks and Chairs',
        'status' => 'Available'
    ],
    [
        '_id' => 'E7.02',
        'room_name' => 'Classroom E7.02',
        'block' => 'E',
        'floor' => '7.02',
        'type' => 'Classroom',
        'min_occupancy' => 1,
        'max_occupancy' => 30,
        'amenities' => 'Whiteboard, TV with Wireless Display, Desks and Chairs',
        'status' => 'Available'
    ],
    [
        '_id' => 'LT1',
        'room_name' => 'Lecture Theatre 1',
        'block' => 'B',
        'floor' => '1',
        'type' => 'Lecture Theatre',
        'min_occupancy' => 1,
        'max_occupancy' => 400,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ],
    [
        '_id' => 'LT2',
        'room_name' => 'Lecture Theatre 2',
        'block' => 'B',
        'floor' => '1',
        'type' => 'Lecture Theatre',
        'min_occupancy' => 1,
        'max_occupancy' => 200,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ],
    [
        '_id' => 'LT3',
        'room_name' => 'Lecture Theatre 3',
        'block' => 'C',
        'floor' => '1',
        'type' => 'Lecture Theatre',
        'min_occupancy' => 1,
        'max_occupancy' => 80,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ],
    [
        '_id' => 'LT4',
        'room_name' => 'Lecture Theatre 4',
        'block' => 'C',
        'floor' => '1',
        'type' => 'Lecture Theatre',
        'min_occupancy' => 1,
        'max_occupancy' => 80,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ],
    [
        '_id' => 'LT5',
        'room_name' => 'Lecture Theatre 5',
        'block' => 'C',
        'floor' => '1',
        'type' => 'Lecture Theatre',
        'min_occupancy' => 1,
        'max_occupancy' => 80,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ],
    [
        '_id' => 'LT6',
        'room_name' => 'Lecture Theatre 6',
        'block' => 'D',
        'floor' => '1',
        'type' => 'Lecture Theatre',
        'min_occupancy' => 1,
        'max_occupancy' => 80,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ],
    [
        '_id' => 'LT7',
        'room_name' => 'Lecture Theatre 7',
        'block' => 'D',
        'floor' => '1',
        'type' => 'Lecture Theatre',
        'min_occupancy' => 1,
        'max_occupancy' => 80,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ],
    [
        '_id' => 'LT8',
        'room_name' => 'Lecture Theatre 8',
        'block' => 'D',
        'floor' => '1',
        'type' => 'Lecture Theatre',
        'min_occupancy' => 1,
        'max_occupancy' => 80,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ],
    [
        '_id' => 'LT9',
        'room_name' => 'Lecture Theatre 9',
        'block' => 'D',
        'floor' => '1',
        'type' => 'Lecture Theatre',
        'min_occupancy' => 1,
        'max_occupancy' => 200,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ],
    [
        '_id' => 'LT10',
        'room_name' => 'Lecture Theatre 10',
        'block' => 'D',
        'floor' => '1',
        'type' => 'Lecture Theatre',
        'min_occupancy' => 1,
        'max_occupancy' => 150,
        'amenities' => 'Whiteboard, TV with Wireless Display',
        'status' => 'Available'
    ]
];

// Insert many documents
try {
    $result = $collection->insertMany($rooms);
    echo "Inserted " . $result->getInsertedCount() . " room(s).";
} catch (MongoDB\Driver\Exception\BulkWriteException $e) {
    echo "Insert failed: " . $e->getMessage();
}
?>
