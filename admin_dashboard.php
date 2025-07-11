<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once(__DIR__ . '/middleware/admin_auth.php');
require_once 'db.php';

// Get admin name or ID from session
$adminName = $_SESSION['admin_name'] ?? $_SESSION['admin_id'] ?? 'Admin';

use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;

// Sanitize and validate GET inputs
function sanitize_input($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Set timezone to Malaysia
date_default_timezone_set('Asia/Kuala_Lumpur');
$today = new DateTime();
$startOfDay = new UTCDateTime((new DateTime('today'))->getTimestamp() * 1000);
$endOfDay = new UTCDateTime((new DateTime('tomorrow'))->getTimestamp() * 1000);

// Sanitize filter inputs
$search = sanitize_input($_GET['search'] ?? '');
$roomSearch = sanitize_input($_GET['room'] ?? '');
$statusFilter = sanitize_input($_GET['status'] ?? '');
$dateFilter = sanitize_input($_GET['date'] ?? '');
$sortBy = sanitize_input($_GET['sort_by'] ?? 'created_at');

// Validate allowed sort field to prevent injection
$allowedSortFields = ['created_at', 'booking_date'];
$sortField = in_array($sortBy, $allowedSortFields) ? $sortBy : 'created_at';

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
        .pending-button {
            float: right;
            font-size: 14px;
            padding: 2px 20px;
            font-weight: 600;
            border-radius: 20px;
        }
        .filter-form input {
            margin: 0 10px 10px 0;
            min-width: 307px;
        }
        .filter-form select {
            margin: 0 10px 10px 0;
            min-width: 200px;
        }
        .div-table {
            padding: 0 50px;
            width: 100%;
        }
        .div-table-content {
            border: 1px solid #E5E8EB;
            border-radius: 12px;
            overflow: hidden;
        }
        .bookings-table th, td {
            text-align: center;
            padding: 15px 5px;
            border-bottom: 1px solid #E5E8EB;
            font-size: 14px;
        }
        .bookings-table td {
            color: #915457;
        }
        .cancel-button {
            background: none;
            border: none;
            color: #876363;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: color 0.3s ease;
            text-decoration: underline;
        }

        .cancel-button:hover {
            color: #C3272B;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            color: white;
            text-align: center;
            min-width: 80px;
        }

        .badge-approved {
            background-color: #86BB8D;
        }

        .badge-cancelled {
            background-color: #c62828;
        }
    </style>
</head>
<body class="font-inter">
    <div class="main-container">
        <div class="content-wrapper">
            <div class="content-container">
                <div class="text-3xl font-bold mb-6" style="align-self: stretch; padding: 0 16px;">Booking Management Dashboard
                    <span class="pending-button bg-red-600 text-white px-4 py-2 rounded"><a href="admin_view_request.php">View Pending Requests</a></span>
                </div>

                <div class="stats-section">
                    <div class="stat-card total">
                        <div class="stat-label">Total Bookings Today</div>
                        <div class="stat-value"><?php echo $totalToday; ?></div>
                    </div>
                    <div class="stat-card available">
                        <div class="stat-label">Approved Bookings</div>
                        <div class="stat-value"><?php echo $totalApproved; ?></div>
                    </div>
                    <div class="stat-card occupied">
                        <div class="stat-label">Cancelled Bookings</div>
                        <div class="stat-value"><?php echo $totalCancelled; ?></div>
                    </div>
                    <div class="stat-card total">
                        <div class="stat-label">Utilization Rate</div>
                        <div class="stat-value"><?php echo $utilizationRate; ?>%</div>
                    </div>
                </div>

                <div class="section-title">
                    <div class="section-title-text">Filters</div>
                </div>

                <form method="GET" id="filterForm" class="gap-4 mb-6 filter-form" style="padding: 0 16px;">
                    <input type="date" name="date" class="border p-2 rounded" style="min-width: 200px;" value="<?= htmlspecialchars($dateFilter) ?>" onchange="submitForm()">
                    <select name="status" class="border p-2 rounded" onchange="submitForm()">
                        <option value="all">All Status</option>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status ?>" <?= $statusFilter === $status ? 'selected' : '' ?>><?= $status ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="sort_by" class="border p-2 rounded" onchange="submitForm()">
                        <option value="created_at" <?= ($_GET['sort_by'] ?? '') === 'created_at' ? 'selected' : '' ?>>Created Time</option>
                        <option value="booking_date" <?= ($_GET['sort_by'] ?? '') === 'booking_date' ? 'selected' : '' ?>>Booking Date</option>
                    </select>
                    <br>
                    <input type="text" name="search" class="border p-2 rounded" placeholder="Search by Student Name or ID" value="<?= htmlspecialchars($search) ?>" oninput="delayedSubmit()">
                    <input type="text" name="room" class="border p-2 rounded" placeholder="Search Room Name" value="<?= htmlspecialchars($roomSearch) ?>" oninput="delayedSubmit()">
                    <button type="button" class="bg-red-600 text-white px-4 py-2 rounded" onclick="resetFilters()">Reset</button>
                </form>
            </div>
        </div>

        <div class="div-table">
            <div class="div-table-content">
                <table class="bookings-table min-w-full">
                    <thead>
                        <tr>
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
                    <tbody>
                        <?php
                            $hasRecord = false;
                            foreach ($bookings as $booking):
                                $hasRecord = true;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($booking['booking_date'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($booking['room_id'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($roomMap[$booking['room_id']] ?? '-') ?></td>
                                <td><?= htmlspecialchars(($booking['start_time'] ?? '-') . ' - ' . ($booking['end_time'] ?? '-')) ?></td>
                                <td><?= htmlspecialchars($booking['student_id'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($booking['full_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($booking['purpose'] ?? '-') ?></td>
                                <td>
                                    <?php
                                        $status = $booking['status'] ?? 'unknown';
                                        $class = 'badge ';
                                        if ($status === 'approved') $class .= 'badge-approved';
                                        elseif ($status === 'cancelled') $class .= 'badge-cancelled';
                                    ?>
                                    <span class="<?= $class ?>"><?= ucfirst($status) ?></span>
                                </td>
                                <td>
                                    <?php if ($booking['status'] === 'approved'): ?>
                                        <button class="cancel-button" onclick="cancelBooking('<?= $booking['_id'] ?>', '<?= addslashes($booking['full_name']) ?>')">Cancel</button>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$hasRecord): ?>
                            <tr><td colspan="9" style="text-align:center; padding:1em;">No records found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

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
        // Show a confirmation dialog before cancelling
        const confirmCancel = confirm(`Are you sure you want to cancel the approved booking for ${studentName}?`);
        if (!confirmCancel) return;

        // Send a POST request to the backend handler to update the booking status
        fetch('handlers/update_booking_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id: bookingId, action: 'cancelled' })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // If cancellation was successful, show confirmation and refresh page
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