<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/db.php';

// Simple endpoint to save the logged-in user's theme preference
// POST body: { theme: 'light'|'dark' }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

if (empty($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$theme = $input['theme'] ?? null;
if (!in_array($theme, ['light', 'dark'], true)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid theme']);
    exit;
}

try {
    $stmt = $pdo->prepare('UPDATE users SET theme = :theme, updatedAt = NOW() WHERE id = :id');
    $stmt->execute([':theme' => $theme, ':id' => $_SESSION['user']['id']]);

    // Update session copy
    $_SESSION['user']['theme'] = $theme;

    echo json_encode(['success' => true, 'theme' => $theme]);
    exit;
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error', 'message' => $e->getMessage()]);
    exit;
}
?>