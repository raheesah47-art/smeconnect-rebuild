<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['error' => 'No product ID provided']);
    exit;
}

$stmt = $conn->prepare('SELECT * FROM products WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    echo json_encode(['error' => 'Product not found']);
    exit;
}

echo json_encode($product);
$conn->close();