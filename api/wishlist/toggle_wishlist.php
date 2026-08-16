<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Please log in to use wishlist']);
    exit;
}

$userId = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['product_id'];

// Check if it's already wishlisted
$check = $conn->prepare('SELECT id FROM wishlist WHERE user_id = ? AND product_id = ?');
$check->bind_param('ii', $userId, $productId);
$check->execute();
$existing = $check->get_result()->fetch_assoc();

if ($existing) {
    // Already wishlisted -> remove it
    $del = $conn->prepare('DELETE FROM wishlist WHERE id = ?');
    $del->bind_param('i', $existing['id']);
    $del->execute();
    echo json_encode(['success' => true, 'wishlisted' => false]);
} else {
    // Not wishlisted -> add it
    $ins = $conn->prepare('INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)');
    $ins->bind_param('ii', $userId, $productId);
    $ins->execute();
    echo json_encode(['success' => true, 'wishlisted' => true]);
}

$conn->close();