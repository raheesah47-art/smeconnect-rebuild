<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$cart_item_id = $data['cart_item_id'];
$quantity = $data['quantity'];
$session_id = session_id();

if ($quantity < 1) {
    echo json_encode(['error' => 'Quantity must be at least 1']);
    exit;
}

$stmt = $conn->prepare('UPDATE cart_items SET quantity = ? WHERE id = ? AND session_id = ?');
$stmt->bind_param('iis', $quantity, $cart_item_id, $session_id);
$stmt->execute();

echo json_encode(['success' => true]);
$conn->close();