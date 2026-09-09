<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

require '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$cart_item_id = isset($data['cart_item_id']) ? (int)$data['cart_item_id'] : 0;
$session_id = session_id();
$user_id = $_SESSION['user_id'] ?? null;

if ($cart_item_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid cart item ID']);
    exit;
}

if ($user_id) {
    $stmt = $conn->prepare('DELETE FROM cart_items WHERE id = ? AND user_id = ?');
    $stmt->bind_param('ii', $cart_item_id, $user_id);
} else {
    $stmt = $conn->prepare('DELETE FROM cart_items WHERE id = ? AND session_id = ? AND user_id IS NULL');
    $stmt->bind_param('is', $cart_item_id, $session_id);
}
$stmt->execute();

$stmt->close();
$conn->close();

echo json_encode(['success' => true]);