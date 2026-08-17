<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$orderId = isset($data['order_id']) ? (int)$data['order_id'] : 0;
$newStatus = isset($data['status']) ? $data['status'] : null;
$sellerId = $_SESSION['user_id'];

$validStatuses = ['Placed', 'Confirmed', 'Out for delivery', 'Delivered'];
if (!in_array($newStatus, $validStatuses)) {
    echo json_encode(['error' => 'Invalid status']);
    exit;
}

// Security check: confirm this seller actually has products in this order
$check = $conn->prepare('
    SELECT COUNT(*) as cnt FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ? AND p.seller_id = ?
');
$check->bind_param('ii', $orderId, $sellerId);
$check->execute();
$result = $check->get_result()->fetch_assoc();
$check->close();

if ($result['cnt'] == 0) {
    echo json_encode(['error' => 'You do not have items in this order']);
    exit;
}

$stmt = $conn->prepare('INSERT INTO order_status_log (order_id, status) VALUES (?, ?)');
$stmt->bind_param('is', $orderId, $newStatus);
$stmt->execute();

// If delivered, recalculate the trust score for every seller involved in this order
if ($newStatus === 'Delivered') {
    $sellerStmt = $conn->prepare('
        SELECT DISTINCT p.seller_id FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ');
    $sellerStmt->bind_param('i', $orderId);
    $sellerStmt->execute();
    $sellers = $sellerStmt->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($sellers as $s) {
        $sid = $s['seller_id'];
        // Simplified inline recalculation (same formula as calculate_trust_score.php)
        $score = 70;
        $oc = $conn->prepare('SELECT COUNT(DISTINCT oi.order_id) as c FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE p.seller_id=?');
        $oc->bind_param('i', $sid); $oc->execute();
        $orderCount = $oc->get_result()->fetch_assoc()['c'];
        $score += min($orderCount, 15);

        $dc = $conn->prepare('SELECT COUNT(DISTINCT oi.order_id) as c FROM order_items oi JOIN products p ON oi.product_id=p.id WHERE p.seller_id=? AND oi.order_id IN (SELECT order_id FROM order_status_log WHERE status="Delivered")');
        $dc->bind_param('i', $sid); $dc->execute();
        $deliveredCount = $dc->get_result()->fetch_assoc()['c'];
        if ($orderCount > 0) $score += round(($deliveredCount / $orderCount) * 10);

        $ac = $conn->prepare('SELECT DATEDIFF(NOW(), created_at) as d FROM users WHERE id=?');
        $ac->bind_param('i', $sid); $ac->execute();
        $daysOld = $ac->get_result()->fetch_assoc()['d'];
        $score += min(floor($daysOld / 30), 5);

        $finalScore = min($score, 100);
        $upd = $conn->prepare('UPDATE products SET trust_score=? WHERE seller_id=?');
        $upd->bind_param('ii', $finalScore, $sid);
        $upd->execute();
    }
}

echo json_encode(['success' => true]);

$conn->close();
