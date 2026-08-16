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
$sellerName = $_SESSION['user_name'];

$stmt = $conn->prepare('
    INSERT INTO products (name, seller_id, seller_name, district, category, price, original_price, trust_score)
    VALUES (?, ?, ?, ?, ?, ?, ?, 80)
');
$stmt->bind_param(
    'sisssdd',
    $data['name'], $sellerId, $sellerName, $data['district'], $data['category'], $data['price'], $data['original_price']
);
$stmt->execute();

echo json_encode(['success' => true, 'product_id' => $conn->insert_id]);
$conn->close();