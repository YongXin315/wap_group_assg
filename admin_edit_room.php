<!-- admin: edit room data -->

<?php
require 'admin_auth.php';
require 'vendor/autoload.php';

$uri = "mongodb+srv://rootadmin:rootadmin@cluster0.ge5ruc5.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";
// Connect MongoDB Client
$client = new MongoDB\Client($uri);

// Select database and collection
$db = $client->wap_system;
$collection = $db->rooms;

// Check if ID is passed
if (!isset($_GET['id'])) {
    header("Location: admin_manage_room.php");
    exit;
}

$original_id = $_GET['id'];
$room = $collection->findOne(['_id' => $original_id]);

if (!$room) {
    // If not found stop execution
    die("Room not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_id = $_POST['room_code'];

    // Check for duplicate if room code is changed
    if ($new_id !== $original_id) {
        $exists = $collection->findOne(['_id' => $new_id]);
        if ($exists) {
            $error = "Room code '$new_id' already exists, please change a new code";
        }
    }

    // Proceed to update if no duplication error
    if (!isset($error)) {
        // Build updated data
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

        // Delete old doc first if ID is changed
        if ($new_id !== $original_id) {
            $collection->deleteOne(['_id' => $original_id]);
        }

        // Insert or replace with new or updated doc
        $collection->replaceOne(['_id' => $new_id], $updated);
        header("Location: admin_manage_room.php?updated=1");
        exit;
    }
}

// Get all distinct types and statuses from DB for dropdown
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

        // Show new input if "Other" is selected on page load
        window.onload = function () {
            toggleNewInput('room_type', 'new_type_input');
            toggleNewInput('status', 'new_status_input');
        };
    </script>
</head>
<body>
    <!-- Display error if room code already exists -->
    <?php if (isset($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="room_name">Room Name</label>
        <input type="text" name="room_name" id="room_name" required
            value="<?= htmlspecialchars($_POST['room_name'] ?? $room['room_name']) ?>">

        <label for="room_code">Room Code/ID</label>
        <input type="text" name="room_code" id="room_code" required
            value="<?= htmlspecialchars($_POST['room_code'] ?? $room['_id']) ?>">

        <label for="room_type">Room Type</label>
        <select name="room_type" id="room_type" onchange="toggleNewInput('room_type', 'new_type_input')" required>
            <option value="">-- Select Type --</option>
            <?php foreach ($types as $type): ?>
                <option value="<?= htmlspecialchars($type) ?>" <?= ($room['type'] === $type || ($_POST['room_type'] ?? '') === $type) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($type) ?>
                </option>
            <?php endforeach; ?>
            <option value="other" <?= ($_POST['room_type'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
        </select>
        <input type="text" name="new_type" id="new_type_input"
            value="<?= htmlspecialchars($_POST['new_type'] ?? '') ?>"
            placeholder="Enter new type" style="display: none;">

        <label for="block">Block</label>
        <select name="block" id="block" required>
            <option value="">-- Select Block --</option>
            <?php foreach (['A', 'B', 'C', 'D', 'E'] as $block): ?>
                <option value="<?= $block ?>" <?= ($room['block'] == $block || ($_POST['block'] ?? '') == $block) ? 'selected' : '' ?>>
                    <?= $block ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="floor">Floor</label>
        <input type="text" name="floor" id="floor" required
            value="<?= htmlspecialchars($_POST['floor'] ?? $room['floor']) ?>">

        <label for="amenities">Amenities</label>
        <input type="text" name="amenities" id="amenities"
            value="<?= htmlspecialchars($_POST['amenities'] ?? $room['amenities']) ?>">

        <label for="min_capacity">Min Capacity</label>
        <input type="number" name="min_capacity" id="min_capacity" required
            value="<?= htmlspecialchars($_POST['min_capacity'] ?? $room['min_occupancy']) ?>">

        <label for="max_capacity">Max Capacity</label>
        <input type="number" name="max_capacity" id="max_capacity" required
            value="<?= htmlspecialchars($_POST['max_capacity'] ?? $room['max_occupancy']) ?>">

        <label for="status">Status</label>
        <select name="status" id="status" onchange="toggleNewInput('status', 'new_status_input')" required>
            <option value="">-- Select Status --</option>
            <?php foreach ($statuses as $status): ?>
                <option value="<?= htmlspecialchars($status) ?>" <?= ($room['status'] === $status || ($_POST['status'] ?? '') === $status) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($status) ?>
                </option>
            <?php endforeach; ?>
            <option value="other" <?= ($_POST['status'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
        </select>
        <input type="text" name="new_status" id="new_status_input"
            value="<?= htmlspecialchars($_POST['new_status'] ?? '') ?>"
            placeholder="Enter new status" style="display: none;">

        <a href="admin_manage_room.php"><button type="button">Cancel</button></a>
        <button type="submit">Save Changes</button>
    </form>
</body>
</html>