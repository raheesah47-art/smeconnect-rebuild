<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$district = $data['district'];
$session_id = session_id();
$user_id = $_SESSION['user_id'] ?? null;

// 1. Get current cart items
if ($user_id) {
    $stmt = $conn->prepare('
        SELECT c.product_id, c.quantity, p.name, p.price
        FROM cart_items c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ');
    $stmt->bind_param('i', $user_id);
} else {
    $stmt = $conn->prepare('
        SELECT c.product_id, c.quantity, p.name, p.price
        FROM cart_items c
        JOIN products p ON c.product_id = p.id
        WHERE c.session_id = ? AND c.user_id IS NULL
    ');
    $stmt->bind_param('s', $session_id);
}
$stmt->execute();
$cartItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

if (count($cartItems) === 0) {
    echo json_encode(['error' => 'Cart is empty']);
    exit;
}

// 2. Calculate total
$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

// 3. Create the order
$paymentMethod = $data['payment_method'] ?? 'unpaid';
$orderStmt = $conn->prepare('INSERT INTO orders (session_id, user_id, district, total, payment_method) VALUES (?, ?, ?, ?, ?)');
$orderStmt->bind_param('siids', $session_id, $user_id, $district, $total, $paymentMethod);
$orderStmt->execute();
$orderId = $conn->insert_id;

// 4. Copy each cart item into order_items
$itemStmt = $conn->prepare('
    INSERT INTO order_items (order_id, product_id, product_name, price, quantity)
    VALUES (?, ?, ?, ?, ?)
');
foreach ($cartItems as $item) {
    $itemStmt->bind_param('iisdi', $orderId, $item['product_id'], $item['name'], $item['price'], $item['quantity']);
    $itemStmt->execute();
}

// 5. Log initial status
$logStmt = $conn->prepare('INSERT INTO order_status_log (order_id, status) VALUES (?, ?)');
$status = 'Placed';
$logStmt->bind_param('is', $orderId, $status);
$logStmt->execute();

// 6. Clear the cart
if ($user_id) {
    $clearStmt = $conn->prepare('DELETE FROM cart_items WHERE user_id = ?');
    $clearStmt->bind_param('i', $user_id);
} else {
    $clearStmt = $conn->prepare('DELETE FROM cart_items WHERE session_id = ? AND user_id IS NULL');
    $clearStmt->bind_param('s', $session_id);
}
$clearStmt->execute();

echo json_encode(['success' => true, 'order_id' => $orderId]);
$conn->close();