<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';


$seller_name = isset($_GET['seller']) ? trim($_GET['seller']) : '';

if ($seller_name === '') {
    echo json_encode(['error' => 'No seller specified']);
    exit;
}

// Get seller summary info (same style as get_makers.php)
$stmt = $conn->prepare("
    SELECT
        p.seller_name,
        COUNT(*) AS product_count,
        ROUND(AVG(p.trust_score)) AS avg_trust,
        (SELECT p2.district FROM products p2 WHERE p2.seller_name = p.seller_name AND p2.district IS NOT NULL LIMIT 1) AS district,
        (SELECT u.profile_image FROM users u WHERE u.name = p.seller_name LIMIT 1) AS profile_image,
        (SELECT u.created_at FROM users u WHERE u.name = p.seller_name LIMIT 1) AS joined_date
    FROM products p
    WHERE TRIM(p.seller_name) = TRIM(?)
    GROUP BY p.seller_name
");
$stmt->bind_param('s', $seller_name);
$stmt->execute();
$maker = $stmt->get_result()->fetch_assoc();

if (!$maker) {
    echo json_encode(['error' => 'Maker not found']);
    exit;
}

// Get this seller's products
$stmt2 = $conn->prepare("
    SELECT id, name, price, original_price, image_url, category, trust_score
    FROM products
    WHERE TRIM(seller_name) = TRIM(?)
    ORDER BY id DESC
");
$stmt2->bind_param('s', $seller_name);
$stmt2->execute();
$products = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'maker' => $maker,
    'products' => $products
]);

$conn->close();