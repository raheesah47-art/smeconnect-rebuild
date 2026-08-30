<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require '../../config/db.php';
require '../../config/paypal_config.php';

$data = json_decode(file_get_contents('php://input'), true);
$paypalOrderId = $data['orderID'];
$buyerName = trim($data['buyer_name'] ?? '');
$buyerPhone = trim($data['buyer_phone'] ?? '');
$district = trim($data['district'] ?? '');

if ($buyerName === '' || $buyerPhone === '' || $district === '') {
    echo json_encode(['success' => false, 'error' => 'Missing buyer details']);
    exit;
}

// Get access token (same as before)
$ch = curl_init(PAYPAL_API_BASE . '/v1/oauth2/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ':' . PAYPAL_SECRET);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo json_encode(['success' => false, 'error' => 'Could not reach PayPal: ' . curl_error($ch)]);
    exit;
}
curl_close($ch);
$accessToken = json_decode($response, true)['access_token'];

// Capture the payment (this is the step that actually finalizes it)
$ch = curl_init(PAYPAL_API_BASE . "/v2/checkout/orders/{$paypalOrderId}/capture");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $accessToken
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo json_encode(['success' => false, 'error' => 'Could not reach PayPal: ' . curl_error($ch)]);
    exit;
}
curl_close($ch);
$result = json_decode($response, true);

if ($result['status'] === 'COMPLETED') {
    // NOW it's safe to create the real order in your database
    $session_id = session_id();

    $stmt = $conn->prepare('
        SELECT c.product_id, c.quantity, p.name, p.price
        FROM cart_items c JOIN products p ON c.product_id = p.id
        WHERE c.session_id = ?
    ');
    $stmt->bind_param('s', $session_id);
    $stmt->execute();
    $cartItems = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    $total = 0;
    foreach ($cartItems as $item) $total += $item['price'] * $item['quantity'];

    $orderStmt = $conn->prepare('INSERT INTO orders (session_id, district, buyer_name, buyer_phone, total, payment_method) VALUES (?, ?, ?, ?, ?, ?)');
    $paymentMethod = 'paypal';
    $orderStmt->bind_param('ssssds', $session_id, $district, $buyerName, $buyerPhone, $total, $paymentMethod);
    $orderStmt->execute();
    $orderId = $conn->insert_id;

    $itemStmt = $conn->prepare('INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)');
    foreach ($cartItems as $item) {
        $itemStmt->bind_param('iisdi', $orderId, $item['product_id'], $item['name'], $item['price'], $item['quantity']);
        $itemStmt->execute();
    }

    $logStmt = $conn->prepare('INSERT INTO order_status_log (order_id, status) VALUES (?, ?)');
    $status = 'Placed';
    $logStmt->bind_param('is', $orderId, $status);
    $logStmt->execute();

    $clearStmt = $conn->prepare('DELETE FROM cart_items WHERE session_id = ?');
    $clearStmt->bind_param('s', $session_id);
    $clearStmt->execute();

    echo json_encode(['success' => true, 'order_id' => $orderId]);
} else {
    echo json_encode(['success' => false, 'error' => 'Payment not completed']);
}

$conn->close();