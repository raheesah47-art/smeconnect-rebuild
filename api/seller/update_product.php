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
$id = $_POST['id'] ?? 0;

$name = $_POST['name'] ?? '';
$district = $_POST['district'] ?? '';
$category = $_POST['category'] ?? '';
$price = (float)($_POST['price'] ?? 0);
$originalPrice = isset($_POST['original_price']) && $_POST['original_price'] !== '' ? (float)$_POST['original_price'] : null;

$imageUrl = null;
$updateImage = false;

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $tmpPath = $_FILES['image']['tmp_name'];

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
    $updateImage = true;
}

if ($updateImage) {
    $stmt = $conn->prepare('
        UPDATE products SET name=?, district=?, category=?, price=?, original_price=?, image_url=?
        WHERE id=? AND seller_id=?
    ');
    $stmt->bind_param('sssddsii', $name, $district, $category, $price, $originalPrice, $imageUrl, $id, $sellerId);
} else {
    // No new photo chosen — keep the existing image_url as-is
    $stmt = $conn->prepare('
        UPDATE products SET name=?, district=?, category=?, price=?, original_price=?
        WHERE id=? AND seller_id=?
    ');
    $stmt->bind_param('sssddii', $name, $district, $category, $price, $originalPrice, $id, $sellerId);
}
$stmt->execute();

echo json_encode(['success' => true]);
$conn->close();