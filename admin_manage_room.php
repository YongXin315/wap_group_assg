<!-- admin: manage room and update data -->

<?php
require 'admin_auth.php';
require 'vendor/autoload.php';

$uri = "mongodb+srv://rootadmin:rootadmin@cluster0.ge5ruc5.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0";
// Connect MongoDB Client
$client = new MongoDB\Client($uri);

// Select database and collection
$db = $client->taylors;
$collection = $db->rooms;

$rooms = $collection->find();
?>

<!DOCTYPE html>
<html>
<body>
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
                    <td><?= htmlspecialchars("{$room['block']}, Level {$room['floor']}") ?></td>
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
</body>
</html>