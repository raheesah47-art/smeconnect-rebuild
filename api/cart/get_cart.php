<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require '../../config/db.php';

$session_id = session_id();
$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $stmt = $conn->prepare('
        SELECT c.id AS cart_item_id, c.quantity, p.id AS product_id, p.name, p.price, p.district, p.image_url
        FROM cart_items c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ');
    $stmt->bind_param('i', $user_id);
} else {
    $stmt = $conn->prepare('
        SELECT c.id AS cart_item_id, c.quantity, p.id AS product_id, p.name, p.price, p.district, p.image_url
        FROM cart_items c
        JOIN products p ON c.product_id = p.id
        WHERE c.session_id = ? AND c.user_id IS NULL
    ');
    $stmt->bind_param('s', $session_id);
}
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items);
$conn->close();