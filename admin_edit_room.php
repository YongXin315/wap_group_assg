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

$previousStatus = $room['status'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_single']) && $_POST['cancel_single'] === 'yes') {
    $bookingId = $_POST['booking_id'] ?? '';
    if ($bookingId) {
        $db->bookings->updateOne(
            ['_id' => new MongoDB\BSON\ObjectId($bookingId)],
            ['$set' => ['status' => 'cancelled']]
        );
    }
    $_POST['booking_cancelled'] = '1';
}

// Handle form submission
$affectedBookings = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['cancel_warning_box']) && $_POST['cancel_warning_box'] === 'yes') {
        $affectedBookings = []; // hide warning
        // Just skip further processing so form re-renders with $_POST data
    } else {
        $new_id = $_POST['room_code'];
        $newStatus = $_POST['status'] === 'other' ? $_POST['new_status'] : $_POST['status'];

    

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
                'status' => $newStatus
            ];

            if ($newStatus === 'Under Maintenance') {
                $affectedBookings = iterator_to_array($db->bookings->find([
                    'room_id' => $new_id,
                    'status' => ['$in' => ['approved', 'pending']],
                    'booking_date' => ['$gte' => date('Y-m-d')]
                ]));
            }

            if (isset($_POST['proceed_without_cancel']) && $_POST['proceed_without_cancel'] === 'yes') {
                if ($new_id !== $original_id) {
                    $db->rooms->deleteOne(['_id' => $original_id]);
                }
                $db->rooms->replaceOne(['_id' => $new_id], $updated);
                header("Location: admin_manage_rooms.php?updated=1");
                exit;
            }

            if (empty($affectedBookings)) {
                if ($new_id !== $original_id) {
                    $db->rooms->deleteOne(['_id' => $original_id]);
                }
                $db->rooms->replaceOne(['_id' => $new_id], $updated);
                header("Location: admin_manage_rooms.php?updated=1");
                exit;
            }

            if (!isset($_POST['booking_cancelled'])) {
                if ($newStatus === 'Under Maintenance') {
                    $affectedBookings = iterator_to_array($db->bookings->find([
                        'room_id' => $new_id,
                        'status' => ['$in' => ['approved', 'pending']],
                        'booking_date' => ['$gte' => date('Y-m-d')]
                    ]));
                }
            }

        }
    }
}

// Fetch types and statuses
$types = $db->rooms->distinct("type");
sort($types);
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
        .warning-box-container {
            padding: 10px 16px;
            width: 100%;
        }
        .warning-box {
            margin-top: 10px;
            padding: 10px 16px;
            width: 100%;
            border: 1px solid #945454; 
            border-radius: 16px;
            background: #ffecec;
        }
        .div-table {
            width: 100%;
            padding-top: 18px;
        }
        .div-table-content {
            border: 1px solid #E5E8EB;
            border-radius: 12px;
            overflow: hidden;
        }
        .bookings-table th, td {
            background: white;
            text-align: center;
            padding: 10px 5px;
            border-bottom: 1px solid #E5E8EB;
            font-size: 13px;
        }
        .warning-ok-button {
            color: white;
            font-weight: 500;
            background: #C72426;
            border-radius: 10px;
            padding: 7px 16px;
        }
        .warning-cancel-button {
            font-weight: 500;
            background: white;
            border-radius: 10px;
            padding: 7px 16px;
            margin-right: 12px;
        }
        .warning-box-form {
            justify-content: center;
            display: flex;
            width: 100%;
        }
        .warning-cancel-booking {
            font-weight: bold;
            color: #944F52;
        }
        .warning-cancel-booking:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="font-inter">
    <div class="main-container">
        <div class="content-wrapper">
            <div class="content-container booking-container">
                <div class="text-3xl font-bold" style="padding-left: 16px;">Edit Room Details</div>

                <?php if (isset($error)): ?>
                    <div style="color: red;"><?= $error ?></div>
                <?php endif; ?>

                <?php if (!empty($affectedBookings)): ?>
                    <div class="warning-box-container">
                        <div class="warning-box">
                            <h3 style="color:rgb(208, 83, 83); font-weight: bold; text-align: center;">Warning: This room has active or pending bookings that may be affected.</h3>
                            <div class="div-table">
                                <div class="div-table-content">
                                    <table class="bookings-table min-w-full">
                                        <thead>
                                            <tr><th>Booking Date</th><th>Time Slot</th><th>Student ID</th><th>Name</th><th>Purpose</th><th>Status</th><th>Action</th></tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($affectedBookings as $b): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($b['booking_date']) ?></td>
                                                    <td><?= htmlspecialchars(($b['start_time'] ?? '-') . ' - ' . ($b['end_time'] ?? '-')) ?></td>
                                                    <td><?= htmlspecialchars($b['student_id']) ?></td>
                                                    <td><?= htmlspecialchars($b['full_name']) ?></td>
                                                    <td><?= htmlspecialchars($b['purpose']) ?></td>
                                                    <td><?= htmlspecialchars($b['status']) ?></td>
                                                    <td>
                                                        <?php if (in_array($b['status'], ['approved', 'pending'])): ?>
                                                            <form method="POST" style="display:inline;">
                                                                <input type="hidden" name="cancel_single" value="yes">
                                                                <input type="hidden" name="booking_id" value="<?= $b['_id'] ?>">
                                                                <!-- Preserve the form data -->
                                                                <?php foreach ($_POST as $key => $value): ?>
                                                                    <?php if (!in_array($key, ['cancel_single', 'booking_id'])): ?>
                                                                        <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                                                                    <?php endif; ?>
                                                                <?php endforeach; ?>
                                                                <button type="submit" class="warning-cancel-booking" onclick="return confirm('Cancel this booking?')">Cancel</button>
                                                            </form>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <br>
                            <form method="POST" class="warning-box-form">
                                <?php foreach ($_POST as $key => $value): ?>
                                    <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                                <?php endforeach; ?>
                                <input type="hidden" name="proceed_without_cancel" value="yes">
                                <button type="submit" class="warning-ok-button" style="margin-right: 10px;">OK</button>
                                <button type="submit" class="warning-cancel-button" name="cancel_warning_box" value="yes">Cancel</button>
                            </form>
                        </div>
                    </div>
                    
                <?php endif; ?>

                <form method="POST" action="" class="gap-4 filter-form">
                    <label>Room Name</label>
                    <input type="text" name="room_name" required class="border p-2 rounded" value="<?= htmlspecialchars($_POST['room_name'] ?? $room['room_name']) ?>">

                    <label>Room Code</label>
                    <input type="text" name="room_code" required class="border p-2 rounded" value="<?= htmlspecialchars($_POST['room_code'] ?? $room['_id']) ?>">

                    <label>Room Type</label>
                    <select name="room_type" id="room_type" class="border p-2 rounded" onchange="toggleNewInput('room_type', 'new_type_input')" required>
                        <?php foreach ($types as $type): ?>
                            <option value="<?= $type ?>" <?= ($room['type'] === $type || ($_POST['room_type'] ?? '') === $type) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type) ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="other" <?= ($_POST['room_type'] ?? '') === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                    <input type="text" name="new_type" id="new_type_input" placeholder="New Type" class="border p-2 rounded mt-4"
                        value="<?= htmlspecialchars($_POST['new_type'] ?? '') ?>" style="display: none;">

                    <label>Block</label>
                    <select name="block" required class="border p-2 rounded">
                        <?php foreach (['A','B','C','D','E'] as $b): ?>
                            <option value="<?= $b ?>" <?= ($room['block'] === $b || ($_POST['block'] ?? '') === $b) ? 'selected' : '' ?>><?= $b ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label>Floor</label>
                    <input type="text" name="floor" required class="border p-2 rounded" value="<?= htmlspecialchars($_POST['floor'] ?? $room['floor']) ?>">

                    <label>Amenities</label>
                    <input type="text" name="amenities" required class="border p-2 rounded" value="<?= htmlspecialchars($_POST['amenities'] ?? $room['amenities']) ?>">

                    <label>Min Capacity</label>
                    <input type="number" name="min_capacity" required class="border p-2 rounded" value="<?= htmlspecialchars($_POST['min_capacity'] ?? $room['min_occupancy']) ?>">

                    <label>Max Capacity</label>
                    <input type="number" name="max_capacity" required class="border p-2 rounded" value="<?= htmlspecialchars($_POST['max_capacity'] ?? $room['max_occupancy']) ?>">

                    <label>Status</label>
                    <select name="status" id="status" required class="border p-2 rounded">
                        <?php
                        $selectedStatus = $_POST['status'] ?? $room['status'];
                        ?>
                        <option value="Available" <?= ($selectedStatus === 'Available') ? 'selected' : '' ?>>Available</option>
                        <option value="Under Maintenance" <?= ($selectedStatus === 'Under Maintenance') ? 'selected' : '' ?>>Under Maintenance</option>
                    </select>

                    <div class="action-btn">
                        <a href="admin_manage_rooms.php"><button type="button" class="cancel-btn">Cancel</button></a>
                        <button type="submit" class="add-btn">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php include_once 'component/footer.php'; ?>