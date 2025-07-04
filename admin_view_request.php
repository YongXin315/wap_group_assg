<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once(__DIR__ . '/middleware/admin_auth.php');
require_once 'db.php';

use MongoDB\BSON\ObjectId;

// Get admin name or ID from session
$adminName = $_SESSION['admin_name'] ?? $_SESSION['admin_id'] ?? 'Admin';

$search = $_GET['search'] ?? '';
$typeFilter = $_GET['type'] ?? '';

// Build query
$conditions = [['status' => 'pending']];

if ($search) {
    $conditions[] = ['$or' => [
        ['full_name' => ['$regex' => $search, '$options' => 'i']],
        ['student_id' => ['$regex' => $search, '$options' => 'i']]
    ]];
}

if ($typeFilter && $typeFilter !== 'all') {
    $roomIds = $db->rooms->find(['type' => $typeFilter], ['projection' => ['_id' => 1]])->toArray();
    $matchedRoomIds = array_map(fn($room) => $room['_id'], $roomIds);
    $conditions[] = ['room_id' => ['$in' => $matchedRoomIds]];
}

$query = count($conditions) > 0 ? ['$and' => $conditions] : [];

$pendingBookings = $db->bookings->find($query, ['sort' => ['created_at' => -1]]);
$roomTypes = $db->rooms->distinct('type');
?>

<?php include_once 'component/header.php'; ?>

<!DOCTYPE html>
<html>
<body>
    <form method="GET" id="filterForm">
        <input type="text" name="search" placeholder="Search Student Name or ID" value="<?= htmlspecialchars($search) ?>">
        <select name="type" onchange="document.getElementById('filterForm').submit();">
            <option value="all">All Room Types</option>
            <?php foreach ($roomTypes as $type): ?>
                <option value="<?= htmlspecialchars($type) ?>" <?= $typeFilter === $type ? 'selected' : '' ?>>
                    <?= htmlspecialchars($type) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <table>
        <thead>
            <tr>
                <th>Room ID</th>
                <th>Room Name</th>
                <th>Booking Date</th>
                <th>Time Slot</th>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Number of People</th>
                <th>Purpose</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pendingBookings as $booking):
                $roomName = '-';
                try {
                    $roomId = $booking['room_id'];
                    $room = $db->rooms->findOne(['_id' => new ObjectId($roomId)]);
                    $roomName = $room['room_name'] ?? '-';
                } catch (Exception $e) {
                    $roomName = 'Error';
                }

                $start = $booking['start_time'] ?? '-';
                $end = $booking['end_time'] ?? '-';
            ?>
            <tr id="row-<?= $booking['_id'] ?>">
                <td><?= htmlspecialchars($booking['room_id']) ?></td>
                <td><?= htmlspecialchars($roomName) ?></td>
                <td><?= htmlspecialchars($booking['booking_date'] ?? '-') ?></td>
                <td><?= htmlspecialchars("$start - $end") ?></td>
                <td><?= htmlspecialchars($booking['student_id']) ?></td>
                <td><?= htmlspecialchars($booking['full_name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($booking['num_people'] ?? '-') ?></td>
                <td><?= htmlspecialchars($booking['purpose'] ?? '-') ?></td>
                <td>
                    <button onclick="handleAction('<?= $booking['_id'] ?>', 'approve', '<?= addslashes($booking['full_name']) ?>')">Approve</button>
                    <button onclick="handleAction('<?= $booking['_id'] ?>', 'reject', '<?= addslashes($booking['full_name']) ?>')">Reject</button>
                </td>
            </tr>
            <?php endforeach; ?>
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
                    alert(`${action === 'approve' ? 'Approved' : 'Rejected'} ${studentName}'s request.`);
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
    </script>

</body>
</html>

<?php include_once 'component/footer.php'; ?>