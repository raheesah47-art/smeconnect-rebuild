<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

$sellerId = $_SESSION['user_id'];
$filter = $_GET['filter'] ?? '';
$category = $_GET['category'] ?? '';

$sql = 'SELECT p.*, COALESCE(SUM(oi.quantity), 0) AS units_sold
        FROM products p
        LEFT JOIN order_items oi ON oi.product_id = p.id
        WHERE p.seller_id = ?';

$params = [$sellerId];
$types = 'i';

if ($category !== '') {
    $sql .= ' AND p.category = ?';
    $params[] = $category;
    $types .= 's';
}
if ($filter === 'deals') {
    $sql .= ' AND p.original_price IS NOT NULL AND p.original_price > p.price';
}

$sql .= ' GROUP BY p.id';

if ($filter === 'new') {
    $sql .= ' ORDER BY p.created_at DESC';
} elseif ($filter === 'bestsellers') {
    $sql .= ' ORDER BY units_sold DESC';
} else {
    $sql .= ' ORDER BY p.created_at DESC';
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode($products);
$conn->close();