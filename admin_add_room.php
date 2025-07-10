<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once(__DIR__ . '/middleware/admin_auth.php');
require_once 'db.php';

$error = '';

// Get admin name or ID from session
$adminName = $_SESSION['admin_name'] ?? $_SESSION['admin_id'] ?? 'Admin';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare room data from form input
    $roomData = [
        '_id' => $_POST['room_code'],
        'room_name' => $_POST['room_name'],
        'type' => $_POST['room_type'] === 'other' ? $_POST['new_type'] : $_POST['room_type'],
        'block' => $_POST['block'],
        'floor' => $_POST['floor'],
        'amenities' => $_POST['amenities'],
        'min_occupancy' => (int)$_POST['min_capacity'],
        'max_occupancy' => (int)$_POST['max_capacity'],
        'status' => $_POST['status'] === 'other' ? $_POST['new_status'] : $_POST['status']
    ];

    // Check for duplicate _id
    $existing = $db->rooms->findOne(['_id' => $roomData['_id']]);
    if ($existing) {
        // Show error without clearing the form
        $error = "Room code already exists, please change a new code";
    } else {
        $db->rooms->insertOne($roomData);
        header("Location: admin_manage_rooms.php?added=1");
        exit;
    }
}

// Fetch existing room types for dropdowns
$types = $db->rooms->distinct("type");
?>

<?php include_once 'component/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        // Toggle visibility of custom input fields when "Other" is selected
        function toggleNewInput(selectId, newInputId) {
            const select = document.getElementById(selectId);
            const newInput = document.getElementById(newInputId);
            newInput.style.display = select.value === 'other' ? 'block' : 'none';
        }
    </script>
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
            background: #F5F5F5;
        }
        .content-wrapper {
            width: 100%;
            justify-content: center;
            align-items: flex-start;
            display: flex;
            padding-top: 8rem;

        }
        .content-container {
            background: white;
            flex: 1 1 0;
            max-width: 700px;
            overflow: hidden;
            flex-direction: column;
            justify-content: flex-start;
            align-items: flex-start;
            display: flex;
            margin-bottom: 20px;
        }
        .filter-form {
            padding: 10px 16px;
            width: 100%;
        }
        .filter-form input, select {
            margin-right: 10px;
            min-width: 100%;
            border-color: #E5D1D1;
            color: #945454;
            background: #FAF7FA;
        }
        .filter-form label {
            display: block;
            margin: 15px 0 5px 0;
            font-weight: 500;
        }
        .action-btn {
            float: right;
            margin-top: 20px;
        }
        .cancel-btn {
            font-weight: 500;
            background: #F2E8E8;
            border-radius: 10px;
            padding: 10px 16px;
            margin-right: 12px;
        }
        .add-btn {
            color: white;
            font-weight: 500;
            background: #C72426;
            border-radius: 10px;
            padding: 10px 16px;
        }
    </style>
</head>
<body class="font-inter">
    <div class="main-container">
        <div class="content-wrapper">
            <div class="content-container booking-container">
                <div class="text-3xl font-bold" style="padding-left: 16px;">Add New Room</div>

                <!-- Show error if room code already exists -->
                <?php if (isset($error)): ?>
                    <div class="error"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST" action="" class="gap-4 filter-form">
                    <!-- Room name -->
                    <label for="room_name">Room Name</label>
                    <input type="text" name="room_name" id="room_name" class="border p-2 rounded" placeholder="Enter Room Name" required value="<?= htmlspecialchars($_POST['room_name'] ?? '') ?>">
                    <br>
                    <!-- Room code or ID -->
                    <label for="room_code">Room Code or ID</label>
                    <input type="text" name="room_code" id="room_code" class="border p-2 rounded" placeholder="Enter Room Code or ID" required value="<?= htmlspecialchars($_POST['room_code'] ?? '') ?>">
                    <br>
                    <!-- Room type dropdown -->
                    <label for="room_type">Room Type</label>
                    <select name="room_type" id="room_type" class="border p-2 rounded" onchange="toggleNewInput('room_type', 'new_type_input')" required>
                        <option value="">Select Type</option>
                        <?php foreach ($types as $type): ?>
                            <option value="<?= htmlspecialchars($type) ?>"
                                <?= ($_POST['room_type'] ?? '') === $type ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type) ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="other" <?= ($_POST['room_type'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                    <!-- Custom type input if "Other" is selected -->
                    <input type="text" name="new_type" id="new_type_input" class="border p-2 rounded mt-4" placeholder="Enter Room Type"
                    value="<?= htmlspecialchars($_POST['new_type'] ?? '') ?>"
                    style="<?= ($_POST['room_type'] ?? '') === 'other' ? 'display: block;' : 'display: none;' ?>">

                    <!-- Block selection (A–E only) -->
                    <label for="block">Block</label>
                    <select name="block" id="block" class="border p-2 rounded" required>
                        <option value="">Select Block</option>
                        <?php foreach (['A', 'B', 'C', 'D', 'E'] as $block): ?>
                            <option value="<?= $block ?>" <?= ($_POST['block'] ?? '') === $block ? 'selected' : '' ?>><?= $block ?></option>
                        <?php endforeach; ?>
                    </select>

                    <!-- Floor -->
                    <label for="floor">Floor</label>
                    <input type="text" name="floor" id="floor" class="border p-2 rounded" placeholder="Enter Floor" required value="<?= htmlspecialchars($_POST['floor'] ?? '') ?>">

                    <!-- Amenities -->
                    <label for="amenities">Amenities</label>
                    <input type="text" name="amenities" id="amenities" class="border p-2 rounded" placeholder="Enter Amenities" required value="<?= htmlspecialchars($_POST['amenities'] ?? '') ?>">

                    <!-- Minimum capacity -->
                    <label for="min_capacity">Minimum Capacity</label>
                    <input type="number" name="min_capacity" id="min_capacity" class="border p-2 rounded" placeholder="Enter Minimum Capacity" required value="<?= htmlspecialchars($_POST['min_capacity'] ?? '') ?>">

                    <!-- Maximum capacity -->
                    <label for="max_capacity">Maximum Capacity</label>
                    <input type="number" name="max_capacity" id="max_capacity" class="border p-2 rounded" placeholder="Enter Maximum Capacity" required value="<?= htmlspecialchars($_POST['max_capacity'] ?? '') ?>">

                    <!-- Room status dropdown -->
                    <label for="status">Status</label>
                    <select name="status"id="status" class="border p-2 rounded" onchange="toggleNewInput('status', 'new_status_input')" required>
                        <option value="" hidden>Select Status</option>
                        <option value="Available" <?= ($_POST['status'] ?? '') === 'Available' ? 'selected' : '' ?>>Available</option>
                        <option value="Under Maintenance" <?= ($_POST['status'] ?? '') === 'Under Maintenance' ? 'selected' : '' ?>>Under Maintenance</option>
                    </select>
                    <div class="action-btn">
                        <a href="admin_manage_rooms.php"><button type="button" class="cancel-btn">Cancel</button></a>
                        <button type="submit" class="add-btn">Add Room</button>
                    </div>
                    
                </form>

            </div>
        </div>
    </div>
</body>
</html>

<?php include_once 'component/footer.php'; ?>