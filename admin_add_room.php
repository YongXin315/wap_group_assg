<?php
require 'admin_auth.php';
require 'vendor/autoload.php';

$uri = "mongodb+srv://rootadmin:rootadmin@cluster0.ge5ruc5.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";
// Connect MongoDB Client
$client = new MongoDB\Client($uri);

// Select database and collection
$db = $client->wap_system;
$collection = $db->rooms;

// handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // check for duplicate _id
    $existing = $collection->findOne(['_id' => $roomData['_id']]);
    if ($existing) {
        $error = "Room code already exists, please change a new code";
    } else {
        $collection->insertOne($roomData);
        header("Location: admin_manage_room.php?added=1");
        exit;
    }
}

// fetch distinct types and statuses
$types = $collection->distinct("type");
$statuses = array_unique(array_merge(['Available', 'Under Maintenance'], $collection->distinct("status")));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        function toggleNewInput(selectId, newInputId) {
            const select = document.getElementById(selectId);
            const newInput = document.getElementById(newInputId);
            newInput.style.display = select.value === 'other' ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="room_name">Room Name</label>
        <input type="text" name="room_name" id="room_name" required value="<?= htmlspecialchars($_POST['room_name'] ?? '') ?>">

        <label for="room_code">Room Code or ID</label>
        <input type="text" name="room_code" id="room_code" required value="<?= htmlspecialchars($_POST['room_code'] ?? '') ?>">

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
        <input type="text" name="new_type" id="new_type_input" 
        value="<?= htmlspecialchars($_POST['new_type'] ?? '') ?>"
        style="<?= ($_POST['room_type'] ?? '') === 'other' ? 'display: block;' : 'display: none;' ?>">

        <label for="block">Block</label>
        <select name="block" id="block" required>
            <option value="">Select Block</option>
            <?php foreach (['A', 'B', 'C', 'D', 'E'] as $block): ?>
                <option value="<?= $block ?>" <?= ($_POST['block'] ?? '') === $block ? 'selected' : '' ?>><?= $block ?></option>
            <?php endforeach; ?>
        </select>

        <label for="floor">Floor</label>
        <input type="text" name="floor" id="floor" required value="<?= htmlspecialchars($_POST['floor'] ?? '') ?>">

        <label for="amenities">Amenities</label>
        <input type="text" name="amenities" id="amenities" value="<?= htmlspecialchars($_POST['amenities'] ?? '') ?>">

        <label for="min_capacity">Minimum Capacity</label>
        <input type="number" name="min_capacity" id="min_capacity" required value="<?= htmlspecialchars($_POST['min_capacity'] ?? '') ?>">

        <label for="max_capacity">Maximum Capacity</label>
        <input type="number" name="max_capacity" id="max_capacity" required value="<?= htmlspecialchars($_POST['max_capacity'] ?? '') ?>">

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
        <input type="text" name="new_status" id="new_status_input" 
        value="<?= htmlspecialchars($_POST['new_status'] ?? '') ?>"
    style="<?= ($_POST['status'] ?? '') === 'other' ? 'display: block;' : 'display: none;' ?>">

        <a href="admin_manage_room.php"><button type="button">Cancel</button></a>
        <button type="submit">Add Room</button>
    </form>
</body>
</html>