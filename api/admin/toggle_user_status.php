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
$userId = isset($data['id']) ? (int)$data['id'] : 0;

if ($userId <= 0) {
    echo json_encode(['error' => 'Invalid user ID']);
    exit;
}

// Prevent an admin from suspending their own account
if ($userId === (int)$_SESSION['user_id']) {
    echo json_encode(['error' => 'You cannot suspend your own account']);
    exit;
}

// Get current status
$stmt = $conn->prepare('SELECT is_active FROM users WHERE id = ?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo json_encode(['error' => 'User not found']);
    exit;
}

$newStatus = $user['is_active'] ? 0 : 1;

$update = $conn->prepare('UPDATE users SET is_active = ? WHERE id = ?');
$update->bind_param('ii', $newStatus, $userId);
$update->execute();

echo json_encode(['success' => true, 'is_active' => $newStatus]);
$conn->close();