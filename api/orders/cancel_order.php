<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$orderId = isset($data['order_id']) ? (int)$data['order_id'] : 0;
$session_id = session_id();

if ($orderId <= 0) {
    echo json_encode(['error' => 'Invalid order ID']);
    exit;
}

// 1. Confirm this order belongs to the current buyer's session
$stmt = $conn->prepare('SELECT id FROM orders WHERE id = ? AND session_id = ?');
$stmt->bind_param('is', $orderId, $session_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo json_encode(['error' => 'Order not found for your session.']);
    exit;
}

// 2. Confirm the order is still in "Placed" status (nothing further has happened)
$stmt = $conn->prepare('SELECT status FROM order_status_log WHERE order_id = ? ORDER BY created_at DESC LIMIT 1');
$stmt->bind_param('i', $orderId);
$stmt->execute();
$latestStatus = $stmt->get_result()->fetch_assoc();

if (!$latestStatus || $latestStatus['status'] !== 'Placed') {
    echo json_encode(['error' => 'This order can no longer be cancelled — it has already been confirmed by the seller.']);
    exit;
}

// 3. Log the cancellation
$stmt = $conn->prepare('INSERT INTO order_status_log (order_id, status) VALUES (?, ?)');
$status = 'Cancelled';
$stmt->bind_param('is', $orderId, $status);
$stmt->execute();

echo json_encode(['success' => true]);
$conn->close();