<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

$sql = "
    SELECT
        seller_name,
        COUNT(*) AS product_count,
        ROUND(AVG(trust_score)) AS avg_trust,
        (SELECT p2.district FROM products p2 WHERE p2.seller_name = p.seller_name AND p2.district IS NOT NULL LIMIT 1) AS district,
        (SELECT p3.category FROM products p3 WHERE p3.seller_name = p.seller_name
            GROUP BY p3.category ORDER BY COUNT(*) DESC LIMIT 1) AS top_category
    FROM products p
    WHERE seller_name IS NOT NULL AND seller_name != ''
    GROUP BY seller_name
    ORDER BY avg_trust DESC
    LIMIT 12
";

$result = $conn->query($sql);
$makers = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($makers);
$conn->close();