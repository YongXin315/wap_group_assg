<?php session_start(); ?>
<?php include './component/header.php'; ?>
<link rel="stylesheet" href="./assests/styles.php"> <!-- Fixed: Changed assets to assests -->

<div class="signup-wrapper">
  <form method="POST" action="handlers/signup_handler.php" class="signup-form-container"> <!-- Fixed: Removed ../ -->
    <h2 style="text-align:center; margin-bottom: 1.5rem; font-size: 1.8rem; font-weight: 700;">Create your Taylor's account to access Room Booking System.</h2>

    <input type="text" name="student_name" placeholder="Full Name" required class="signup-input">
    <input type="text" name="student_id" placeholder="Student ID" required class="signup-input">
    <input type="email" name="student_email" placeholder="Taylor’s Email Address (@sd.taylors.edu.my)" pattern=".+@sd\.taylors\.edu\.my" required class="signup-input">
    <input type="password" name="password" placeholder="Password" required class="signup-input">
    <input type="password" name="confirm_password" placeholder="Confirm Password" required class="signup-input">

    <button type="submit" class="signup-button">Sign Up</button>

    <p style="text-align:center; margin-top: 20px;">Already have an account?<br>
      <a href="login.php">Sign in here</a>
    </p>
  </form>
</div>

<?php include './component/footer.php'; ?>

<script>
  document.querySelector("form").addEventListener("submit", function(e) {
    const pw = document.querySelector("[name='password']").value;
    const cpw = document.querySelector("[name='confirm_password']").value;
    if (pw !== cpw) {
      e.preventDefault();
      alert("Passwords do not match.");
    }
  });
</script>

<mcfile name="signup_handler.php" path="signup_handler.php"></mcfile>
<mcfolder name="handler" path="handlers"></mcfolder>