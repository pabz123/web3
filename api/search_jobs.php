<?php
// api/search_jobs.php
require_once __DIR__ . '/../includes/db.php';

// Get search query
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

header('Content-Type: application/json');

if ($q === '') {
    echo json_encode([]);
    exit;
}

// Use prepared statements to prevent SQL injection
$stmt = $conn->prepare("
    SELECT id, title, location, industry, type
    FROM jobs
    WHERE title LIKE CONCAT('%', ?, '%')
       OR location LIKE CONCAT('%', ?, '%')
       OR industry LIKE CONCAT('%', ?, '%')
    AND status = 'Open'
    ORDER BY createdAt DESC
    LIMIT 10
");

$stmt->bind_param('sss', $q, $q, $q);
$stmt->execute();

$result = $stmt->get_result();
$jobs = [];

while ($row = $result->fetch_assoc()) {
    $jobs[] = $row;
}

echo json_encode($jobs);
?>
