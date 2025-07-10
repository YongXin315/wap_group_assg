<?php
require_once(__DIR__ . '/middleware/admin_auth.php');
require_once 'db.php';

// Get admin name or ID from session
$adminName = $_SESSION['admin_name'] ?? $_SESSION['admin_id'] ?? 'Admin';

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
if ($filterType !== '' && strtolower($filterType) !== 'all') {
    $conditions[] = ['type' => $filterType];
}
// Filter by room status
if ($filterStatus !== '' && strtolower($filterStatus) !== 'all') {
    $conditions[] = ['status' => $filterStatus];
}

// Final MongoDB query
$query = count($conditions) > 0 ? ['$and' => $conditions] : [];

// Execute query to get matching records
$rooms = $db->rooms->find($query, ['sort' => ['room_name' => 1]]);

// Get dynamic dropdown options
$types = $db->rooms->distinct("type");
$statuses = $db->rooms->distinct("status");
sort($types);
sort($statuses);

include_once 'component/header.php';
?>

<!DOCTYPE html>
<html>
<body>
    <!-- Show popup after adding new room -->
    <?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
        <script>alert("New room has been successfully added.");</script>
    <?php endif; ?>
    
    <!-- Show popup after saving changes -->
    <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
        <script>alert("Room details updated successfully.");</script>
    <?php endif; ?>

    <!-- Filters -->
    <form style="margin-top: 8em;" method="GET" action="" id="filterForm">
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
        <button type="button" onclick="resetFilters()">Reset</button>
    </form>

    <a href="admin_add_room.php"><button>Add Room</button></a>

    <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; margin-top: 1em; width: 100%;">
        <thead>
            <tr>
                <th>Room Name / Code</th>
                <th>Block / Floor</th>
                <th>Room Type</th>
                <th>Capacity</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
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
                    <td><a href="admin_edit_room.php?id=<?= (string)$room['_id'] ?>">Edit</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
        let timeout;
        const searchInput = document.getElementById('searchInput');

        searchInput.addEventListener('input', function () {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 1000); // wait 1000ms after typing stops
        });

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            document.querySelector('select[name="type"]').value = 'all';
            document.querySelector('select[name="status"]').value = 'all';
            document.getElementById('filterForm').submit();
        }
    </script>
</body>
</html>

<?php include_once 'component/footer.php'; ?>
