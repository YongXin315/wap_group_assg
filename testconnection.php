<?php
require 'vendor/autoload.php';

// Your MongoDB Atlas connection string (make sure the password is correct)
$uri = "mongodb+srv://rootadmin:rootadmin@cluster0.ge5ruc5.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";

$client = new MongoDB\Client($uri);

try {
    $databases = $client->listDatabases();
    echo "Connected! Databases:\n";
    foreach ($databases as $db) {
        echo "- " . $db->getName() . "\n";
    }
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}
