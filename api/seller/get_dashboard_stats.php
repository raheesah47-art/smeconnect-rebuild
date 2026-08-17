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
$sellerName = $_SESSION['user_name'];

// Product count
$stmt = $conn->prepare('SELECT COUNT(*) as cnt, AVG(trust_score) as avg_trust FROM products WHERE seller_id = ?');
$stmt->bind_param('i', $sellerId);
$stmt->execute();
$productStats = $stmt->get_result()->fetch_assoc();

// Orders + sales total (this seller's items only)
$stmt = $conn->prepare('
    SELECT COUNT(DISTINCT oi.order_id) as order_cnt, COALESCE(SUM(oi.price * oi.quantity), 0) as total_sales
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE p.seller_id = ?
');
$stmt->bind_param('i', $sellerId);
$stmt->execute();
$orderStats = $stmt->get_result()->fetch_assoc();

// Wishlist count
$stmt = $conn->prepare('
    SELECT COUNT(*) as cnt FROM wishlist w
    JOIN products p ON w.product_id = p.id
    WHERE p.seller_id = ?
');
$stmt->bind_param('i', $sellerId);
$stmt->execute();
$wishlistStats = $stmt->get_result()->fetch_assoc();

// Recent orders (last 5)
$stmt = $conn->prepare('
    SELECT DISTINCT o.id, o.total, o.created_at,
           (SELECT status FROM order_status_log WHERE order_id = o.id ORDER BY created_at DESC LIMIT 1) as status
    FROM orders o
    JOIN order_items oi ON oi.order_id = o.id
    JOIN products p ON oi.product_id = p.id
    WHERE p.seller_id = ?
    ORDER BY o.created_at DESC
    LIMIT 5
');
$stmt->bind_param('i', $sellerId);
$stmt->execute();
$recentOrders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'seller_name' => $sellerName,
    'product_count' => (int)$productStats['cnt'],
    'avg_trust' => round($productStats['avg_trust'] ?? 0),
    'order_count' => (int)$orderStats['order_cnt'],
    'total_sales' => (float)$orderStats['total_sales'],
    'wishlist_count' => (int)$wishlistStats['cnt'],
    'recent_orders' => $recentOrders
]);
$conn->close();