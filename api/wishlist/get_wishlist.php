<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

$session_id = session_id();
$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $stmt = $conn->prepare('
        SELECT p.* FROM wishlist w
        JOIN products p ON w.product_id = p.id
        WHERE w.user_id = ?
        ORDER BY w.created_at DESC
    ');
    $stmt->bind_param('i', $user_id);
} else {
    $stmt = $conn->prepare('
        SELECT p.* FROM wishlist w
        JOIN products p ON w.product_id = p.id
        WHERE w.session_id = ? AND w.user_id IS NULL
        ORDER BY w.created_at DESC
    ');
    $stmt->bind_param('s', $session_id);
}
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($products);
$conn->close();