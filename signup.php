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
    const name = document.querySelector("[name='student_name']").value.trim();
    const studentId = document.querySelector("[name='student_id']").value.trim();
    const email = document.querySelector("[name='student_email']").value.trim();
    const pw = document.querySelector("[name='password']").value;
    const cpw = document.querySelector("[name='confirm_password']").value;

    // Name: only letters and spaces
    if (!/^[A-Za-z\s]+$/.test(name)) {
      e.preventDefault();
      alert("Name must contain only letters and spaces.");
      return;
    }

    // Student ID: must be exactly 7 digits
    if (!/^\d{7}$/.test(studentId)) {
      e.preventDefault();
      alert("Student ID must be exactly 7 digits.");
      return;
    }

    // Email: must match @sd.taylors.edu.my
    if (!/^.+@sd\.taylors\.edu\.my$/.test(email)) {
      e.preventDefault();
      alert("Email must be a valid Taylor's email address (@sd.taylors.edu.my).");
      return;
    }

    // Password match
    if (pw !== cpw) {
      e.preventDefault();
      alert("Passwords do not match.");
      return;
    }

    // Check for empty fields (should be covered by HTML5, but extra safety)
    if (!name || !studentId || !email || !pw || !cpw) {
      e.preventDefault();
      alert("Please fill in all required fields.");
      return;
    }
  });
</script>
