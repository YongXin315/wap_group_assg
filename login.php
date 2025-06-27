<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <?php include_once 'assests/styles.php'; ?> <!-- This is correct -->
</head>
<body>
  <?php include_once 'component/header.php'; ?>
  <?php include_once 'component/login_form.php'; ?>
  <?php include_once 'component/footer.php'; ?>
</body>
</html>
