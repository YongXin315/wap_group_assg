<?php session_start(); ?>
<?php include './component/header.php'; ?>

<link rel="stylesheet" href="./assests/main.css">

<div class="signup-wrapper">
  <form method="POST" action="submit_booking.php" class="signup-form-container">
    <h2 style="text-align:center; margin-bottom: 1.5rem; font-size: 1.8rem; font-weight: 700;">
      Confirm Room Booking
    </h2>
    <p style="text-align:center; margin-bottom: 2rem; font-size: 1rem;">Selected Booking Summary</p>

    <h3 style="margin-top: 1.5rem; font-weight: 600;">Student Details</h3>
    <input type="text" name="student_id" placeholder="Student ID (e.g., 0312345)" required class="signup-input">
    <input type="text" name="full_name" placeholder="Full Name" required class="signup-input">

    <h3 style="margin-top: 2rem; font-weight: 600;">Booking Details</h3>

    <label for="start_time" style="text-align:left; display:block;">Start Time</label>
    <select id="start_time" name="start_time" required class="signup-input">
      <?php for ($h = 8; $h <= 22; $h++): ?>
        <option value="<?= sprintf('%02d:00', $h) ?>"><?= sprintf('%02d:00', $h) ?></option>
        <option value="<?= sprintf('%02d:30', $h) ?>"><?= sprintf('%02d:30', $h) ?></option>
      <?php endfor; ?>
    </select>

    <label for="end_time" style="text-align:left; display:block; margin-top: 10px;">End Time</label>
    <select id="end_time" name="end_time" required class="signup-input">
      <?php for ($h = 8; $h <= 22; $h++): ?>
        <option value="<?= sprintf('%02d:00', $h) ?>"><?= sprintf('%02d:00', $h) ?></option>
        <option value="<?= sprintf('%02d:30', $h) ?>"><?= sprintf('%02d:30', $h) ?></option>
      <?php endfor; ?>
    </select>

    <input type="text" name="purpose" placeholder="Purpose of Booking" required class="signup-input">
    <input type="number" name="people" placeholder="Number of People" min="1" required class="signup-input">

    <button type="submit" class="signup-button" style="margin-top: 20px;">Submit Booking</button>

    <p style="text-align:center; margin-top: 20px;">
      <a href="dashboard.php">Cancel</a>
    </p>
  </form>
</div>

<?php include './component/footer.php'; ?>
