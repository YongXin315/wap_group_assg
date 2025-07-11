<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once(__DIR__ . '/middleware/admin_auth.php');
require_once 'db.php';

use MongoDB\BSON\ObjectId;

// Get admin name or ID from session
$adminName = $_SESSION['admin_name'] ?? $_SESSION['admin_id'] ?? 'Admin';

// Sanitize input safely
function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Sanitize filters from GET request
$search = sanitize_input($_GET['search'] ?? '');
$roomTypeFilter = sanitize_input($_GET['room_type'] ?? '');

// Get room_ids for pending bookings
$pendingRoomIds = $db->bookings->distinct('room_id', ['status' => 'pending']);
$pendingRoomObjs = $pendingRoomIds;

// Get room data and build type & name maps
$roomMap = [];
$roomTypeMap = [];
$allRoomTypes = [];

// Build maps for room names and types
foreach ($db->rooms->find(['_id' => ['$in' => $pendingRoomObjs]]) as $room) {
    $idStr = (string)$room['_id'];
    $roomMap[$idStr] = $room['room_name'];
    $roomTypeMap[$idStr] = $room['type'];
    if (!in_array($room['type'], $allRoomTypes)) {
        $allRoomTypes[] = $room['type'];
    }
}

// Build base condition (pending only)
$conditions = [['status' => 'pending']];

// Search by student name or ID
if ($search) {
    $conditions[] = ['$or' => [
        ['full_name' => ['$regex' => $search, '$options' => 'i']],
        ['student_id' => ['$regex' => $search, '$options' => 'i']]
    ]];
}

// Room type filter (exact match from dropdown options)
if ($roomTypeFilter && $roomTypeFilter !== 'all') {
    $matchedRoomIds = [];
    foreach ($roomTypeMap as $roomId => $type) {
        if ($type === $roomTypeFilter) {
            $matchedRoomIds[] = $roomId;
        }
    }
    if (!empty($matchedRoomIds)) {
        $conditions[] = ['room_id' => ['$in' => $matchedRoomIds]];
    } else {
        $conditions[] = ['room_id' => null]; // force empty result
    }
}

// Final query
$query = ['$and' => $conditions];
// Get bookings sorted by latest first
$pendingBookings = $db->bookings->find($query, ['sort' => ['created_at' => -1]]);
?>

<?php include_once 'component/header.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .main-container {
            flex: 1 0 auto;
            background: white;
            overflow: hidden;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            display: flex;
        }
        .content-wrapper {
            width: 100%;
            justify-content: center;
            align-items: flex-start;
            display: flex;
            padding-top: 8rem;
        }
        .content-container {
            flex: 1 1 0;
            max-width: 960px;
            overflow: hidden;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            display: flex;
        }
        .filter-form {
            padding: 10px 16px;

        }
        .filter-form input, select {
            margin-right: 10px;
            min-width: 230px;
        }
        .filter-form input {
            min-width: 300px;
        }
        .div-table-content {
            border: 1px solid #E5E8EB;
            border-radius: 12px;
            overflow: hidden;
            width: 100%;
        }
        .bookings-table th, td {
            text-align: center;
            padding: 15px 5px;
            border-bottom: 1px solid #E5E8EB;
            font-size: 14px;
        }
        .action-btn {
            font-weight: bold;
            color: #944F52;
            padding: 0 10px;
        }
    </style>
</head>
<body class="font-inter">
    <div class="main-container">
        <div class="content-wrapper">
            <div class="content-container">
                <div class="text-3xl font-bold mb-6" style="padding: 0 16px;">Pending Booking Requests</div>

                <form method="GET" id="filterForm" class="gap-4 mb-6 filter-form">
                    <input type="text" name="search" class="border p-2 rounded" placeholder="Search Student Name or ID" value="<?= htmlspecialchars($search) ?>">
                    <select name="room_type" class="border p-2 rounded" onchange="document.getElementById('filterForm').submit()">
                        <option value="all">All Room Types</option>
                        <?php foreach ($allRoomTypes as $type): ?>
                            <option value="<?= htmlspecialchars($type) ?>" <?= $roomTypeFilter === $type ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="bg-red-600 text-white px-4 py-2 rounded" onclick="resetFilters()">Reset</button>
                </form>

                <div class="div-table-content">
                    <table class="bookings-table min-w-full">
                        <thead>
                            <tr>
                                <th>Booking Date</th>
                                <th>Room ID</th>
                                <th>Room Name</th>
                                <th>Time</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                                <th>Purpose</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $hasRecord = false;
                                foreach ($pendingBookings as $booking):
                                    $hasRecord = true;
                                    $roomIdStr = (string)($booking['room_id'] ?? '');
                            ?>
                            <tr id="row-<?= $booking['_id'] ?>">
                                <td><?= htmlspecialchars($booking['booking_date'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($booking['room_id']) ?></td>
                                <td><?= htmlspecialchars($roomMap[$roomIdStr] ?? '-') ?></td>
                                <td><?= htmlspecialchars(($booking['start_time'] ?? '-') . ' - ' . ($booking['end_time'] ?? '-')) ?></td>
                                <td><?= htmlspecialchars($booking['student_id']) ?></td>
                                <td><?= htmlspecialchars($booking['full_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($booking['purpose'] ?? '-') ?></td>
                                <td>
                                    <button class="action-btn" onclick="handleAction('<?= $booking['_id'] ?>', 'approved', '<?= addslashes($booking['full_name']) ?>')">Approve</button>
                                    <button class="action-btn" onclick="handleAction('<?= $booking['_id'] ?>', 'cancelled', '<?= addslashes($booking['full_name']) ?>')">Cancel</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if (!$hasRecord): ?>
                                <tr><td colspan="9" style="text-align:center; padding:1em;">No pending bookings found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handles approval or cancellation of a booking
        function handleAction(bookingId, action, studentName) {
            // Show confirmation dialog before proceeding
            const confirmation = confirm(`Are you sure you want to ${action} this request for ${studentName}?`);
            if (!confirmation) return;

            // Send a POST request to the backend PHP handler with booking ID and action
            fetch('handlers/update_booking_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: bookingId, action: action }) // Convert data to JSON
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message and remove the booking row from the table
                    alert(`${action === 'approved' ? 'Approved' : 'Cancelled'} ${studentName}'s request.`);
                    document.getElementById(`row-${bookingId}`).remove();
                } else {
                    alert('Failed to update status: ' + data.message);
                }
            })
            .catch(err => {
                console.error('Error:', err);
                alert('An error occurred while processing the request.');
            });
        }

        // Auto submit on typing (search box debounce)
        let debounceTimeout;
        document.querySelector('input[name="search"]').addEventListener('input', function () {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 1000);
        });

        // Reset button clears filters
        function resetFilters() {
            const form = document.getElementById('filterForm');
            form.querySelector('input[name="search"]').value = '';
            form.querySelector('select[name="room_type"]').value = 'all';
            form.submit();
        }
    </script>

</body>
</html>

<?php include_once 'component/footer.php'; ?>