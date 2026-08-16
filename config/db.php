<?php
$host = 'localhost';
$dbname = 'smeconnect';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed']));
}