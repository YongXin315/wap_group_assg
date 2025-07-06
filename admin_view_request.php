<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once(__DIR__ . '/middleware/admin_auth.php');
require_once 'db.php';

use MongoDB\BSON\ObjectId;

// Get admin name or ID from session
$adminName = $_SESSION['admin_name'] ?? $_SESSION['admin_id'] ?? 'Admin';

$search = $_GET['search'] ?? '';
$roomTypeFilter = $_GET['room_type'] ?? '';

// Get room_ids for pending bookings
$pendingRoomIds = $db->bookings->distinct('room_id', ['status' => 'pending']);
$pendingRoomObjs = $pendingRoomIds;

// Get room data and build type & name maps
$roomMap = [];
$roomTypeMap = [];
$allRoomTypes = [];

foreach ($db->rooms->find(['_id' => ['$in' => $pendingRoomObjs]]) as $room) {
    $idStr = (string)$room['_id'];
    $roomMap[$idStr] = $room['room_name'];
    $roomTypeMap[$idStr] = $room['type'];
    if (!in_array($room['type'], $allRoomTypes)) {
        $allRoomTypes[] = $room['type'];
    }
}

// Build query
$conditions = [['status' => 'pending']];

// Search by student name or ID
if ($search) {
    $conditions[] = ['$or' => [
        ['full_name' => ['$regex' => $search, '$options' => 'i']],
        ['student_id' => ['$regex' => $search, '$options' => 'i']]
    ]];
}

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
<body>
    <br><br><br><br>
    <form method="GET" id="filterForm">
        <input type="text" name="search" placeholder="Search Student Name or ID" value="<?= htmlspecialchars($search) ?>">
        <select name="room_type" onchange="document.getElementById('filterForm').submit()">
            <option value="all">All Room Types</option>
            <?php foreach ($allRoomTypes as $type): ?>
                <option value="<?= htmlspecialchars($type) ?>" <?= $roomTypeFilter === $type ? 'selected' : '' ?>>
                    <?= htmlspecialchars($type) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="button" onclick="resetFilters()">Reset</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Booking Date</th>
                <th>Room ID</th>
                <th>Room Name</th>
                <th>Time Slot</th>
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
                    <button onclick="handleAction('<?= $booking['_id'] ?>', 'approved', '<?= addslashes($booking['full_name']) ?>')">Approve</button>
                    <button onclick="handleAction('<?= $booking['_id'] ?>', 'cancelled', '<?= addslashes($booking['full_name']) ?>')">Cancel</button>
                </td>
            </tr>
            <?php endforeach; ?>

            <?php if (!$hasRecord): ?>
                <tr><td colspan="9" style="text-align:center; padding:1em;">No pending bookings found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <script>
        function handleAction(bookingId, action, studentName) {
            const confirmation = confirm(`Are you sure you want to ${action} this request for ${studentName}?`);
            if (!confirmation) return;

            fetch('handlers/update_booking_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: bookingId, action: action })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
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