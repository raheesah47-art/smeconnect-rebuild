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
$orderId = isset($data['order_id']) ? (int)$data['order_id'] : 0;
$newStatus = isset($data['status']) ? $data['status'] : null;
$sellerId = $_SESSION['user_id'];

$validStatuses = ['Placed', 'Confirmed', 'Out for delivery', 'Delivered'];
if (!in_array($newStatus, $validStatuses)) {
    echo json_encode(['error' => 'Invalid status']);
    exit;
}

// Security check: confirm this seller actually has products in this order
$check = $conn->prepare('
    SELECT COUNT(*) as cnt FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ? AND p.seller_id = ?
');
$check->bind_param('ii', $orderId, $sellerId);
$check->execute();
$result = $check->get_result()->fetch_assoc();
$check->close();

if ($result['cnt'] == 0) {
    echo json_encode(['error' => 'You do not have items in this order']);
    exit;
}

$stmt = $conn->prepare('INSERT INTO order_status_log (order_id, status) VALUES (?, ?)');
$stmt->bind_param('is', $orderId, $newStatus);
$stmt->execute();
$stmt->close();

echo json_encode(['success' => true]);
$conn->close();
