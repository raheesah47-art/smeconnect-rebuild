<?php
session_start();
error_log('SESSION ID: ' . session_id());
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'];
$session_id = session_id();

$stmt = $conn->prepare('SELECT id, quantity FROM cart_items WHERE session_id = ? AND product_id = ?');
$stmt->bind_param('si', $session_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $newQty = $row['quantity'] + 1;
    $update = $conn->prepare('UPDATE cart_items SET quantity = ? WHERE id = ?');
    $update->bind_param('ii', $newQty, $row['id']);
    $update->execute();
} else {
    $insert = $conn->prepare('INSERT INTO cart_items (session_id, product_id, quantity) VALUES (?, ?, 1)');
    $insert->bind_param('si', $session_id, $product_id);
    $insert->execute();
}

echo json_encode(['success' => true]);
$conn->close();