<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require '../config/db.php';

$order_id = $_GET['order_id'];

$orderStmt = $conn->prepare('SELECT * FROM orders WHERE id = ?');
$orderStmt->bind_param('i', $order_id);
$orderStmt->execute();
$order = $orderStmt->get_result()->fetch_assoc();

$itemsStmt = $conn->prepare('SELECT * FROM order_items WHERE order_id = ?');
$itemsStmt->bind_param('i', $order_id);
$itemsStmt->execute();
$items = $itemsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$logStmt = $conn->prepare('SELECT status, created_at FROM order_status_log WHERE order_id = ? ORDER BY created_at ASC');
$logStmt->bind_param('i', $order_id);
$logStmt->execute();
$statusLog = $logStmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'order' => $order,
    'items' => $items,
    'status_log' => $statusLog
]);
$conn->close();