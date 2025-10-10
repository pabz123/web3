<?php
// register.php - register new user
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db_connect.php';
session_start();

$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if(!$username || !$email || !$password){
    http_response_code(422);
    echo json_encode(['error'=>'Missing fields']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email OR username = :username LIMIT 1');
    $stmt->execute([':email'=>$email, ':username'=>$username]);
    if($stmt->fetch()){
        http_response_code(409);
        echo json_encode(['error'=>'User already exists']);
        exit;
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (username,email,password_hash,created_at) VALUES (:u,:e,:p,NOW())');
    $stmt->execute([':u'=>$username, ':e'=>$email, ':p'=>$hash]);
    echo json_encode(['ok'=>true,'msg'=>'Registered']);
} catch(Exception $e){
    http_response_code(500);
    error_log('Register error: '.$e->getMessage());
    echo json_encode(['error'=>'Server error']);
}
?>