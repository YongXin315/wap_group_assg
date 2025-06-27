<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once(__DIR__ . '/../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginInput = trim($_POST['student_id']);
    $password = $_POST['password'];

    $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);

    try {
        // Search either by email or student_id (case-insensitive)
        $queryField = $isEmail ? 'student_email' : 'student_id';

        $user = $db->students->findOne([
            $queryField => [
                '$regex' => '^' . preg_quote($loginInput) . '$',
                '$options' => 'i' // case-insensitive match
            ]
        ]);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['student_id'] = $user['student_id'];
            $_SESSION['student_name'] = $user['student_name'];
            session_regenerate_id(true);

            header('Location: ../roomavailability.php');
            exit();
        } else {
            $_SESSION['error'] = "❌ Invalid student ID/email or password.";
            header('Location: ../login.php');
            exit();
        }

    } catch (Exception $e) {
        $_SESSION['error'] = "⚠️ Login error: " . $e->getMessage();
        error_log("Login error: " . $e->getMessage());
        header('Location: ../login.php');
        exit();
    }
} else {
    header('Location: ../login.php');
    exit();
}
