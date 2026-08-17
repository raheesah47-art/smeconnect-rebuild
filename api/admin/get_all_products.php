<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

$result = $conn->query('SELECT * FROM products ORDER BY created_at DESC');
$products = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($products);
$conn->close();