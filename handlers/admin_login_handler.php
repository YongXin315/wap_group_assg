<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once(__DIR__ . '/../db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginInput = trim($_POST['admin_id']);
    $password = $_POST['admin_password'];

    $isEmail = filter_var($loginInput, FILTER_VALIDATE_EMAIL);

    try {
        // Search either by email or admin_id (case-insensitive)
        $queryField = $isEmail ? 'admin_email' : '_id';

        $admin = $db->admin->findOne([
            $queryField => [
                '$regex' => '^' . preg_quote($loginInput) . '$',
                '$options' => 'i'
            ]
        ]);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['user_role'] = 'admin';
            $_SESSION['admin_id'] = (string) $admin['_id'];
            $_SESSION['admin_name'] = $admin['admin_name'] ?? 'Admin';
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['LAST_ACTIVITY'] = time();
            session_regenerate_id(true);

            header('Location: ../admin_dashboard.php');
            exit();
        } else {
            $_SESSION['error'] = "❌ Invalid admin ID/email or password.";
            header('Location: ../login.php');
            exit();
        }

    } catch (Exception $e) {
        $_SESSION['error'] = "⚠️ Login error: " . $e->getMessage();
        error_log("Admin login error: " . $e->getMessage());
        header('Location: ../login.php');
        exit();
    }
} else {
    header('Location: ../login.php');
    exit();
}
