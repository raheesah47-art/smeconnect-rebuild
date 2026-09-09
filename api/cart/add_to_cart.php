<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

require '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$product_id = isset($data['product_id']) ? (int)$data['product_id'] : 0;
$session_id = session_id();
$user_id = $_SESSION['user_id'] ?? null;

if ($product_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
    exit;
}

if ($user_id) {
    $stmt = $conn->prepare('SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?');
    $stmt->bind_param('ii', $user_id, $product_id);
} else {
    $stmt = $conn->prepare('SELECT id, quantity FROM cart_items WHERE session_id = ? AND product_id = ? AND user_id IS NULL');
    $stmt->bind_param('si', $session_id, $product_id);
}
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $newQty = (int)$row['quantity'] + 1;
    $update = $conn->prepare('UPDATE cart_items SET quantity = ? WHERE id = ?');
    $update->bind_param('ii', $newQty, $row['id']);
    $update->execute();
    $update->close();
} else {
    $insert = $conn->prepare('INSERT INTO cart_items (session_id, user_id, product_id, quantity) VALUES (?, ?, ?, 1)');
    $insert->bind_param('sii', $session_id, $user_id, $product_id);
    $insert->execute();
    $insert->close();
}

$stmt->close();
$conn->close();

echo json_encode(['success' => true]);