<?php
session_start();

// Check if user is logged in, if not redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'components/header.php';
?>

<link rel="stylesheet" href="./assests/main.css">

<div class="signup-wrapper">
  <div class="signup-form-container">

    <!-- Calendar -->
    <div class="calendar" style="text-align:center;">

      <!-- Calendar Header -->
      <div class="calendar-header" style="display:flex; justify-content:space-between; margin-bottom:10px;">
        <button onclick="changeMonth(-1)" style="background:none; border:none; font-size:18px; cursor:pointer;">&lt;</button>
        <div id="monthYear" style="font-weight:bold;"></div>
        <button onclick="changeMonth(1)" style="background:none; border:none; font-size:18px; cursor:pointer;">&gt;</button>
      </div>

      <!-- Calendar Grid -->
      <div class="calendar-grid" id="calendarGrid" style="display:grid; grid-template-columns: repeat(7, 1fr); gap:10px;">
        <div class="day-name">S</div>
        <div class="day-name">M</div>
        <div class="day-name">T</div>
        <div class="day-name">W</div>
        <div class="day-name">T</div>
        <div class="day-name">F</div>
        <div class="day-name">S</div>
        <!-- Calendar days will be generated here -->
      </div>

      <!-- Back Button (Above Book Button) -->
      <p style="margin-top: 30px;">
        <a href="live_view.php" style="text-decoration:none; color: #d32f2f; font-weight:bold;">
          ← Back to Live View
        </a>
      </p>

      <!-- Book Now Button -->
      <a href="confirm_booking.php" class="signup-button" style="margin-top: 15px; display:inline-block;">
        Book this room
      </a>
    </div>

  </div>
</div>

<?php include './component/footer.php'; ?>

<script>
  const calendarGrid = document.getElementById("calendarGrid");
  const monthYear = document.getElementById("monthYear");
  let currentDate = new Date();

  function generateCalendar(date) {
    const year = date.getFullYear();
    const month = date.getMonth();
    const today = new Date();

    monthYear.textContent = date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

    // Remove old day cells
    const oldDays = calendarGrid.querySelectorAll(".day");
    oldDays.forEach(el => el.remove());

    const firstDay = new Date(year, month, 1).getDay();
    const totalDays = new Date(year, month + 1, 0).getDate();

    // Empty slots before 1st day
    for (let i = 0; i < firstDay; i++) {
      const empty = document.createElement("div");
      calendarGrid.appendChild(empty);
    }

    // Generate calendar days
    for (let d = 1; d <= totalDays; d++) {
      const dayDiv = document.createElement("div");
      dayDiv.className = "day";
      dayDiv.textContent = d;

      if (
        d === today.getDate() &&
        month === today.getMonth() &&
        year === today.getFullYear()
      ) {
        dayDiv.style.backgroundColor = "#d32f2f";
        dayDiv.style.color = "white";
        dayDiv.style.borderRadius = "50%";
      }

      calendarGrid.appendChild(dayDiv);
    }
  }

  function changeMonth(offset) {
    currentDate.setMonth(currentDate.getMonth() + offset);
    generateCalendar(currentDate);
  }

  generateCalendar(currentDate);
</script>
