<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

$productId = $_GET['product_id'] ?? 0;

$stmt = $conn->prepare('SELECT buyer_name, rating, comment, created_at FROM reviews WHERE product_id = ? ORDER BY created_at DESC');
$stmt->bind_param('i', $productId);
$stmt->execute();
$reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$avgRating = 0;
if (count($reviews) > 0) {
    $avgRating = round(array_sum(array_column($reviews, 'rating')) / count($reviews), 1);
}

echo json_encode([
    'reviews' => $reviews,
    'average_rating' => $avgRating,
    'review_count' => count($reviews)
]);
$conn->close();