<?php
// Path: api/student_profile.php
// POST: update profile (requires login). Accepts name, optional profile_image upload.
// GET: ?email= returns profile info by email.

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/upload_helpers.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    if (empty($_SESSION['user'])) { http_response_code(401); echo json_encode(['error'=>'Unauthorized']); exit; }

    $userId = (int)$_SESSION['user']['id'];
    $name = trim($_POST['name'] ?? '');

    if (!$name) { http_response_code(400); echo json_encode(['error'=>'Name required']); exit; }

    $profileImageFilename = $_SESSION['user']['profile_image'] ?? null;
    if (!empty($_FILES['profile_image']) && $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $stored = store_profile_image($_FILES['profile_image']);
        if ($stored === null) { http_response_code(400); echo json_encode(['error'=>'Invalid image']); exit; }
        $profileImageFilename = $stored;
    }

    $stmt = $pdo->prepare("UPDATE users SET name = :name, profile_image = :profile_image WHERE id = :id");
    $stmt->execute([':name' => $name, ':profile_image' => $profileImageFilename, ':id' => $userId]);

    // update session copy
    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['profile_image'] = $profileImageFilename;

    echo json_encode(['success' => true]);
    exit;
}

if ($method === 'GET') {
    $email = strtolower(trim($_GET['email'] ?? ''));
    if (!$email) { http_response_code(400); echo json_encode(['error'=>'email required']); exit; }

    $stmt = $pdo->prepare("SELECT id, name, email, profile_image, role, created_at FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
    if (!$user) { http_response_code(404); echo json_encode(['error'=>'Not found']); exit; }
    echo json_encode(['user' => $user]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
exit;
