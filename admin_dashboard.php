<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once(__DIR__ . '/middleware/admin_auth.php');
require_once 'db.php';

// Get admin name or ID from session
$adminName = $_SESSION['admin_name'] ?? $_SESSION['admin_id'] ?? 'Admin';

// Set timezone to Malaysia
use MongoDB\BSON\UTCDateTime;
date_default_timezone_set('Asia/Kuala_Lumpur');
$today = new DateTime();
$startOfDay = new UTCDateTime((new DateTime('today'))->getTimestamp() * 1000);
$endOfDay = new UTCDateTime((new DateTime('tomorrow'))->getTimestamp() * 1000);

// Filters
$search = trim($_GET['search'] ?? '');
$roomSearch = trim($_GET['room'] ?? '');
$statusFilter = $_GET['status'] ?? '';
$dateFilter = $_GET['date'] ?? '';

// Build query
$conditions = [['status' => ['$ne' => 'Pending']]];

if ($search) {
    $conditions[] = ['$or' => [
        ['student_name' => ['$regex' => $search, '$options' => 'i']],
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
    $dateStart = new UTCDateTime((new DateTime($dateFilter))->getTimestamp() * 1000);
    $dateEnd = new UTCDateTime((new DateTime($dateFilter . ' 23:59:59'))->getTimestamp() * 1000);
    $conditions[] = ['booking_date' => ['$gte' => $dateStart, '$lte' => $dateEnd]];
}

$query = count($conditions) ? ['$and' => $conditions] : [];

$bookings = iterator_to_array($db->bookings->find($query, ['sort' => ['booking_date' => -1]]));

// Join room names
$roomMap = [];
$rooms = $db->rooms->find();
foreach ($rooms as $room) {
    $roomMap[(string)$room['_id']] = $room['room_name'];
}

// Summary cards
$totalToday = $db->bookings->countDocuments([
    'booking_date' => ['$gte' => $startOfDay, '$lt' => $endOfDay]
]);
$totalApproved = $db->bookings->countDocuments(['status' => 'Approved']);
$totalCancelled = $db->bookings->countDocuments(['status' => 'Cancelled']);
$totalCompleted = $db->bookings->countDocuments(['status' => ['$in' => ['Approved', 'Cancelled']]]);
$utilizationRate = $totalCompleted > 0 ? round(($totalApproved / $totalCompleted) * 100) : 0;

$statuses = array_filter($db->bookings->distinct("status"), fn($s) => $s !== 'Pending');
?>

<?php include_once 'component/header.php'; ?>

<!DOCTYPE html>
<html>
<body>
    <a href="view_pending_requests.php">View Pending Requests</a>
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
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?= htmlspecialchars($booking['room_id'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($roomMap[$booking['room_id']] ?? '-') ?></td>
                    <td><?= htmlspecialchars($booking['booking_date'] ?? '-') ?></td>
                    <td><?= htmlspecialchars(($booking['start_time'] ?? '-') . ' - ' . ($booking['end_time'] ?? '-')) ?></td>
                    <td><?= htmlspecialchars($booking['student_id'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($booking['student_name'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($booking['num_people'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($booking['purpose'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($booking['status'] ?? '-') ?></td>
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
    </script>
</body>
</html>

<?php include_once 'component/footer.php'; ?>