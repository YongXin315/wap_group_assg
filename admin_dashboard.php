<?php
include 'component/header.php';
$pageTitle = "Booking Management Dashboard";

session_start();
$adminName = $_SESSION['admin_name'] ?? $_SESSION['admin_id'] ?? 'Admin';
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $pageTitle; ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="font-inter">
<div class="w-full p-6">
  <div class="text-3xl font-bold mb-6">Booking Management Dashboard</div>

  <div class="text-sm text-right text-gray-500 mb-4">
    Logged in as: <span class="font-semibold text-black"><?= htmlspecialchars($adminName) ?></span>
</div>


  <!-- Filter Form -->
  <form id="filterForm" class="flex flex-wrap gap-4 mb-6">
    <input type="date" name="date" class="border p-2 rounded" placeholder="Select Date">
    <input type="text" name="room" class="border p-2 rounded" placeholder="Search Room Name">
    <select name="status" class="border p-2 rounded">
      <option value="all">All Status</option>
      <option value="approved">Approved</option>
      <option value="cancelled">Cancelled</option>
    </select>
    <input type="text" name="search" class="border p-2 rounded" placeholder="Search by Student Name or ID">
    <select name="sort_by" class="border p-2 rounded">
      <option value="created_at">Created Time</option>
      <option value="booking_date">Booking Date</option>
    </select>
    <button type="button" onclick="resetFilters()" class="bg-red-600 text-white px-4 py-2 rounded">Reset</button>
  </form>

  <!-- Booking Table -->
  <div class="overflow-auto">
    <table class="min-w-full border">
      <thead>
      <tr class="bg-gray-200">
        <th class="px-4 py-2">Booking Date</th>
        <th class="px-4 py-2">Room ID</th>
        <th class="px-4 py-2">Room Name</th>
        <th class="px-4 py-2">Time Slot</th>
        <th class="px-4 py-2">Student ID</th>
        <th class="px-4 py-2">Student Name</th>
        <th class="px-4 py-2">Purpose</th>
        <th class="px-4 py-2">Status</th>
        <th class="px-4 py-2">Action</th>
      </tr>
      </thead>
      <tbody id="bookingTableBody"></tbody>
    </table>
  </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("filterForm");
  const tbody = document.getElementById("bookingTableBody");

  function getQueryParams() {
    const formData = new FormData(form);
    const params = new URLSearchParams();
    for (const [key, value] of formData.entries()) {
      if (value) params.append(key, value);
    }
    return params.toString();
  }

  function fetchBookings() {
    const query = getQueryParams();
    fetch('admin_dashboard_data.php?' + query)
      .then(res => res.json())
      .then(data => {
        tbody.innerHTML = "";
        data.forEach(b => {
          const tr = document.createElement("tr");
          tr.className = "border-t";
          tr.innerHTML = `
            <td class="px-4 py-2">${b.booking_date}</td>
            <td class="px-4 py-2">${b.room_id}</td>
            <td class="px-4 py-2">${b.room_name}</td>
            <td class="px-4 py-2">${b.time_slot}</td>
            <td class="px-4 py-2">${b.student_id}</td>
            <td class="px-4 py-2">${b.full_name}</td>
            <td class="px-4 py-2">${b.purpose}</td>
            <td class="px-4 py-2">${b.status}</td>
            <td class="px-4 py-2">
              ${b.status === 'approved' ? 
                `<button class="text-red-500 underline" onclick="cancelBooking('${b._id}', '${b.full_name}')">Cancel</button>` : 
                '-'}
            </td>
          `;
          tbody.appendChild(tr);
        });
      });
  }

  form.addEventListener("change", fetchBookings);
  form.addEventListener("input", () => {
    clearTimeout(window.filterTimeout);
    window.filterTimeout = setTimeout(fetchBookings, 600);
  });

  window.resetFilters = () => {
    form.reset();
    fetchBookings();
  };

  window.cancelBooking = (bookingId, studentName) => {
    if (!confirm(`Cancel booking for ${studentName}?`)) return;

    fetch('handlers/update_booking_status.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: bookingId, action: 'cancelled' })
    })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        alert(`Booking for ${studentName} cancelled.`);
        fetchBookings();
      } else {
        alert("Cancel failed: " + data.message);
      }
    })
    .catch(err => {
      console.error(err);
      alert("Error cancelling booking.");
    });
  };

  // Initial fetch
  fetchBookings();
});
</script>

</body>
</html>

<?php include 'component/footer.php'; ?>

