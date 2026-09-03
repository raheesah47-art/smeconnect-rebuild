<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$orderId = $data['order_id'] ?? 0;
$productId = $data['product_id'] ?? 0;
$rating = (int)($data['rating'] ?? 0);
$comment = trim($data['comment'] ?? '');
$session_id = session_id();

if ($rating < 1 || $rating > 5) {
    echo json_encode(['error' => 'Rating must be between 1 and 5.']);
    exit;
}

// 1. Confirm this order belongs to the current buyer's session
$stmt = $conn->prepare('SELECT buyer_name FROM orders WHERE id = ? AND session_id = ?');
$stmt->bind_param('is', $orderId, $session_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo json_encode(['error' => 'This order was not found for your session.']);
    exit;
}

// 2. Confirm the order actually contains this product
$stmt = $conn->prepare('SELECT id FROM order_items WHERE order_id = ? AND product_id = ?');
$stmt->bind_param('ii', $orderId, $productId);
$stmt->execute();
if (!$stmt->get_result()->fetch_assoc()) {
    echo json_encode(['error' => 'This product was not part of that order.']);
    exit;
}

// 3. Confirm the order has actually been delivered
$stmt = $conn->prepare('SELECT status FROM order_status_log WHERE order_id = ? ORDER BY created_at DESC LIMIT 1');
$stmt->bind_param('i', $orderId);
$stmt->execute();
$latestStatus = $stmt->get_result()->fetch_assoc();

if (!$latestStatus || $latestStatus['status'] !== 'Delivered') {
    echo json_encode(['error' => 'You can only review items after they have been delivered.']);
    exit;
}

// 4. Insert the review (unique key blocks duplicates automatically)
$stmt = $conn->prepare('INSERT INTO reviews (product_id, order_id, buyer_name, rating, comment) VALUES (?, ?, ?, ?, ?)');
$stmt->bind_param('iisis', $productId, $orderId, $order['buyer_name'], $rating, $comment);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    if ($conn->errno === 1062) {
        echo json_encode(['error' => 'You have already reviewed this item.']);
    } else {
        echo json_encode(['error' => 'Could not save review.']);
    }
}
$conn->close();