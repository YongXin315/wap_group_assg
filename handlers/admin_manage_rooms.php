<?php
require_once(__DIR__ . '/middleware/admin_auth.php');
require_once 'db.php';

// Fetch filters from GET
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filterType = isset($_GET['type']) ? $_GET['type'] : '';
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';

// Build MongoDB query
$conditions = [];

if ($search !== '') {
    $conditions[] = ['room_name' => ['$regex' => $search, '$options' => 'i']];
}
if ($filterType !== '' && strtolower($filterType) !== 'all') {
    $conditions[] = ['type' => $filterType];
}
if ($filterStatus !== '' && strtolower($filterStatus) !== 'all') {
    $conditions[] = ['status' => $filterStatus];
}

$query = count($conditions) > 0 ? ['$and' => $conditions] : [];

$rooms = $db->rooms->find($query, ['sort' => ['room_name' => 1]]);

header('Content-Type: application/json');
echo json_encode(iterator_to_array($rooms));
