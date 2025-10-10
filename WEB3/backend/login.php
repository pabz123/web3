<?php
// login.php - authenticate user
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db_connect.php';
session_start();

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if(!$email || !$password){
    http_response_code(422);
    echo json_encode(['error'=>'Missing fields']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT id,username,email,password_hash FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email'=>$email]);
    $user = $stmt->fetch();
    if(!$user || !password_verify($password, $user['password_hash'])){
        http_response_code(401);
        echo json_encode(['error'=>'Invalid credentials']);
        exit;
    }
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];
    echo json_encode(['ok'=>true,'username'=>$user['username']]);
} catch(Exception $e){
    http_response_code(500);
    error_log('Login error: '.$e->getMessage());
    echo json_encode(['error'=>'Server error']);
}
?>