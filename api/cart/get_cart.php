<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

require '../../config/db.php';

$session_id = session_id();

$stmt = $conn->prepare('
    SELECT c.id AS cart_item_id, c.quantity, p.id AS product_id, p.name, p.price, p.district
    FROM cart_items c
    JOIN products p ON c.product_id = p.id
    WHERE c.session_id = ?
');
$stmt->bind_param('s', $session_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($items);