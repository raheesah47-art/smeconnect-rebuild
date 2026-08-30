<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

// Orders are tracked by session_id, so no login is required to view them
$session_id = session_id();

$stmt = $conn->prepare('SELECT * FROM orders WHERE session_id = ? ORDER BY created_at DESC');
$stmt->bind_param('s', $session_id);
$stmt->execute();
$orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

foreach ($orders as &$order) {
    $itemStmt = $conn->prepare('SELECT * FROM order_items WHERE order_id = ?');
    $itemStmt->bind_param('i', $order['id']);
    $itemStmt->execute();
    $order['items'] = $itemStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $logStmt = $conn->prepare('SELECT status, created_at FROM order_status_log WHERE order_id = ? ORDER BY created_at ASC');
    $logStmt->bind_param('i', $order['id']);
    $logStmt->execute();
    $order['status_log'] = $logStmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

echo json_encode($orders);
$conn->close();