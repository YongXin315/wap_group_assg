<?php
$pageTitle = "Pending Booking Requests";
$extraCss = "style.css"; // Assuming style.css contains general site styles
include 'component/header.php';

// Function to generate a random number of people range
function generateNumPeopleRange() {
    $ranges = ["1-10", "11-20", "21-30", "31-40", "41-50"];
    return $ranges[array_rand($ranges)];
}

// --- PHP: Simulate Data Fetching ---
// In a real application, this data would come from a database query.
// Each array element represents a booking request.
$pendingBookings = [
    [
        'room_name' => 'Discussion Room A',
        'room_type' => 'discussion', // Added for easier filtering
        'date' => '2024-07-15',
        'time' => '10:00 AM - 11:00 AM',
        'student_id' => 'STU12345',
        'purpose' => 'Group Study',
        'num_people' => generateNumPeopleRange() // New field
    ],
    [
        'room_name' => 'Classroom B',
        'room_type' => 'classroom', // Added for easier filtering
        'date' => '2024-07-15',
        'time' => '2:00 PM - 4:00 PM',
        'student_id' => 'STU67890',
        'purpose' => 'Project Meeting',
        'num_people' => generateNumPeopleRange() // New field
    ],
    [
        'room_name' => 'Discussion Room C',
        'room_type' => 'discussion', // Added for easier filtering
        'date' => '2024-07-16',
        'time' => '11:00 AM - 12:00 PM',
        'student_id' => 'STU24680',
        'purpose' => 'Presentation Practice',
        'num_people' => generateNumPeopleRange() // New field
    ],
    [
        'room_name' => 'Classroom A',
        'room_type' => 'classroom', // Added for easier filtering
        'date' => '2024-07-16',
        'time' => '1:00 PM - 3:00 PM',
        'student_id' => 'STU13579',
        'purpose' => 'Study Session',
        'num_people' => generateNumPeopleRange() // New field
    ],
    [
        'room_name' => 'Computer Lab 1',
        'room_type' => 'computer_lab', // Added for easier filtering
        'date' => '2024-07-17',
        'time' => '09:00 AM - 10:00 AM',
        'student_id' => 'STU97531',
        'purpose' => 'Programming Practice',
        'num_people' => generateNumPeopleRange() // New field
    ],
    [
        'room_name' => 'Discussion Room D',
        'room_type' => 'discussion', // Added for easier filtering
        'date' => '2024-07-17',
        'time' => '01:00 PM - 02:00 PM',
        'student_id' => 'STU54321',
        'purpose' => 'Brainstorming',
        'num_people' => generateNumPeopleRange() // New field
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <?php if (isset($extraCss)) { echo "<link rel='stylesheet' href='$extraCss'>"; } ?>
    <style>
        /* (styles remain unchanged - omitted here for brevity, please keep as-is from your code) */
    </style>
</head>
<body>

<div class="main-content-container">
    <div class="inner-content-wrapper">
        <div class="content-area">
            <div class="page-title-section">
                <div class="page-title">
                    <h1>Pending Booking Requests</h1>
                </div>
            </div>

            <div class="filter-search-section">
                <div class="filter-dropdown-container">
                    <select id="roomTypeFilter" class="room-type-select">
                        <option value="all">All Room Types</option>
                        <option value="discussion">Discussion Room</option>
                        <option value="classroom">Classroom</option>
                        <option value="computer_lab">Computer Lab</option>
                    </select>
                </div>
            </div>

            <div class="booking-table-container">
                <div class="booking-table-wrapper">
                    <div class="table-header">
                        <div class="table-header-cell">Room Name</div>
                        <div class="table-header-cell">Booking Date</div>
                        <div class="table-header-cell">Time</div>
                        <div class="table-header-cell">Student ID</div>
                        <div class="table-header-cell">Purpose</div>
                        <div class="table-header-cell">Number of People</div>
                        <div class="table-header-cell">Actions</div>
                    </div>

                    <div id="bookingTableBody" class="table-body">
                        <?php foreach ($pendingBookings as $booking): ?>
                        <div class="booking-row"
                             data-room-type="<?= htmlspecialchars($booking['room_type']); ?>"
                             data-student-id="<?= htmlspecialchars($booking['student_id']); ?>"
                             data-student-name="">
                            <div class="booking-cell"><?= htmlspecialchars($booking['room_name']); ?></div>
                            <div class="booking-cell"><?= htmlspecialchars($booking['date']); ?></div>
                            <div class="booking-cell"><?= htmlspecialchars($booking['time']); ?></div>
                            <div class="booking-cell student-id-cell"><?= htmlspecialchars($booking['student_id']); ?></div>
                            <div class="booking-cell"><?= htmlspecialchars($booking['purpose']); ?></div>
                            <div class="booking-cell"><?= htmlspecialchars($booking['num_people']); ?></div>
                            <div class="action-cell approve-btn" data-action="approve">Approve</div>
                            <div class="action-cell reject-btn" data-action="reject">Reject</div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="confirmationModal" class="modal-overlay">
    <div class="modal-content">
        <h3 id="modalTitle">Confirm Action</h3>
        <p id="modalMessage">Are you sure you want to proceed?</p>
        <div class="modal-buttons">
            <button class="modal-button confirm" id="modalConfirmBtn">Confirm</button>
            <button class="modal-button cancel" id="modalCancelBtn">Cancel</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const roomTypeFilter = document.getElementById('roomTypeFilter');
    const bookingRows = document.querySelectorAll('.booking-row');

    const confirmationModal = document.getElementById('confirmationModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const modalConfirmBtn = document.getElementById('modalConfirmBtn');
    const modalCancelBtn = document.getElementById('modalCancelBtn');

    let currentActionRow = null; // To keep track of which row triggered the modal

    function filterBookings() {
        const selectedRoomType = roomTypeFilter.value;

        bookingRows.forEach(row => {
            const rowRoomType = row.getAttribute('data-room-type');
            const roomTypeMatch = (selectedRoomType === 'all' || rowRoomType === selectedRoomType);
            row.style.display = roomTypeMatch ? 'inline-flex' : 'none';
        });
    }

    function showConfirmationModal(title, message, onConfirm) {
        modalTitle.textContent = title;
        modalMessage.textContent = message;
        confirmationModal.classList.add('show');

        modalConfirmBtn.onclick = null;
        modalCancelBtn.onclick = null;

        modalConfirmBtn.onclick = function() {
            confirmationModal.classList.remove('show');
            onConfirm(true);
        };

        modalCancelBtn.onclick = function() {
            confirmationModal.classList.remove('show');
            onConfirm(false);
        };
    }

    document.getElementById('bookingTableBody').addEventListener('click', function(event) {
        const target = event.target;
        if (target.classList.contains('approve-btn') || target.classList.contains('reject-btn')) {
            const bookingRow = target.closest('.booking-row');
            if (!bookingRow) return;

            const action = target.getAttribute('data-action');
            currentActionRow = bookingRow;

            let title = '';
            let message = '';

            if (action === 'approve') {
                title = 'Approve Booking?';
                message = `Are you sure you want to approve this booking?`;
            } else if (action === 'reject') {
                title = 'Reject Booking?';
                message = `Are you sure you want to reject this booking? This action cannot be undone.`;
            }

            showConfirmationModal(title, message, function(confirmed) {
                if (confirmed) {
                    console.log(`${action.toUpperCase()} action confirmed for this booking.`);

                    // Placeholder for real AJAX call to backend (e.g., process_booking.php)
                    // On success:
                    if (currentActionRow) {
                        currentActionRow.remove();
                        currentActionRow = null;
                    }
                } else {
                    console.log(`Action for this booking cancelled.`);
                    currentActionRow = null;
                }
            });
        }
    });

    roomTypeFilter.addEventListener('change', filterBookings);
    filterBookings();
});
</script>

<?php include 'component/footer.php'; ?>
</body>
</html>
