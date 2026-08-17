<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$stmt = $conn->prepare('DELETE FROM products WHERE id = ?');
$stmt->bind_param('i', $data['id']);
$stmt->execute();

echo json_encode(['success' => true]);
$conn->close();