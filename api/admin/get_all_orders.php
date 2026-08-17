<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

$result = $conn->query('
    SELECT o.*,
    (SELECT status FROM order_status_log WHERE order_id = o.id ORDER BY created_at DESC LIMIT 1) as current_status
    FROM orders o ORDER BY o.created_at DESC
');
$orders = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($orders);
$conn->close();