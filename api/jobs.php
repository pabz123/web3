<?php
// Path: api/jobs.php
// Handles GET (all or single by ?id=) and POST (create) for jobs.
// Example:
// - GET /api/jobs.php       -> returns all jobs
// - GET /api/jobs.php?id=5  -> returns job with id=5
// - POST /api/jobs.php      -> create job (requires auth)

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php'; // $conn (mysqli)
require_once __DIR__ . '/../includes/csrf.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    if ($id) {
    $stmt = $conn->prepare("SELECT id, title, company, description, created_at FROM jobs WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $job = $res->fetch_assoc();
    $stmt->close();
        if (!$job) { http_response_code(404); echo json_encode(['error' => 'Not found']); exit; }
        echo json_encode(['job' => $job]); exit;
    } else {
    $res = $conn->query("SELECT id, title, company, LEFT(description, 400) as description, created_at FROM jobs ORDER BY created_at DESC");
    $jobs = $res->fetch_all(MYSQLI_ASSOC);
        echo json_encode(['jobs' => $jobs]); exit;
    }
}

if ($method === 'POST') {
    // require auth
    if (empty($_SESSION['user'])) { http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit; }
    $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    if (!verify_csrf_token($token)) { http_response_code(403); echo json_encode(['error'=>'Invalid CSRF token']); exit; }

    $title = trim($_POST['title'] ?? '');
    $company = trim($_POST['company'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if (!$title || !$company || !$description) { http_response_code(400); echo json_encode(['error'=>'Missing fields']); exit; }

    $createdAt = (new DateTime('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s');
    $stmt = $conn->prepare("INSERT INTO jobs (title, company, description, created_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $title, $company, $description, $createdAt);
    $stmt->execute();
    $newId = $conn->insert_id;
    $stmt->close();
    echo json_encode(['success' => true, 'job_id' => $newId]); exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
exit;
