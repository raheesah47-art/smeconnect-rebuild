<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

$sellerId = $_SESSION['user_id'];
$stmt = $conn->prepare('SELECT * FROM products WHERE seller_id = ? ORDER BY created_at DESC');
$stmt->bind_param('i', $sellerId);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($products);
$conn->close();