<!-- admin: manage room and update data -->

<?php
require 'admin_auth.php';
require 'vendor/autoload.php';

$uri = "mongodb+srv://rootadmin:rootadmin@cluster0.ge5ruc5.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";
// Connect MongoDB Client
$client = new MongoDB\Client($uri);

// Select database and collection
$db = $client->wap_system;
$collection = $db->rooms;

// Fetch filters from GET
$search = isset($_GET['search']) ? trim($_GET['search']) : '';      // search for room name
$filterType = isset($_GET['type']) ? $_GET['type'] : '';            // room type filter
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';      // room status filter

// Build MongoDB query
$conditions = [];

// Search by room name
if ($search !== '') {
    $conditions[] = ['room_name' => ['$regex' => $search, '$options' => 'i']];  // case-insensitive for search
}
// Filter by room type
if ($filterType !== '' && $filterType !== 'all') {
    $conditions[] = ['type' => $filterType];
}
// Filter by room status
if ($filterStatus !== '' && $filterStatus !== 'all') {
    $conditions[] = ['status' => $filterStatus];
}

// Final MongoDB query
$query = count($conditions) > 0 ? ['$and' => $conditions] : [];

// Execute query to get matching records
$rooms = $collection->find($query);

// Get all unique room types and statuses for filter dropdowns
$types = $collection->distinct("type");
$statuses = $collection->distinct("status");
sort($types);
sort($statuses);
?>

<!DOCTYPE html>
<html>
<body>
    <!-- Filters -->
    <form method="GET" action="" id="filterForm">
        <!-- Search input -->
        <input type="text" name="search" id="searchInput" placeholder="Search Room Name" value="<?= htmlspecialchars($search) ?>">

        <!-- Dropdown: Filter by room type -->
        <select name="type" onchange="document.getElementById('filterForm').submit();">
            <option value="all">All Types</option>
            <?php foreach ($types as $type): ?>
                <option value="<?= htmlspecialchars($type) ?>" <?= $filterType === $type ? 'selected' : '' ?>>
                    <?= htmlspecialchars($type) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <!-- Dropdown: Filter by room status -->
        <select name="status" onchange="document.getElementById('filterForm').submit();">
            <option value="all">All Status</option>
            <?php foreach ($statuses as $status): ?>
                <option value="<?= htmlspecialchars($status) ?>" <?= $filterStatus === $status ? 'selected' : '' ?>>
                    <?= htmlspecialchars($status) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <a href="admin_add_room.php"><button>Add Room</button></a>

    <table>
        <thead>
            <th>Room Name / Code</th>
            <th>Block / Floor</th>
            <th>Room Type</th>
            <th>Capacity</th>
            <th rowspan="2">Status</th>
        </thead>
        <tbody>
            <?php foreach ($rooms as $room): ?>
                <tr>
                    <td><?= htmlspecialchars($room['room_name']) ?></td>
                    <td><?= htmlspecialchars("Block {$room['block']}, Level {$room['floor']}") ?></td>
                    <td><?= htmlspecialchars($room['type']) ?></td>
                    <td><?= htmlspecialchars($room['max_occupancy']) ?> people</td>
                    <td>
                        <?php
                        $status = $room['status'] ?? 'Available';
                        $statusClass = ($status === 'Under Maintenance') ? 'maintenance' : 'available';
                        echo "<span class='$statusClass'>" . htmlspecialchars($status) . "</span>";
                        ?>
                    </td>
                    <td><a href="admin_edit_room.php?id=<?= urldecode($room['_id']) ?>">Edit</a></td>
                </tr>
                <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Auto-submit search after 1 second of no typing -->
    <script>
        let timeout;
        const searchInput = document.getElementById('searchInput');

        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 1000); // wait 1000ms after typing stops
        });
    </script>
</body>
</html>