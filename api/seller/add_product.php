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

$name = $_POST['name'] ?? '';
$district = $_POST['district'] ?? '';
$category = $_POST['category'] ?? '';
$price = (float)($_POST['price'] ?? 0);
$originalPrice = isset($_POST['original_price']) && $_POST['original_price'] !== '' ? (float)$_POST['original_price'] : null;

$imageUrl = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $tmpPath = $_FILES['image']['tmp_name'];

    // Confirm this is actually a real image, not just a renamed file
    $imageInfo = getimagesize($tmpPath);
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    if ($imageInfo === false || !in_array($imageInfo['mime'], $allowedTypes)) {
        echo json_encode(['error' => 'That file is not a valid image. Please upload a JPG, PNG, or WEBP.']);
        exit;
    }

    $extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $ext = $extMap[$imageInfo['mime']];
    $filename = uniqid('product_', true) . '.' . $ext;
    $destPath = __DIR__ . '/../../uploads/products/' . $filename;

    if (!move_uploaded_file($tmpPath, $destPath)) {
        echo json_encode(['error' => 'Could not save the uploaded image. Check that the uploads folder exists.']);
        exit;
    }

    $imageUrl = '/smeconnect/uploads/products/' . $filename;
}

$stmt = $conn->prepare('
    INSERT INTO products (name, seller_id, seller_name, district, category, price, original_price, trust_score, image_url)
    VALUES (?, ?, ?, ?, ?, ?, ?, 80, ?)
');
$stmt->bind_param(
    'sisssdds',
    $name, $sellerId, $sellerName, $district, $category, $price, $originalPrice, $imageUrl
);
$stmt->execute();

echo json_encode(['success' => true, 'product_id' => $conn->insert_id]);
$conn->close();