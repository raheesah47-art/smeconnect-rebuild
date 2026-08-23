<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

require '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$cart_item_id = isset($data['cart_item_id']) ? (int)$data['cart_item_id'] : 0;
$quantity = isset($data['quantity']) ? (int)$data['quantity'] : 0;
$session_id = session_id();

if ($cart_item_id <= 0 || $quantity < 1) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid cart item or quantity']);
    exit;
}

$stmt = $conn->prepare('UPDATE cart_items SET quantity = ? WHERE id = ? AND session_id = ?');
$stmt->bind_param('iis', $quantity, $cart_item_id, $session_id);
$stmt->execute();

$stmt->close();
$conn->close();

echo json_encode(['success' => true]);