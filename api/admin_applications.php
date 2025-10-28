<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

// Check if user is admin
if (($_SESSION['user']['role'] ?? '') !== 'admin') {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Fetch all applications with job and user details
$stmt = $conn->prepare("
    SELECT 
        a.id,
        a.user_id,
        a.job_id,
        a.full_name as applicant_name,
        a.email,
        a.phone,
        a.cover_letter,
        a.cv_file,
        a.applied_at,
        a.status,
        j.title as job_title,
        j.company
    FROM applications a
    LEFT JOIN jobs j ON a.job_id = j.id
    ORDER BY a.applied_at DESC
");
$stmt->execute();
$result = $stmt->get_result();

$applications = [];
while ($row = $result->fetch_assoc()) {
    $applications[] = $row;
}

$stmt->close();

echo json_encode(['applications' => $applications]);
