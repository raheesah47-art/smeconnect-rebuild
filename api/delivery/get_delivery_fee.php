<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

$district = $_GET['district'] ?? '';

$stmt = $conn->prepare('SELECT fee FROM delivery_fees WHERE district = ?');
$stmt->bind_param('s', $district);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();

if (!$result) {
    echo json_encode(['error' => 'District not found', 'fee' => 0]);
    exit;
}

echo json_encode(['district' => $district, 'fee' => (float)$result['fee']]);
$conn->close();
