<?php
include 'header.php';
?>

<!-- Main Container -->
<div style="align-self: stretch; flex-direction: column; justify-content: center; align-items: center; display: flex">

  <!-- Top Bar -->
  <div style="padding: 20px 160px; background: white; display: flex; justify-content: center;">
    <div style="max-width: 960px; width: 100%;">

      <!-- Title -->
      <div style="padding: 16px; display: flex; justify-content: space-between; flex-wrap: wrap;">
        <div style="min-width: 288px;">
          <div style="font-size: 32px; font-family: Inter; font-weight: 700; color: #1A0F0F;">Manage Rooms</div>
        </div>
      </div>

      <!-- Filter Inputs -->
      <div style="padding: 12px 16px; display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">

        <!-- Search by Room Name (Text Input) -->
        <input
          type="text"
          id="filterRoomName"
          placeholder="Search by Room Name"
          style="padding: 8px 12px; border: 1px solid #E5D1D4; border-radius: 8px; font-size: 14px; font-family: Inter; width: 200px; box-sizing: border-box;"
        />

        <!-- All Types (Dropdown) -->
        <select
          id="filterRoomType"
          style="padding: 8px 12px; border: 1px solid #E5D1D4; border-radius: 8px; font-size: 14px; font-family: Inter; width: 200px; box-sizing: border-box;"
        >
          <option value="All">All Types</option>
          <option value="Discussion Room">Discussion Room</option>
          <option value="Computer Lab">Computer Lab</option>
          <option value="Design Lab">Design Lab</option>
          <option value="Classroom">Classroom</option>
        </select>

        <!-- All Status (Dropdown) -->
        <select
          id="filterStatus"
          style="padding: 8px 12px; border: 1px solid #E5D1D4; border-radius: 8px; font-size: 14px; font-family: Inter; width: 200px; box-sizing: border-box;"
        >
          <option value="All">All Status</option>
          <option value="Available">Available</option>
          <option value="Unavailable">Unavailable</option>
          <option value="Under Maintenance">Under Maintenance</option>
        </select>

        <!-- Add Room Button -->
        <a href="add_room.php" style="background: #C3272B; padding: 0 16px; height: 40px; border-radius: 20px; display: flex; align-items: center; color: white; text-decoration: none; margin-left: auto;">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white" width="20px" height="20px">
            <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z"/>
          </svg>
          <span style="margin-left: 8px; font-weight: 700; font-size: 14px;">Add Room</span>
        </a>

      </div>

      <!-- Table Header -->
      <div style="padding: 12px 16px; display: flex; justify-content: center;">
        <div style="background: white; border-radius: 12px; border: 1px solid #E5D1D4;">
          <div style="display: flex;">
            <?php
            $headers = ['Room Name / Code', 'Block / Floor', 'Room Type', 'Capacity', 'Status', ''];
            $widths = ['145px', '144px', '171px', '161px', '206px', '100px'];
            foreach ($headers as $index => $text) {
              echo "<div style=\"width: {$widths[$index]}; padding: 12px 16px; font-weight: 500;\">$text</div>";
            }
            ?>
          </div>

          <!-- Room List -->
          <div id="roomTableBody">
            <?php
            $rooms = [
              ['Discussion Room 3.2', 'Library, Level 3', 'Discussion Room', '6 people', 'Available', 1],
              ['Computer Lab E7.01', 'Block E, Level 7', 'Computer Lab', '30 people', 'Available', 2],
              ['Classroom C2.01', 'Block C, Level 2', 'Classroom', '30 people', 'Available', 3],
              ['Discussion Room 5.1', 'Library, Level 5', 'Discussion Room', '6 people', 'Unavailable', 4],
              ['Design Lab 02', 'Block E, Level 1', 'Design Lab', '25 people', 'Under Maintenance', 5],
              ['Classroom D4.03', 'Block D, Level 4', 'Classroom', '40 people', 'Available', 6],
              ['Discussion Room 4.1', 'Library, Level 4', 'Discussion Room', '5 people', 'Available', 7]
            ];

            foreach ($rooms as [$name, $location, $type, $capacity, $status, $id]) {
              echo "<div class='room-row' data-room-type=\"$type\" data-status=\"$status\" style=\"display: flex; border-top: 1px solid #E5E8EB; height: 72px;\">";
              echo "<div class='room-name' style=\"width: 145px; padding: 8px 16px; display: flex; align-items: center;\">$name</div>";
              echo "<div style=\"width: 144px; padding: 8px 16px; display: flex; align-items: center;\">$location</div>";
              echo "<div style=\"width: 171px; padding: 8px 16px; display: flex; align-items: center;\">$type</div>";
              echo "<div style=\"width: 161px; padding: 8px 16px; display: flex; align-items: center;\">$capacity</div>";
              echo "<div style=\"width: 206px; padding: 8px 16px; display: flex; align-items: center;\">
                      <div style=\"background: #F2E8E8; border-radius: 16px; padding: 0 16px; height: 32px; display: flex; align-items: center;\">$status</div>
                    </div>";
              echo "<div style=\"width: 100px; padding: 8px 16px; display: flex; align-items: center;\">
                      <a href=\"edit_room.php?room_id=$id\" style=\"color: #915457; font-weight: 700; text-decoration: none;\">Edit</a>
                    </div>";
              echo "</div>";
            }
            ?>
          </div>

        </div>
      </div>

    </div>
  </div>

</div>

<!-- JS for Filter Functionality -->
<script>
const roomNameInput = document.getElementById('filterRoomName');
const roomTypeSelect = document.getElementById('filterRoomType');
const statusSelect = document.getElementById('filterStatus');

function filterRooms() {
  const roomNameVal = roomNameInput.value.toLowerCase();
  const roomTypeVal = roomTypeSelect.value;
  const statusVal = statusSelect.value;

  const rows = document.querySelectorAll('.room-row');

  rows.forEach(row => {
    const name = row.querySelector('.room-name').textContent.toLowerCase();
    const type = row.getAttribute('data-room-type');
    const status = row.getAttribute('data-status');

    const matchName = name.includes(roomNameVal);
    const matchType = (roomTypeVal === 'All' || type === roomTypeVal);
    const matchStatus = (statusVal === 'All' || status === statusVal);

    if (matchName && matchType && matchStatus) {
      row.style.display = 'flex';
    } else {
      row.style.display = 'none';
    }
  });
}

// Attach events to filter inputs
roomNameInput.addEventListener('input', filterRooms);
roomTypeSelect.addEventListener('change', filterRooms);
statusSelect.addEventListener('change', filterRooms);
</script>

<?php
include 'footer.php';
?>


<!-- integrate with database search later (search by room name - fetch data from the table) & add room function (add into db)-->










