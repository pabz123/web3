<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

// Check if user is admin
if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Fetch all jobs
$stmt = $conn->prepare("SELECT id, title, company, location, job_type, salary_range, posted_date FROM jobs ORDER BY posted_date DESC");
$stmt->execute();
$result = $stmt->get_result();

$jobs = [];
while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}

$stmt->close();

echo json_encode(['jobs' => $jobs]);
