<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

try {
    $query = "
        SELECT 
            j.id,
            j.title,
            j.description,
            j.location,
            j.type,
            j.external_link,
            j.createdAt,
            e.company_name as company
        FROM jobs j
        LEFT JOIN employers e ON j.employer_id = e.id
        ORDER BY j.createdAt DESC
    ";

    $result = $conn->query($query);
    $jobs = [];

    while ($row = $result->fetch_assoc()) {
        $jobs[] = $row;
    }

    echo json_encode([
        'success' => true,
        'jobs' => $jobs
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
