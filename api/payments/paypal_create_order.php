<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require '../../config/db.php';
require '../../config/paypal_config.php';

// Get an access token from PayPal
$ch = curl_init(PAYPAL_API_BASE . '/v1/oauth2/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ':' . PAYPAL_SECRET);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
$response = curl_exec($ch);
curl_close($ch);
$accessToken = json_decode($response, true)['access_token'];

// Calculate cart total (reuse your existing logic)
$session_id = session_id();
$stmt = $conn->prepare('
    SELECT c.quantity, p.price FROM cart_items c
    JOIN products p ON c.product_id = p.id
    WHERE c.session_id = ?
');
$stmt->bind_param('s', $session_id);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$totalMUR = 0;
foreach ($items as $item) {
    $totalMUR += $item['price'] * $item['quantity'];
}

// Rough MUR to USD conversion for sandbox purposes (approx rate, fine for testing)
$totalUSD = number_format($totalMUR / 46, 2, '.', '');

// Create the PayPal order
$orderPayload = json_encode([
    'intent' => 'CAPTURE',
    'purchase_units' => [[
        'amount' => [
            'currency_code' => 'USD',
            'value' => $totalUSD
        ]
    ]]
]);

$ch = curl_init(PAYPAL_API_BASE . '/v2/checkout/orders');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $accessToken
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $orderPayload);
$response = curl_exec($ch);
curl_close($ch);

echo $response; // contains the PayPal order ID the frontend needs
$conn->close();