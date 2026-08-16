<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require '../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'];
$password = $data['password'];

$stmt = $conn->prepare('SELECT id, name, password_hash, role FROM users WHERE email = ?');
$stmt->bind_param('s', $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user || !password_verify($password, $user['password_hash'])) {
    echo json_encode(['error' => 'Invalid email or password']);
    exit;
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_role'] = $user['role'];

echo json_encode(['success' => true, 'name' => $user['name'], 'role' => $user['role']]);
$conn->close();