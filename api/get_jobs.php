<?php
// api/get_jobs.php
require_once __DIR__ . '/../includes/db.php';

$result = $conn->query("SELECT * FROM jobs ORDER BY created_at DESC");
$jobs = [];
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}
header('Content-Type: application/json');
echo json_encode(['jobs' => $jobs]);
?>
