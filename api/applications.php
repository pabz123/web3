<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php'; // defines $conn (MySQLi)

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    if (empty($_SESSION['user'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $studentId   = (int) $_SESSION['user']['id'];
    $jobId       = isset($_POST['jobId']) ? (int) $_POST['jobId'] : 0;
    $coverLetter = isset($_POST['coverLetter']) ? trim($_POST['coverLetter']) : '';

    if ($jobId === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing jobId']);
        exit;
    }

    $stmt = $conn->prepare("
        INSERT INTO applications (status, coverLetter, createdAt, updatedAt, studentId, jobId)
        VALUES ('Applied', ?, NOW(), NOW(), ?, ?)
    ");

    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['error' => 'Database prepare failed: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param("sii", $coverLetter, $studentId, $jobId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'application_id' => $stmt->insert_id]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save application: ' . $stmt->error]);
    }

    $stmt->close();
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
exit;
