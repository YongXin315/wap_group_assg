<!-- siugnup page for user -->

<?php 
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = $_POST['student_id'];
    $student_email = $_POST['student_email'];
    $student_name = $_POST['student_name'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (student_id, student_email, student_name, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$student_id, $student_email, $student_name, $password]);

    header("Location: landingpage.php");
    exit();
}
?>
<!-- check whether password match with confirm password -->
<!-- check whether is Taylor's email: '@sd.taylors.edu.my'-->