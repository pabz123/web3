<?php
// add_job.php - admin-only add job
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db_connect.php';
session_start();
if(empty($_SESSION['admin_id'])){
    http_response_code(401);
    echo json_encode(['error'=>'Not authorized']);
    exit;
}

$title = trim($_POST['title'] ?? '');
$company = trim($_POST['company'] ?? '');
$location = trim($_POST['location'] ?? '');
$description = trim($_POST['description'] ?? '');
$link = trim($_POST['link'] ?? '');

if(!$title){
    http_response_code(422);
    echo json_encode(['error'=>'Missing title']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO jobs (title,company,location,description,link,date_posted) VALUES (:t,:c,:l,:d,:k,NOW())');
    $stmt->execute([':t'=>$title,':c'=>$company,':l'=>$location,':d'=>$description,':k'=>$link]);
    echo json_encode(['ok'=>true,'id'=>$pdo->lastInsertId()]);
} catch(Exception $e){
    http_response_code(500);
    error_log('Add job error: '.$e->getMessage());
    echo json_encode(['error'=>'Server error']);
}
?>