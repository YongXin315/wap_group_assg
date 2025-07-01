<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <style>
    /* ===== LOGIN PAGE STYLES ===== */
    .login-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f8f8;
    }
    .login-form-container {
        background-color: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: border-left-color 0.3s ease;
        width: 800px;
        max-width: 90%;
        margin: 0 auto;
    }
    .login-title {
        font-size: 28px;
        font-weight: bold;
        color: #171212;
        margin-bottom: 12px;
    }
    .login-subtitle,
    .login-instruction {
        font-size: 16px;
        color: #876363;
        margin-bottom: 16px;
    }
    .login-form input {
        width: 100%;
        padding: 12px 16px;
        margin-bottom: 16px;
        border-radius: 8px;
        border: 1px solid #ccc;
        font-size: 1rem;
    }
    .login-button {
        width: 100%;
        padding: 12px;
        background: #C3272B;
        color: white;
        font-weight: bold;
        border: none;
        border-radius: 20px;
        cursor: pointer;
        transition: background 0.3s;
    }
    .login-button:hover {
        background: #a02024;
    }
    .login-footer {
        margin-top: 20px;
        font-size: 14px;
        color: #876363;
    }
    .login-link,
    .login-register a {
        color: #C3272B;
        text-decoration: none;
        font-weight: bold;
    }
    .login-tabs {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 20px;
    }
    .login-tabs .tab {
        font-size: 14px;
        font-weight: bold;
        padding-bottom: 5px;
        cursor: pointer;
        color: #876363;
        border-bottom: 3px solid #E5E8EB;
        transition: all 0.3s ease;
        user-select: none;
    }
    .login-tabs .tab:hover {
        background-color: rgba(195, 39, 43, 0.1);
    }
    .login-tabs .tab.active {
        color: #C3272B;
        border-color: #C3272B;
    }
    .login-tabs .tab.active.student {
        color: #C3272B;
        border-color: #C3272B;
    }
    .login-tabs .tab.active.staff {
        color: #2E8B57;
        border-color: #2E8B57;
    }
    .login-input {
        width: 100%;
        padding: 12px 16px;
        margin-bottom: 16px;
        border-radius: 8px;
        border: 1px solid #E5D1D1;
        font-size: 1rem;
        color: #915457;
        transition: border-color 0.3s ease;
    }
    .login-input:focus {
        outline: none;
        border-color: #C3272B;
        box-shadow: 0 0 0 2px rgba(195, 39, 43, 0.1);
    }
    .login-input::placeholder {
        color: #915457;
        opacity: 0.7;
    }
    /* ===== LOGIN TOGGLE STYLES ===== */
    .login-form-container.student-mode {
        border-left: 4px solid #C3272B;
    }
    .login-form-container.admin-mode {
        border-left: 4px solid #2E8B57;
    }
    form.student-mode,
    form.admin-mode {
        transition: all 0.3s ease;
    }
    form.admin-mode .login-button {
        background: #2E8B57;
    }
    form.admin-mode .login-button:hover {
        background: #1e6b47;
    }
    @media (max-width: 768px) {
      .login-form-container {
        padding: 20px;
        width: 100%;
      }
    }
    @media (max-width: 480px) {
      .login-title {
        font-size: 20px;
      }
      .login-form-container {
        padding: 10px;
      }
    }
  </style>
</head>

<body>
  <?php include_once 'component/header.php'; ?>

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

  <script>
  document.addEventListener('DOMContentLoaded', function() {
      const tabs = document.querySelectorAll('.login-tabs .tab');
      const form = document.querySelector('form[action*="login_handler.php"]');
      const studentIdInput = document.querySelector('input[name="student_id"]');
      const passwordInput = document.querySelector('input[name="password"]');
      
      // Set initial state
      let currentMode = 'student';
      
      // Add click event listeners to tabs
      tabs.forEach(tab => {
          tab.addEventListener('click', function() {
              // Remove active class from all tabs
              tabs.forEach(t => t.classList.remove('active'));
              
              // Add active class to clicked tab
              this.classList.add('active');
              
              // Update current mode
              currentMode = this.textContent.toLowerCase();
              
              // Update form action and input names based on mode
              if (currentMode === 'student') {
                  form.action = 'handlers/login_handler.php';
                  studentIdInput.name = 'student_id';
                  studentIdInput.placeholder = 'Email Address or Student ID';
                  passwordInput.name = 'password';
                  
                  // Update form styling for student mode
                  form.classList.remove('admin-mode');
                  form.classList.add('student-mode');
              } else if (currentMode === 'staff') {
                  form.action = 'handlers/admin_login_handler.php';
                  studentIdInput.name = 'admin_id';
                  studentIdInput.placeholder = 'Admin ID or Email';
                  passwordInput.name = 'admin_password';
                  
                  // Update form styling for admin mode
                  form.classList.remove('student-mode');
                  form.classList.add('admin-mode');
              }
              
              // Clear form inputs when switching modes
              studentIdInput.value = '';
              passwordInput.value = '';
              
              // Update visual feedback
              updateToggleVisuals(currentMode);
          });
      });
      
      function updateToggleVisuals(mode) {
          const container = document.querySelector('.login-form-container');
          const title = document.querySelector('.login-title');
          const subtitle = document.querySelector('.login-subtitle');
          const instruction = document.querySelector('.login-instruction');
          
          if (mode === 'student') {
              container.style.borderLeft = '4px solid #C3272B';
              title.innerHTML = 'Welcome to<br>Taylor\'s Room Booking System!';
              subtitle.textContent = 'You are connecting to Taylor\'s Education';
              instruction.textContent = 'Sign in with your Taylor\'s account.';
          } else if (mode === 'staff') {
              container.style.borderLeft = '4px solid #2E8B57';
              title.innerHTML = 'Admin Portal<br>Taylor\'s Room Booking System';
              subtitle.textContent = 'Administrative Access';
              instruction.textContent = 'Sign in with your admin credentials.';
          }
      }
      
      // Initialize with student mode
      updateToggleVisuals('student');
  });
  </script>

  <?php include_once 'component/footer.php'; ?>
</body>
</html>
