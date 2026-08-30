<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

$sellerId = $_SESSION['user_id'];

// Find all orders that include items from this seller
$stmt = $conn->prepare(
    'SELECT DISTINCT o.id AS order_id, o.district, o.buyer_name, o.buyer_phone, o.total, o.created_at
     FROM orders o
     JOIN order_items oi ON oi.order_id = o.id
     JOIN products p ON oi.product_id = p.id
     WHERE p.seller_id = ?
     ORDER BY o.created_at DESC'
);
$stmt->bind_param('i', $sellerId);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

foreach ($orders as &$order) {
    // Only this seller's items within the order
    $itemStmt = $conn->prepare(
        'SELECT oi.* FROM order_items oi
         JOIN products p ON oi.product_id = p.id
         WHERE oi.order_id = ? AND p.seller_id = ?'
    );
    $itemStmt->bind_param('ii', $order['order_id'], $sellerId);
    $itemStmt->execute();
    $order['items'] = $itemStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $itemStmt->close();

    $logStmt = $conn->prepare('SELECT status, created_at FROM order_status_log WHERE order_id = ? ORDER BY created_at ASC');
    $logStmt->bind_param('i', $order['order_id']);
    $logStmt->execute();
    $log = $logStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $logStmt->close();

    if (!empty($log)) {
        $last = end($log);
        $order['current_status'] = $last['status'];
    } else {
        $order['current_status'] = null;
    }
}

echo json_encode($orders);
$conn->close();
