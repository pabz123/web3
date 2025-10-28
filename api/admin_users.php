<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

// Check if user is admin
if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Fetch all users
$stmt = $conn->prepare("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$stmt->close();

echo json_encode(['users' => $users]);
