<?php

if (!extension_loaded('mongodb')) {
    die('MongoDB extension is not loaded!');
}

// Check if the class exists
if (!class_exists('MongoDB\BSON\UTCDateTime')) {
    die('UTCDateTime class not found!');
}

echo "MongoDB extension loaded successfully!<br>";

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once(__DIR__ . '/middleware/admin_auth.php');
require_once 'db.php';

// Get admin name or ID from session
$adminName = $_SESSION['admin_name'] ?? $_SESSION['admin_id'] ?? 'Admin';

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

// Set timezone to Malaysia
date_default_timezone_set('Asia/Kuala_Lumpur');
$today = new DateTime();
$startOfDay = new UTCDateTime((new DateTime('today'))->getTimestamp() * 1000);
$endOfDay = new UTCDateTime((new DateTime('tomorrow'))->getTimestamp() * 1000);

// Filters
$search = trim($_GET['search'] ?? '');
$roomSearch = trim($_GET['room'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$dateFilter = $_GET['date'] ?? '';
$sortBy = $_GET['sort_by'] ?? 'created_at';
$sortField = $sortBy === 'booking_date' ? 'booking_date' : 'created_at';

// Build query
$conditions = [];

if ($search) {
    $conditions[] = ['$or' => [
        ['full_name' => ['$regex' => $search, '$options' => 'i']],
        ['student_id' => ['$regex' => $search, '$options' => 'i']],
    ]];
}
if ($roomSearch) {
    // Will match later with joined data
    $roomMatches = $db->rooms->find([
        'room_name' => ['$regex' => $roomSearch, '$options' => 'i']
    ]);
    $roomIds = array_map(fn($room) => $room['_id'], iterator_to_array($roomMatches));
    $conditions[] = ['room_id' => ['$in' => $roomIds]];
}
if ($statusFilter && $statusFilter !== 'all') {
    $conditions[] = ['status' => $statusFilter];
}
if ($dateFilter) {
    $conditions[] = ['booking_date' => $dateFilter];
}

// Always exclude 'Pending'
$conditions[] = ['status' => ['$ne' => 'pending']];

$query = count($conditions) ? ['$and' => $conditions] : [];

$bookings = iterator_to_array($db->bookings->find($query, ['sort' => [$sortField => -1]]));

// Join room names
$roomMap = [];
foreach ($db->rooms->find() as $room) {
    $roomMap[(string)$room['_id']] = $room['room_name'];
}

// Summary cards
$totalToday = $db->bookings->countDocuments([
    'created_at' => ['$gte' => $startOfDay, '$lt' => $endOfDay]
]);
$totalApproved = $db->bookings->countDocuments(['status' => 'approved']);
$totalCancelled = $db->bookings->countDocuments(['status' => 'cancelled']);
$totalCompleted = $db->bookings->countDocuments(['status' => ['$in' => ['approved', 'cancelled']]]);
$utilizationRate = $totalCompleted > 0 ? round(($totalApproved / $totalCompleted) * 100) : 0;

$statuses = array_filter($db->bookings->distinct("status"), fn($s) => $s !== 'pending');

?>

<?php include_once 'component/header.php'; ?>

<!DOCTYPE html>
<html>
<body>
    <br><br><br><br>
    <a href="admin_view_request.php">View Pending Requests</a>
    <div>
        <div><h3>Total Bookings Today</h3><p><?= $totalToday ?></p></div>
        <div><h3>Approved Bookings</h3><p><?= $totalApproved ?></p></div>
        <div><h3>Cancelled Bookings</h3><p><?= $totalCancelled ?></p></div>
        <div><h3>Utilization Rate</h3><p><?= $utilizationRate ?>%</p></div>
    </div>

    <form method="GET" id="filterForm">
        <input type="date" name="date" value="<?= htmlspecialchars($dateFilter) ?>" onchange="submitForm()">
        <input type="text" name="room" placeholder="Search Room Name" value="<?= htmlspecialchars($roomSearch) ?>" oninput="delayedSubmit()">
        <select name="status" onchange="submitForm()">
            <option value="all">All Status</option>
            <?php foreach ($statuses as $status): ?>
                <option value="<?= $status ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= $status ?></option>
            <?php endforeach; ?>
        </select>
        <input type="text" name="search" placeholder="Search by Student Name or ID" value="<?= htmlspecialchars($search) ?>" oninput="delayedSubmit()">
        <select name="sort_by" onchange="submitForm()">
            <option value="created_at" <?= ($_GET['sort_by'] ?? '') === 'created_at' ? 'selected' : '' ?>>Created Time</option>
            <option value="booking_date" <?= ($_GET['sort_by'] ?? '') === 'booking_date' ? 'selected' : '' ?>>Booking Date</option>
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
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?= htmlspecialchars($booking['booking_date'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($booking['room_id'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($roomMap[$booking['room_id']] ?? '-') ?></td>
                    <td><?= htmlspecialchars(($booking['start_time'] ?? '-') . ' - ' . ($booking['end_time'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars($booking['student_id'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($booking['full_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($booking['purpose'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($booking['status'] ?? '-') ?></td>
                    <td>
                        <?php if ($booking['status'] === 'approved'): ?>
                            <button onclick="cancelBooking('<?= $booking['_id'] ?>', '<?= addslashes($booking['full_name']) ?>')">Cancel</button>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
    function submitForm() {
        document.getElementById('filterForm').submit();
    }

    let timeout;
    function delayedSubmit() {
        clearTimeout(timeout);
        timeout = setTimeout(submitForm, 800);
    }
    function resetFilters() {
        const form = document.getElementById('filterForm');
        form.querySelector('input[name="date"]').value = '';
        form.querySelector('input[name="room"]').value = '';
        form.querySelector('input[name="search"]').value = '';
        form.querySelector('select[name="status"]').value = 'all';
        form.querySelector('select[name="sort_by"]').value = 'created_at';
        form.submit();
    }
    function cancelBooking(bookingId, studentName) {
        const confirmCancel = confirm(`Are you sure you want to cancel the approved booking for ${studentName}?`);
        if (!confirmCancel) return;

        fetch('handlers/update_booking_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: bookingId, action: 'cancelled' })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(`Booking for ${studentName} has been cancelled.`);
                location.reload(); // Refresh to reflect changes
            } else {
                alert('Failed to cancel booking: ' + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred while cancelling the booking.');
        });
    }
    </script>
</body>
</html>

<?php include_once 'component/footer.php'; ?>