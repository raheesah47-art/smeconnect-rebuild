<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Please log in to view your wishlist']);
    exit;
}

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare('
    SELECT p.* FROM wishlist w
    JOIN products p ON w.product_id = p.id
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
');
$stmt->bind_param('i', $userId);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($products);
$conn->close();