<?php
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json');

try {
    // Total jobs
    $totalJobsQuery = $conn->query("SELECT COUNT(*) as count FROM jobs");
    $totalJobs = $totalJobsQuery->fetch_assoc()['count'];

    // Today's imports
    $todayQuery = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE DATE(createdAt) = CURDATE()");
    $todayImports = $todayQuery->fetch_assoc()['count'];

    // Total companies
    $companiesQuery = $conn->query("SELECT COUNT(*) as count FROM employers");
    $totalCompanies = $companiesQuery->fetch_assoc()['count'];

    // External jobs (jobs with external_link)
    $externalQuery = $conn->query("SELECT COUNT(*) as count FROM jobs WHERE external_link IS NOT NULL AND external_link != ''");
    $externalJobs = $externalQuery->fetch_assoc()['count'];

    echo json_encode([
        'success' => true,
        'stats' => [
            'total_jobs' => $totalJobs,
            'today_imports' => $todayImports,
            'total_companies' => $totalCompanies,
            'external_jobs' => $externalJobs
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
