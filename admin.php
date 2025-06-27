<?php
require 'vendor/autoload.php'; // Include Composer autoload

// Replace with your own MongoDB URI
$uri = "mongodb+srv://rootadmin:rootadmin@cluster0.ge5ruc5.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";

// Connect MongoDB Client
$client = new MongoDB\Client($uri);

// Select database and collection
$db = $client->taylors;
$collection = $db->admin;

$admin = [
    [
        '_id' => 'ADM00001',
        'admin_email' => 'taylorsadmin@taylors.edu.my',
        'password' => password_hash('admin123', PASSWORD_DEFAULT)
    ]
];

// Insert many documents
try {
    $result = $collection->insertOne($admin);
    echo "Inserted " . $result->getInsertedCount() . " admin(s).";
} catch (MongoDB\Driver\Exception\BulkWriteException $e) {
    echo "Insert failed: " . $e->getMessage();
}
?>
