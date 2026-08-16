<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$sellerId = $_SESSION['user_id'];

$stmt = $conn->prepare('
    UPDATE products SET name=?, district=?, category=?, price=?, original_price=?
    WHERE id=? AND seller_id=?
');
$stmt->bind_param(
    'sssddii',
    $data['name'], $data['district'], $data['category'], $data['price'], $data['original_price'], $data['id'], $sellerId
);
$stmt->execute();

echo json_encode(['success' => true]);
$conn->close();