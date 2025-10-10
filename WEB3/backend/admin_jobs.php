<?php
// admin_jobs.php - requires admin session, returns all jobs
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db_connect.php';
session_start();
if(empty($_SESSION['admin_id'])){
    http_response_code(401);
    echo json_encode(['error'=>'Not authorized']);
    exit;
}
try {
    $stmt = $pdo->query('SELECT * FROM jobs ORDER BY date_posted DESC LIMIT 500');
    $rows = $stmt->fetchAll();
    echo json_encode($rows);
} catch(Exception $e){
    http_response_code(500);
    error_log('Admin jobs error: '.$e->getMessage());
    echo json_encode([]);
}
?>