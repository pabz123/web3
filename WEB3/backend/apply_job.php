<?php
// apply_job.php - logged-in users apply to a job
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db_connect.php';
session_start();

if(empty($_SESSION['user_id'])){
    http_response_code(401);
    echo json_encode(['error'=>'Not authenticated']);
    exit;
}

$job_id = intval($_POST['job_id'] ?? 0);
$cover = trim($_POST['cover_letter'] ?? '');

if(!$job_id){
    http_response_code(422);
    echo json_encode(['error'=>'Missing job_id']);
    exit;
}

try {
    $stmt = $pdo->prepare('INSERT INTO applications (user_id,job_id,cover_letter,applied_at) VALUES (:uid,:jid,:cov,NOW())');
    $stmt->execute([':uid'=>$_SESSION['user_id'], ':jid'=>$job_id, ':cov'=>$cover]);
    echo json_encode(['ok'=>true,'msg'=>'Applied']);
} catch(Exception $e){
    http_response_code(500);
    error_log('Apply job error: '.$e->getMessage());
    echo json_encode(['error'=>'Server error']);
}
?>