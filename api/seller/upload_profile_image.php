<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'seller') {
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['error' => 'No image received']);
    exit;
}

$tmpPath = $_FILES['image']['tmp_name'];
$imageInfo = getimagesize($tmpPath);
$allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
if ($imageInfo === false || !in_array($imageInfo['mime'], $allowedTypes)) {
    echo json_encode(['error' => 'That file is not a valid image. Please upload a JPG, PNG, or WEBP.']);
    exit;
}

$extMap = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
$ext = $extMap[$imageInfo['mime']];
$filename = uniqid('profile_', true) . '.' . $ext;
$destPath = __DIR__ . '/../../uploads/products/' . $filename;

if (!move_uploaded_file($tmpPath, $destPath)) {
    echo json_encode(['error' => 'Could not save the uploaded image.']);
    exit;
}

$imageUrl = '/smeconnect/uploads/products/' . $filename;
$sellerId = $_SESSION['user_id'];

$stmt = $conn->prepare('UPDATE users SET profile_image = ? WHERE id = ?');
$stmt->bind_param('si', $imageUrl, $sellerId);
$stmt->execute();

echo json_encode(['success' => true, 'image_url' => $imageUrl]);
$conn->close();