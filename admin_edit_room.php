<?php
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require_once(__DIR__ . '/middleware/admin_auth.php');
require_once 'db.php';

// Check if ID is passed
if (!isset($_GET['id'])) {
    header("Location: admin_manage_rooms.php");
    exit;
}

$original_id = $_GET['id'];
$room = $db->rooms->findOne(['_id' => $original_id]);

if (!$room) {
    // If not found stop execution
    die("Room not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_id = $_POST['room_code'];
    
    // Check if room code changed and already exists
    if ($new_id !== $original_id) {
        $exists = $db->rooms->findOne(['_id' => $new_id]);
        if ($exists) {
            $error = "Room code already exists, please change a new code";
        }
    }

    if (!isset($error)) {
        $updated = [
            '_id' => $new_id,
            'room_name' => $_POST['room_name'],
            'type' => $_POST['room_type'] === 'other' ? $_POST['new_type'] : $_POST['room_type'],
            'block' => $_POST['block'],
            'floor' => $_POST['floor'],
            'amenities' => $_POST['amenities'],
            'min_occupancy' => (int)$_POST['min_capacity'],
            'max_occupancy' => (int)$_POST['max_capacity'],
            'status' => $_POST['status'] === 'other' ? $_POST['new_status'] : $_POST['status']
        ];

        if ($new_id !== $original_id) {
            $db->rooms->deleteOne(['_id' => $original_id]);
        }

        $db->rooms->replaceOne(['_id' => $new_id], $updated);
        header("Location: admin_manage_rooms.php?updated=1");
        exit;
    }
}

// Fetch types and statuses
$types = $db->rooms->distinct("type");
$statuses = array_unique(array_merge(['Available', 'Under Maintenance'], $db->rooms->distinct("status")));
sort($types);
sort($statuses);
?>

<?php include_once 'component/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Edit Room</title>
    <script>
        function toggleNewInput(selectId, newInputId) {
            const select = document.getElementById(selectId);
            const input = document.getElementById(newInputId);
            input.style.display = select.value === 'other' ? 'block' : 'none';
        }
        window.onload = function () {
            toggleNewInput('room_type', 'new_type_input');
            toggleNewInput('status', 'new_status_input');
        };
    </script>
</head>
<body>
    <?php if (isset($error)): ?>
        <div style="color: red;"><?= $error ?></div>
    <?php endif; ?>

    <form style="margin-top: 8em;" method="POST" action="">
        <label>Room Name</label>
        <input type="text" name="room_name" required value="<?= htmlspecialchars($_POST['room_name'] ?? $room['room_name']) ?>">

        <label>Room Code</label>
        <input type="text" name="room_code" required value="<?= htmlspecialchars($_POST['room_code'] ?? $room['_id']) ?>">

        <label>Room Type</label>
        <select name="room_type" id="room_type" onchange="toggleNewInput('room_type', 'new_type_input')" required>
            <option value="">Select Type</option>
            <?php foreach ($types as $type): ?>
                <option value="<?= $type ?>" <?= ($room['type'] === $type || ($_POST['room_type'] ?? '') === $type) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($type) ?>
                </option>
            <?php endforeach; ?>
            <option value="other" <?= ($_POST['room_type'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
        </select>
        <input type="text" name="new_type" id="new_type_input" placeholder="New Type"
               value="<?= htmlspecialchars($_POST['new_type'] ?? '') ?>" style="display: none;">

        <label>Block</label>
        <select name="block" required>
            <?php foreach (['A','B','C','D','E'] as $b): ?>
                <option value="<?= $b ?>" <?= ($room['block'] === $b || ($_POST['block'] ?? '') === $b) ? 'selected' : '' ?>><?= $b ?></option>
            <?php endforeach; ?>
        </select>

        <label>Floor</label>
        <input type="text" name="floor" required value="<?= htmlspecialchars($_POST['floor'] ?? $room['floor']) ?>">

        <label>Amenities</label>
        <input type="text" name="amenities" value="<?= htmlspecialchars($_POST['amenities'] ?? $room['amenities']) ?>">

        <label>Min Capacity</label>
        <input type="number" name="min_capacity" required value="<?= htmlspecialchars($_POST['min_capacity'] ?? $room['min_occupancy']) ?>">

        <label>Max Capacity</label>
        <input type="number" name="max_capacity" required value="<?= htmlspecialchars($_POST['max_capacity'] ?? $room['max_occupancy']) ?>">

        <label>Status</label>
        <select name="status" id="status" onchange="toggleNewInput('status', 'new_status_input')" required>
            <option value="">Select Status</option>
            <?php foreach ($statuses as $s): ?>
                <option value="<?= $s ?>" <?= ($room['status'] === $s || ($_POST['status'] ?? '') === $s) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s) ?>
                </option>
            <?php endforeach; ?>
            <option value="other" <?= ($_POST['status'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
        </select>
        <input type="text" name="new_status" id="new_status_input" placeholder="New Status"
               value="<?= htmlspecialchars($_POST['new_status'] ?? '') ?>" style="display: none;">

        <br><br>
        <a href="admin_manage_rooms.php"><button type="button">Cancel</button></a>
        <button type="submit">Save Changes</button>
    </form>
</body>
</html>

<?php include_once 'component/footer.php'; ?>