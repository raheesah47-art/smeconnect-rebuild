<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require '../../config/db.php';

$data = json_decode(file_get_contents('php://input'), true);
$name = $data['name'];
$email = $data['email'];
$password = $data['password'];
$role = $data['role'] ?? 'buyer';

// Check if email already exists
$check = $conn->prepare('SELECT id FROM users WHERE email = ?');
$check->bind_param('s', $email);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode(['error' => 'Email already registered']);
    exit;
}

// Hash the password — never store plain text
$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare('INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)');
$stmt->bind_param('ssss', $name, $email, $passwordHash, $role);
$stmt->execute();

// Log them in immediately after registering
$_SESSION['user_id'] = $conn->insert_id;
$_SESSION['user_name'] = $name;
$_SESSION['user_role'] = $role;

echo json_encode(['success' => true, 'user_id' => $conn->insert_id]);
$conn->close();