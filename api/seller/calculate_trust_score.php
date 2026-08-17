<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

function calculateTrustScore($conn, $sellerId) {
    // Base score
    $score = 70;

    // Order volume (up to 15 points) — 1 point per order, capped at 15
    $stmt = $conn->prepare('
        SELECT COUNT(DISTINCT oi.order_id) as order_cnt
        FROM order_items oi JOIN products p ON oi.product_id = p.id
        WHERE p.seller_id = ?
    ');
    $stmt->bind_param('i', $sellerId);
    $stmt->execute();
    $orderCount = $stmt->get_result()->fetch_assoc()['order_cnt'];
    $score += min($orderCount, 15);

    // Delivery completion rate (up to 10 points)
    if ($orderCount > 0) {
        $stmt = $conn->prepare('
            SELECT COUNT(DISTINCT oi.order_id) as delivered_cnt
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            JOIN orders o ON oi.order_id = o.id
            WHERE p.seller_id = ? AND o.id IN (
                SELECT order_id FROM order_status_log WHERE status = "Delivered"
            )
        ');
        $stmt->bind_param('i', $sellerId);
        $stmt->execute();
        $deliveredCount = $stmt->get_result()->fetch_assoc()['delivered_cnt'];
        $completionRate = $deliveredCount / $orderCount;
        $score += round($completionRate * 10);
    }

    // Account age (up to 5 points) — 1 point per 30 days, capped at 5
    $stmt = $conn->prepare('SELECT DATEDIFF(NOW(), created_at) as days_old FROM users WHERE id = ?');
    $stmt->bind_param('i', $sellerId);
    $stmt->execute();
    $daysOld = $stmt->get_result()->fetch_assoc()['days_old'];
    $score += min(floor($daysOld / 30), 5);

    return min($score, 100);
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

$sellerId = $_SESSION['user_id'];
$newScore = calculateTrustScore($conn, $sellerId);

// Update all of this seller's products with the new score
$stmt = $conn->prepare('UPDATE products SET trust_score = ? WHERE seller_id = ?');
$stmt->bind_param('ii', $newScore, $sellerId);
$stmt->execute();

echo json_encode(['success' => true, 'trust_score' => $newScore]);
$conn->close();