<?php
require 'vendor/autoload.php'; // Composer autoload

$uri = "mongodb+srv://rootadmin:rootadmin@cluster0.ge5ruc5.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";
$client = new MongoDB\Client($uri);

$db = $client->taylors; // your database name
