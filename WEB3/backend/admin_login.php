<?php
// admin_login.php - simple admin auth (admins should be stored in admin table)
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db_connect.php';
session_start();

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if(!$username || !$password){
    http_response_code(422);
    echo json_encode(['error'=>'Missing fields']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id,username,password_hash FROM admin WHERE username = :u LIMIT 1');
    $stmt->execute([':u'=>$username]);
    $admin = $stmt->fetch();
    if(!$admin || !password_verify($password, $admin['password_hash'])){
        http_response_code(401);
        echo json_encode(['error'=>'Invalid credentials']);
        exit;
    }
    session_regenerate_id(true);
    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_user'] = $admin['username'];
    echo json_encode(['ok'=>true]);
} catch(Exception $e){
    http_response_code(500);
    error_log('Admin login error: '.$e->getMessage());
    echo json_encode(['error'=>'Server error']);
}
?>