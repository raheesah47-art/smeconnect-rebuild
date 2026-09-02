<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
require '../../config/db.php';

if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare('SELECT profile_image FROM users WHERE id = ?');
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    echo json_encode([
        'logged_in' => true,
        'name' => $_SESSION['user_name'],
        'role' => $_SESSION['user_role'],
        'profile_image' => $row['profile_image'] ?? null
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}