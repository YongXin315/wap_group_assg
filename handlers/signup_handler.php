<?php
session_start();

require 'vendor/autoload.php';

use MongoDB\Client;
use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$mongoUri = $_ENV['MONGO_URI'] ?? '';
if (!$mongoUri) {
    die("MongoDB URI is not set in the .env file.");
}

$client = new Client($mongoUri);
$collection = $client->wap_system->students;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id']);
    $student_email = trim($_POST['student_email']);
    $student_name = trim($_POST['student_name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Email format check
    if (!preg_match("/@sd\.taylors\.edu\.my$/", $student_email)) {
        die("Error: Email must be a valid Taylor's student email.");
    }

    // 2. Password match check
    if ($password !== $confirm_password) {
        die("Error: Passwords do not match.");
    }

    // 3. Check for duplicates
    $existing = $collection->findOne([
        '$or' => [
            ['student_id' => $student_id],
            ['student_email' => $student_email]
        ]
    ]);
    if ($existing) {
        die("Error: Student ID or Email already registered.");
    }

    // 4. Insert user
    $collection->insertOne([
        'student_id' => $student_id,
        'student_email' => $student_email,
        'student_name' => $student_name,
        'password' => password_hash($password, PASSWORD_DEFAULT)
    ]);

    // 5. Start session
    $_SESSION['student_id'] = $student_id;
    $_SESSION['student_name'] = $student_name;

    // 6. Redirect
    header("Location: ../index.php");
    exit();
} else {
    echo "Invalid request.";
}
?>
