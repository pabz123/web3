<?php
// fetch_jobs.php - return jobs from DB, optional q param to search title/company/location
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db_connect.php';

$q = trim($_GET['q'] ?? '');
$params = [];
$sql = 'SELECT id,title,company,location,description,link,date_posted FROM jobs';
if($q !== ''){
    $sql .= ' WHERE title LIKE :q OR company LIKE :q OR location LIKE :q';
    $params[':q'] = '%' . $q . '%';
}
$sql .= ' ORDER BY date_posted DESC LIMIT 100';
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
    echo json_encode($rows);
} catch(Exception $e){
    http_response_code(500);
    error_log('Fetch jobs error: '.$e->getMessage());
    echo json_encode([]);
}
?>