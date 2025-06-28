<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<?php if (isset($_SESSION['error'])): ?>
  <div style="color: red; margin-bottom: 1rem;">
    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
  </div>
<?php endif; ?>

<div class="login-wrapper">
  <div class="login-form-container">
    <h2 class="login-title">Welcome to<br>Taylor's Room Booking System!</h2>
    <p class="login-subtitle">You are connecting to Taylor's Education</p>
    <h3 class="login-instruction">Sign in with your Taylor's account.</h3>

    <div class="login-tabs">
      <div class="tab active">Student</div>
      <div class="tab">Staff</div>
    </div>

    <form action="handlers/login_handler.php" method="POST" class="student-mode">
      <input type="text" name="student_id" class="login-input" placeholder="Email Address or Student ID" required>
      <input type="password" name="password" class="login-input" placeholder="Password" required>
      <button type="submit" class="login-button">Sign In</button>
    </form>

    <div class="login-footer">
      <a href="#" class="login-link">Forgot Password?</a>
      <p class="login-register">Don't have an account? <a href="signup.php">Sign Up here</a></p>
    </div>
  </div>
</div>

<?php
// Initialize the login toggle functionality
require_once dirname(__DIR__) . '/functions.php';
initializeLoginToggle();
?>
