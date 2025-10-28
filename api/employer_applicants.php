<?php
// Path: api/employer_applicants.php
// GET ?employerId= : returns applicants for an employer's jobs (requires admin or employer role).
// GET ?applicationId= : returns specific application.

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';

if (empty($_SESSION['user'])) { http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit; }

$role = $_SESSION['user']['role'] ?? 'user';

// For simplicity we allow employer or admin to view
if (!in_array($role, ['employer', 'admin'])) { http_response_code(403); echo json_encode(['error'=>'Forbidden']); exit; }

if (!empty($_GET['employerId'])) {
    $employerId = (int)$_GET['employerId'];
    $stmt = $pdo->prepare("
        SELECT a.id as application_id, a.job_id, j.title, a.user_id, u.name as applicant_name, a.cover_letter, a.created_at
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        JOIN users u ON a.user_id = u.id
        WHERE j.employer_id = :emp
        ORDER BY a.created_at DESC
    ");
    $stmt->execute([':emp' => $employerId]);
    echo json_encode(['applicants' => $stmt->fetchAll()]);
    exit;
}

if (!empty($_GET['applicationId'])) {
    $applicationId = (int)$_GET['applicationId'];
    $stmt = $pdo->prepare("SELECT a.*, u.name as applicant_name, j.title FROM applications a JOIN users u ON a.user_id = u.id JOIN jobs j ON a.job_id = j.id WHERE a.id = :id LIMIT 1");
    $stmt->execute([':id' => $applicationId]);
    $a = $stmt->fetch();
    if (!$a) { http_response_code(404); echo json_encode(['error'=>'Not found']); exit; }
    echo json_encode(['application' => $a]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Bad request']);
exit;
