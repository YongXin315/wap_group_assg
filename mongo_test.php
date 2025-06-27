<?php
require 'vendor/autoload.php';

try {
    // Connect to MongoDB with increased timeout
    $uri = "mongodb+srv://rootadmin:rootadmin@cluster0.ge5ruc5.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";
    $client = new MongoDB\Client($uri, [
        'serverSelectionTimeoutMS' => 5000,  // Increase timeout to 5 seconds
        'connectTimeoutMS' => 10000,        // Connection timeout
    ]);
    
    // Select database and collection
    $db = $client->taylors;
    $collection = $db->admin;
    
    // Insert a test document
    $result = $collection->insertOne([
        'test_field' => 'Hello MongoDB',
        'timestamp' => time() // Using regular PHP timestamp instead of MongoDB\BSON\UTCDateTime
    ]);
    
    echo "Document inserted with ID: " . $result->getInsertedId() . "<br>";
    
    // Retrieve the document
    $document = $collection->findOne(['_id' => $result->getInsertedId()]);
    
    echo "Retrieved document: <pre>";
    var_dump($document);
    echo "</pre>";
    
    echo "<p style='color:green;font-weight:bold;'>MongoDB connection successful and write operation verified!</p>";
    
} catch (Exception $e) {
    echo "<p style='color:red;font-weight:bold;'>MongoDB Error: " . $e->getMessage() . "</p>";
}
?>