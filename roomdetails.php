<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'components/header.php';
?>

<style>
  .signup-wrapper {
    display: flex;
    justify-content: center;
    padding: 2rem;
  }

  .signup-form-container {
    max-width: 600px;
    width: 100%;
    background: #fff;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  }

  .calendar {
    text-align: center;
  }

  .calendar-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    align-items: center;
  }

  .calendar-header button {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
  }

  .calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 10px;
  }

  .day-name {
    font-weight: bold;
    color: #444;
  }

  .day {
    padding: 10px;
    background-color: #f0f0f0;
    border-radius: 6px;
  }

  .day.today {
    background-color: #d32f2f;
    color: #fff;
    border-radius: 50%;
  }

  .calendar-actions {
    margin-top: 30px;
  }

  .calendar-actions a {
    display: inline-block;
    margin-top: 10px;
    text-decoration: none;
    font-weight: bold;
  }

  .back-link {
    color: #d32f2f;
  }

  .signup-button {
    background-color: #d32f2f;
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
  }
</style>

<div class="signup-wrapper">
  <div class="signup-form-container">
    <div class="calendar">

      <div class="calendar-header">
        <button onclick="changeMonth(-1)">&lt;</button>
        <div id="monthYearDisplay"></div>
        <button onclick="changeMonth(1)">&gt;</button>
      </div>

      <div class="calendar-grid" id="calendarGrid">
        <?php foreach (['S','M','T','W','T','F','S'] as $day): ?>
          <div class="day-name"><?= $day ?></div>
        <?php endforeach; ?>
        <!-- JS-generated days go here -->
      </div>

      <div class="calendar-actions">
        <a href="live_view.php" class="back-link">← Back to Live View</a>
        <a href="confirm_booking.php" class="signup-button">Book this room</a>
      </div>

    </div>
  </div>
</div>

<?php include './component/footer.php'; ?>

<script>
  const calendarGrid = document.getElementById("calendarGrid");
  const monthYearDisplay = document.getElementById("monthYearDisplay");
  let currentDate = new Date();

  function renderCalendar(date) {
    const year = date.getFullYear();
    const month = date.getMonth();
    const today = new Date();

    monthYearDisplay.textContent = date.toLocaleString('default', { month: 'long', year: 'numeric' });

    // Clear old days
    document.querySelectorAll(".calendar-grid .day").forEach(el => el.remove());

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    // Add empty cells for alignment
    for (let i = 0; i < firstDay; i++) {
      calendarGrid.appendChild(document.createElement("div"));
    }

    for (let d = 1; d <= daysInMonth; d++) {
      const dayCell = document.createElement("div");
      dayCell.className = "day";
      dayCell.textContent = d;

      if (d === today.getDate() && month === today.getMonth() && year === today.getFullYear()) {
        dayCell.classList.add("today");
      }

      calendarGrid.appendChild(dayCell);
    }
  }

  function changeMonth(offset) {
    currentDate.setMonth(currentDate.getMonth() + offset);
    renderCalendar(currentDate);
  }

  renderCalendar(currentDate);
</script>
