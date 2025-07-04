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

// Fetch existing room types and statuses for dropdowns
$types = $db->rooms->distinct("type");
$statuses = array_unique(array_merge(['Available', 'Under Maintenance'], $db->rooms->distinct("status")));
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
</head>
<body>
    <!-- Show error if room code already exists -->
    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form style="margin-top: 8em;" method="POST" action="">
        <!-- Room name -->
        <label for="room_name">Room Name</label>
        <input type="text" name="room_name" id="room_name" required value="<?= htmlspecialchars($_POST['room_name'] ?? '') ?>">

        <!-- Room code or ID -->
        <label for="room_code">Room Code or ID</label>
        <input type="text" name="room_code" id="room_code" required value="<?= htmlspecialchars($_POST['room_code'] ?? '') ?>">

        <!-- Room type dropdown -->
        <label for="room_type">Room Type</label>
        <select name="room_type" id="room_type" onchange="toggleNewInput('room_type', 'new_type_input')" required>
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
        <input type="text" name="new_type" id="new_type_input" 
        value="<?= htmlspecialchars($_POST['new_type'] ?? '') ?>"
        style="<?= ($_POST['room_type'] ?? '') === 'other' ? 'display: block;' : 'display: none;' ?>">

        <!-- Block selection (A–E only) -->
        <label for="block">Block</label>
        <select name="block" id="block" required>
            <option value="">Select Block</option>
            <?php foreach (['A', 'B', 'C', 'D', 'E'] as $block): ?>
                <option value="<?= $block ?>" <?= ($_POST['block'] ?? '') === $block ? 'selected' : '' ?>><?= $block ?></option>
            <?php endforeach; ?>
        </select>

        <!-- Floor -->
        <label for="floor">Floor</label>
        <input type="text" name="floor" id="floor" required value="<?= htmlspecialchars($_POST['floor'] ?? '') ?>">

        <!-- Amenities -->
        <label for="amenities">Amenities</label>
        <input type="text" name="amenities" id="amenities" value="<?= htmlspecialchars($_POST['amenities'] ?? '') ?>">

        <!-- Minimum capacity -->
        <label for="min_capacity">Minimum Capacity</label>
        <input type="number" name="min_capacity" id="min_capacity" required value="<?= htmlspecialchars($_POST['min_capacity'] ?? '') ?>">

        <!-- Maximum capacity -->
        <label for="max_capacity">Maximum Capacity</label>
        <input type="number" name="max_capacity" id="max_capacity" required value="<?= htmlspecialchars($_POST['max_capacity'] ?? '') ?>">

        <!-- Room status dropdown -->
        <label for="status">Status</label>
        <select name="status" id="status" onchange="toggleNewInput('status', 'new_status_input')" required>
            <option value="">Select Status</option>
            <?php foreach ($statuses as $status): ?>
                <option value="<?= htmlspecialchars($status) ?>" 
                    <?= ($_POST['status'] ?? '') === $status ? 'selected' : '' ?>>
                    <?= htmlspecialchars($status) ?>
                </option>
            <?php endforeach; ?>
            <option value="other" <?= ($_POST['status'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
        </select>
        <!-- Custom status input if "Other" is selected -->
        <input type="text" name="new_status" id="new_status_input" 
        value="<?= htmlspecialchars($_POST['new_status'] ?? '') ?>"
        style="<?= ($_POST['status'] ?? '') === 'other' ? 'display: block;' : 'display: none;' ?>">

        <a href="admin_manage_rooms.php"><button type="button">Cancel</button></a>
        <button type="submit">Add Room</button>
    </form>
</body>
</html>

<?php include_once 'component/footer.php'; ?>