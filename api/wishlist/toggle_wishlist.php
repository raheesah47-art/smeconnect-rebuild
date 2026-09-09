<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

$session_id = session_id();
$user_id = $_SESSION['user_id'] ?? null;
$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['product_id'];

if ($user_id) {
    $check = $conn->prepare('SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?');
    $check->bind_param('ii', $user_id, $productId);
} else {
    $check = $conn->prepare('SELECT id FROM wishlist WHERE session_id = ? AND product_id = ? AND user_id IS NULL');
    $check->bind_param('si', $session_id, $productId);
}
$check->execute();
$existing = $check->get_result()->fetch_assoc();

if ($existing) {
    $del = $conn->prepare('DELETE FROM wishlist WHERE id = ?');
    $del->bind_param('i', $existing['id']);
    $del->execute();
    echo json_encode(['success' => true, 'wishlisted' => false]);
} else {
    $ins = $conn->prepare('INSERT INTO wishlist (session_id, user_id, product_id) VALUES (?, ?, ?)');
    $ins->bind_param('sii', $session_id, $user_id, $productId);
    $ins->execute();
    echo json_encode(['success' => true, 'wishlisted' => true]);
}

$conn->close();