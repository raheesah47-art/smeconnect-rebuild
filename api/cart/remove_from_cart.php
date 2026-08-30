<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$cart_item_id = $data['cart_item_id'];
$session_id = session_id();

$stmt = $conn->prepare('DELETE FROM cart_items WHERE id = ? AND session_id = ?');
$stmt->bind_param('is', $cart_item_id, $session_id);
$stmt->execute();

echo json_encode(['success' => true]);
$conn->close();