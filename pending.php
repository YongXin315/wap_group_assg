<?php
$pageTitle = "Pending Booking Requests";
$extraCss = "style.css"; // Assuming style.css contains general site styles
include 'header.php';

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?></title>
    <?php if (isset($extraCss)) { echo "<link rel='stylesheet' href='$extraCss'>"; } ?>

    <style>
        /* General layout for the main content area */
        .main-content-container {
            align-self: stretch;
            height: 753px; /* Consider making this flexible or controlled by content */
            background: white;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            display: flex;
        }

        .inner-content-wrapper {
            align-self: stretch;
            flex: 1 1 0;
            padding: 20px 160px; /* Combined padding-left/right/top/bottom */
            justify-content: center;
            align-items: flex-start;
            display: inline-flex;
        }

        .content-area {
            flex: 1 1 0;
            max-width: 960px;
            overflow: hidden;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            display: inline-flex;
        }

        /* Title styling */
        .page-title-section {
            align-self: stretch;
            padding: 16px;
            justify-content: space-between;
            align-items: flex-start;
            display: inline-flex;
            flex-wrap: wrap;
            align-content: flex-start;
        }

        .page-title {
            min-width: 288px;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            display: inline-flex;
        }

        .page-title h1 {
            color: #1C0D0D;
            font-size: 32px;
            font-family: Inter;
            font-weight: 700;
            line-height: 40px;
            word-wrap: break-word;
            margin: 0; /* Reset default h1 margin */
        }

        /* Filter Section (no search bar) */
        .filter-search-section {
            max-width: 960px;
            padding: 12px 16px;
            justify-content: flex-start; /* Align dropdown to the left */
            align-items: flex-end;
            gap: 16px; /* Still useful if you add more filters later */
            display: flex;
            flex-wrap: wrap;
            align-content: flex-end;
            width: 100%;
            box-sizing: border-box;
        }

        .filter-dropdown-container {
            flex-grow: 0; /* Don't let it grow to fill space */
            min-width: 250px;
            max-width: 448px; /* Maintain consistent max-width as before */
        }

        .room-type-select {
            width: 100%;
            height: 56px;
            background: white;
            border-radius: 12px;
            border: 1px #E5DBDB solid;
            padding-left: 17px;
            color: #876363;
            font-size: 16px;
            font-family: Inter;
            font-weight: 400;
            line-height: 24px;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url('data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%3E%3Cpath%20d%3D%22M7%2010L12%2015L17%2010H7Z%22%20fill%3D%22%23876363%22%2F%3E%3C%2Fsvg%3E');
            background-repeat: no-repeat;
            background-position: right 17px center;
            background-size: 24px;
        }

        /* Table/List Styling */
        .booking-table-container {
            align-self: stretch;
            padding: 12px 16px;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            display: flex;
        }

        .booking-table-wrapper {
            align-self: stretch;
            background: white;
            overflow: hidden; /* For rounded corners */
            border-radius: 12px;
            border: 1px #E5D1D1 solid;
            display: flex; /* Using flex for the overall table structure */
            flex-direction: column;
            width: 100%;
        }

        .table-header {
            align-self: stretch;
            background: white;
            display: inline-flex;
            justify-content: flex-start;
            align-items: flex-start;
            width: 100%;
        }

        .table-header-cell {
            padding: 12px 16px;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            display: inline-flex;
            text-align: center;
            color: #1C0D0D;
            font-size: 14px;
            font-family: Inter;
            font-weight: 500;
            line-height: 21px;
            word-wrap: break-word;
            flex-shrink: 0; /* Prevent shrinking below content */
        }

        /* Updated widths for table headers */
        .table-header-cell:nth-child(1) { width: 130px; } /* Room Name */
        .table-header-cell:nth-child(2) { width: 114px; } /* Booking Date */
        .table-header-cell:nth-child(3) { width: 100px; } /* Time */
        .table-header-cell:nth-child(4) { width: 130px; } /* Student ID */
        .table-header-cell:nth-child(5) { width: 144px; } /* Purpose */
        .table-header-cell:nth-child(6) { width: 130px; } /* Number of People */
        .table-header-cell:nth-child(7) {
            width: 194px; /* Actions (Approve/Reject) */
            color: #944F52;
            font-weight: 700;
        }

        .table-body {
            align-self: stretch;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            display: flex;
            width: 100%;
        }

        .booking-row {
            align-self: stretch;
            height: 72px;
            border-top: 1px #E5E8EB solid;
            display: inline-flex;
            justify-content: flex-start;
            align-items: flex-start;
            width: 100%;
        }

        .booking-cell {
            padding: 8px 16px;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            display: inline-flex;
            text-align: center;
            color: black;
            font-size: 14px;
            font-family: Inter;
            font-weight: 400;
            line-height: 21px;
            word-wrap: break-word;
            flex-shrink: 0; /* Prevent shrinking below content */
        }

        /* Updated widths for booking cells */
        .booking-cell:nth-child(1) { width: 130px; } /* Room Name */
        .booking-cell:nth-child(2) { width: 114px; } /* Booking Date */
        .booking-cell:nth-child(3) { width: 100px; } /* Time */
        .booking-cell:nth-child(4) { width: 130px; } /* Student ID */
        .booking-cell:nth-child(5) { width: 144px; } /* Purpose */
        .booking-cell:nth-child(6) { width: 130px; } /* Number of People */


        .action-cell {
            padding: 8px 16px;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            display: inline-flex;
            text-align: center;
            color: #944F52;
            font-size: 14px;
            font-family: Inter;
            font-weight: 700;
            line-height: 21px;
            word-wrap: break-word;
            flex-shrink: 0;
            cursor: pointer; /* Indicate clickable */
        }

        .approve-btn {
            width: 105px;
        }

        .reject-btn {
            width: 91px;
        }

        /* Utility classes for JS filtering */
        .hidden {
            display: none !important;
        }

        /* Modal Styling for custom alerts/confirms */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }

        .modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            max-width: 400px;
            width: 90%;
            transform: translateY(-20px);
            transition: transform 0.3s ease;
        }

        .modal-overlay.show .modal-content {
            transform: translateY(0);
        }

        .modal-content h3 {
            margin-top: 0;
            color: #1C0D0D;
            font-family: Inter;
            font-size: 20px;
            margin-bottom: 15px;
        }

        .modal-content p {
            color: #555;
            font-family: Inter;
            font-size: 16px;
            margin-bottom: 25px;
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .modal-button {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-family: Inter;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .modal-button.confirm {
            background-color: #944F52;
            color: white;
        }

        .modal-button.confirm:hover {
            background-color: #7a3f42;
        }

        .modal-button.cancel {
            background-color: #E5DBDB;
            color: #1C0D0D;
        }

        .modal-button.cancel:hover {
            background-color: #d1c2c2;
        }


        /* Responsive adjustments (optional, but good practice) */
        @media (max-width: 1024px) {
            .inner-content-wrapper {
                padding: 20px 80px;
            }
            .filter-search-section {
                flex-direction: column; /* Stack filters on smaller screens */
                align-items: flex-start;
                gap: 10px;
            }
            .filter-dropdown-container {
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .inner-content-wrapper {
                padding: 10px 20px;
            }
            .table-header, .booking-row {
                flex-wrap: wrap; /* Allow cells to wrap if content is too wide */
                height: auto; /* Auto height for wrapping content */
            }
            .table-header-cell, .booking-cell, .action-cell {
                width: auto; /* Let content dictate width more */
                flex-basis: auto; /* Reset flex basis */
                min-width: 80px; /* Ensure some minimum width */
            }
            /* Adjust specific column widths for smaller screens */
            .table-header-cell:nth-child(1),
            .booking-cell:nth-child(1) { flex-basis: 45%; } /* Room Name */
            .table-header-cell:nth-child(2),
            .booking-cell:nth-child(2) { flex-basis: 45%; } /* Booking Date */
            .table-header-cell:nth-child(3),
            .booking-cell:nth-child(3) { flex-basis: 45%; } /* Time */
            .table-header-cell:nth-child(4),
            .booking-cell:nth-child(4) { flex-basis: 45%; } /* Student ID */
            .table-header-cell:nth-child(5),
            .booking-cell:nth-child(5) { flex-basis: 95%; } /* Purpose */
            .table-header-cell:nth-child(6),
            .booking-cell:nth-child(6) { flex-basis: 95%; } /* Number of People */
            .table-header-cell:nth-child(7),
            .action-cell {
                flex-basis: 48%; /* Adjust action buttons */
            }
        }
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
                             data-room-type="<?php echo htmlspecialchars($booking['room_type']); ?>"
                             data-student-id="<?php echo htmlspecialchars($booking['student_id']); ?>"
                             data-student-name="">
                            <div class="booking-cell"><?php echo htmlspecialchars($booking['room_name']); ?></div>
                            <div class="booking-cell"><?php echo htmlspecialchars($booking['date']); ?></div>
                            <div class="booking-cell"><?php echo htmlspecialchars($booking['time']); ?></div>
                            <div class="booking-cell student-id-cell"><?php echo htmlspecialchars($booking['student_id']); ?></div>
                            <div class="booking-cell"><?php echo htmlspecialchars($booking['purpose']); ?></div>
                            <div class="booking-cell"><?php echo htmlspecialchars($booking['num_people']); ?></div>
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

            if (roomTypeMatch) {
                row.style.display = 'inline-flex'; // Show the row
            } else {
                row.style.display = 'none'; // Hide the row
            }
        });
    }

    // Function to show the custom confirmation modal
    function showConfirmationModal(title, message, onConfirm) {
        modalTitle.textContent = title;
        modalMessage.textContent = message;
        confirmationModal.classList.add('show');

        // Clear previous event listeners to prevent multiple calls
        modalConfirmBtn.onclick = null;
        modalCancelBtn.onclick = null;

        modalConfirmBtn.onclick = function() {
            confirmationModal.classList.remove('show');
            onConfirm(true); // Execute the provided callback with true
        };

        modalCancelBtn.onclick = function() {
            confirmationModal.classList.remove('show');
            onConfirm(false); // Execute the provided callback with false
        };
    }

    // Handle Approve/Reject actions
    document.getElementById('bookingTableBody').addEventListener('click', function(event) {
        const target = event.target;
        // Check if the clicked element is an approve or reject button
        if (target.classList.contains('approve-btn') || target.classList.contains('reject-btn')) {
            const bookingRow = target.closest('.booking-row');
            if (!bookingRow) return; // Should not happen if logic is correct

            // NOTE: The 'id' field was removed from PHP data, so bookingId will be undefined.
            // If you need a unique identifier for AJAX, you'll need to add one to the data,
            // perhaps by using an array index or generating a temporary ID for pending requests.
            // For now, I'm removing the `bookingId` variable and related message part.
            // If a real database ID is needed, it would come from a data attribute on the row.
            // For this example, we'll just log and remove the row.
            const action = target.getAttribute('data-action'); // 'approve' or 'reject'

            currentActionRow = bookingRow; // Store reference to the row

            let title = '';
            let message = '';

            if (action === 'approve') {
                title = 'Approve Booking?';
                message = `Are you sure you want to approve this booking?`; // Removed ID
            } else if (action === 'reject') {
                title = 'Reject Booking?';
                message = `Are you sure you want to reject this booking? This action cannot be undone.`; // Removed ID
            }

            showConfirmationModal(title, message, function(confirmed) {
                if (confirmed) {
                    // User confirmed the action
                    console.log(`${action.toUpperCase()} action confirmed for this booking.`);
                    // In a real application, you would send an AJAX request here
                    // You might need to pass other booking details (e.g., student ID, room, date)
                    // if you no longer have a unique 'booking_id' on the client side.
                    /*
                    fetch('process_booking.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            // Pass relevant data to identify the booking for backend processing
                            roomName: bookingRow.querySelector('.booking-cell:nth-child(1)').textContent,
                            date: bookingRow.querySelector('.booking-cell:nth-child(2)').textContent,
                            time: bookingRow.querySelector('.booking-cell:nth-child(3)').textContent,
                            studentId: bookingRow.querySelector('.booking-cell:nth-child(4)').textContent,
                            action: action
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log(`Booking ${action}d successfully.`);
                            if (currentActionRow) {
                                currentActionRow.remove(); // Remove the row from the DOM
                                currentActionRow = null; // Clear the reference
                            }
                        } else {
                            console.error('Failed to process booking:', data.message);
                            // Show an error message to the user
                        }
                    })
                    .catch(error => {
                        console.error('Error processing booking:', error);
                        // Show an error message to the user
                    });
                    */

                    // For demonstration, just remove the row immediately
                    if (currentActionRow) {
                        currentActionRow.remove();
                        currentActionRow = null;
                    }
                } else {
                    console.log(`Action for this booking cancelled.`);
                    currentActionRow = null; // Clear the reference
                }
            });
        }
    });


    // Add event listener for filter dropdown
    roomTypeFilter.addEventListener('change', filterBookings);

    // Initial filter on page load
    filterBookings();
});
</script>

<?php
require_once 'footer.php';
?>
</body>
</html>