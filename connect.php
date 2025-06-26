<!-- testing purpose -->

<?php
require 'vendor/autoload.php'; // Include Composer autoload

// Replace with your own MongoDB URI
$uri = "mongodb+srv://rootadmin:rootadmin@cluster0.ge5ruc5.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";


// Create a client
$client = new MongoDB\Client($uri);

// Select database and collection
$db = $client->taylors;
$admin = $db->admin;

// Fetch one document (test query)
$admin = $admin->findOne();

echo "<h2>MongoDB Atlas Connection Successful ✅</h2>";
echo "<pre>";
print_r($admin);
echo "</pre>";
?>
