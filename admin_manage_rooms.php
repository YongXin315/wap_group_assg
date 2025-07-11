<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once(__DIR__ . '/middleware/admin_auth.php');
require_once 'db.php';

// Get admin name or ID from session
$adminName = $_SESSION['admin_name'] ?? $_SESSION['admin_id'] ?? 'Admin';

// Fetch filters from GET
$search = isset($_GET['search']) ? trim(strip_tags($_GET['search'])) : '';
$filterType = isset($_GET['type']) ? trim(strip_tags($_GET['type'])) : '';
$filterStatus = isset($_GET['status']) ? trim(strip_tags($_GET['status'])) : '';

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
$rooms = $db->rooms->find($query, ['sort' => ['room_name' => 1]]);

// Get dynamic dropdown options
$types = $db->rooms->distinct("type");
$statuses = $db->rooms->distinct("status");
sort($types);
sort($statuses);
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
        .add-button {
            float: right;
            font-size: 14px;
            padding: 2px 20px;
            font-weight: 600;
            border-radius: 20px;
        }
        .filter-form {
            padding: 10px 16px;

        }
        .filter-form input, select {
            margin-right: 10px;
            min-width: 200px;
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
        .action-btn:hover {
            text-decoration: underline;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            color: white;
            text-align: center;
            min-width: 140px;
        }

        .badge-available {
            background-color: #86BB8D;
        }

        .badge-maintenance {
            background-color: #c62828;
        }
    </style>
</head>
<body class="font-inter">
    <div class="main-container">
        <div class="content-wrapper">
            <div class="content-container">
                <div class="text-3xl font-bold mb-6" style="align-self: stretch; padding: 0 16px;">Manage Rooms
                    <span class="add-button bg-red-600 text-white px-4 py-2 rounded"><a href="admin_add_room.php">Add Room</a></span>
                </div>

                <!-- Show popup after adding new room -->
                <?php if (isset($_GET['added']) && $_GET['added'] == 1): ?>
                    <script>
                        alert("New room has been successfully added.");
                    </script>
                <?php endif; ?>
                
                <!-- Show popup after saving changes -->
                <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
                    <script>alert("Room details updated successfully.");</script>
                <?php endif; ?>

                <!-- Filters -->
                <form method="GET" action="" id="filterForm" class="gap-4 mb-6 filter-form">
                    <!-- Search input -->
                    <input type="text" name="search" id="searchInput" class="border p-2 rounded" placeholder="Search Room Name" value="<?= htmlspecialchars($search) ?>">

                    <!-- Dropdown: Filter by room type -->
                    <select name="type" class="border p-2 rounded" onchange="document.getElementById('filterForm').submit();">
                        <option value="all">All Types</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?= htmlspecialchars($type) ?>" <?= $filterType === $type ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Dropdown: Filter by room status -->
                    <select name="status" class="border p-2 rounded" onchange="document.getElementById('filterForm').submit();">
                        <option value="all">All Status</option>
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= htmlspecialchars($status) ?>" <?= $filterStatus === $status ? 'selected' : '' ?>>
                                <?= htmlspecialchars($status) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="bg-red-600 text-white px-4 py-2 rounded" onclick="resetFilters()">Reset</button>
                </form>

                <div class="div-table-content">
                    <table class="bookings-table min-w-full">
                        <thead>
                            <th>Room Name / Code</th>
                            <th>Block / Floor</th>
                            <th>Room Type</th>
                            <th>Capacity</th>
                            <th>Status</th>
                            <th></th>
                        </thead>
                        <tbody>
                            <?php
                                $hasRecord = false;
                                foreach ($rooms as $room):
                                    $hasRecord = true;
                            ?>
                                <tr>
                                    <td><?= htmlspecialchars($room['room_name']) ?></td>
                                    <td style="color: #915457;"><?= htmlspecialchars("Block {$room['block']}, Level {$room['floor']}") ?></td>
                                    <td style="color: #915457;"><?= htmlspecialchars($room['type']) ?></td>
                                    <td style="color: #915457;"><?= htmlspecialchars($room['max_occupancy']) ?> people</td>
                                    <td>
                                        <?php
                                            $status = $room['status'] ?? 'Available';
                                            $class = 'badge ';
                                            if ($status === 'Under Maintenance') {
                                                $class .= 'badge-maintenance';
                                            } else {
                                                $class .= 'badge-available';
                                            }
                                            echo "<span class='$class'>" . htmlspecialchars($status) . "</span>";
                                        ?>
                                    </td>
                                    <td><a class="action-btn" href="admin_edit_room.php?id=<?= urldecode($room['_id']) ?>">Edit</a></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (!$hasRecord): ?>
                                <tr><td colspan="6" style="text-align:center; padding:1em;">No records found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

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

        // Reset filter values and submit the form
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
