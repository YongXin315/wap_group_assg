<?php
require_once __DIR__ . '/vendor/autoload.php';

use MongoDB\Client;
use Dotenv\Dotenv;

// Try to load environment variables from .env file if it exists
try {
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    $uri = $_ENV['MONGO_URI'] ?? '';
} catch (Exception $e) {
    // If .env file doesn't exist, use the connection string directly
    $uri = "mongodb+srv://rootadmin:rootadmin@cluster0.ge5ruc5.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";
}

if (empty($uri)) {
    die("MongoDB URI is not defined.");
}

$client = new Client($uri, [
    'serverSelectionTimeoutMS' => 5000,
    'connectTimeoutMS' => 10000,
]);

// Choose your database (changed from 'taylors' to 'wap_system')
$db = $client->wap_system;
