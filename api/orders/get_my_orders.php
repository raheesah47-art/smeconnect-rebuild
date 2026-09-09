<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

$session_id = session_id();
$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $stmt = $conn->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
    $stmt->bind_param('i', $user_id);
} else {
    $stmt = $conn->prepare('SELECT * FROM orders WHERE session_id = ? AND user_id IS NULL ORDER BY created_at DESC');
    $stmt->bind_param('s', $session_id);
}
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