<?php
// Path: api/auth/login.php
// Handles POST login: expects email and password. Returns JSON with user profile.
// Optional "remember me" implementation is included but commented/configurable.

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Optional CSRF check
// $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
// if (!verify_csrf_token($token)) { http_response_code(403); echo json_encode(['error'=>'Invalid CSRF']); exit; }

$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
$remember = !empty($_POST['remember']) ? true : false;

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing credentials']);
    exit;
}

$stmt = $pdo->prepare("SELECT id, name, email, password_hash, profile_image, role, theme, phone FROM users WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid email or password']);
    exit;
}

// Successful login: regenerate session id for security
session_regenerate_id(true);

// Store user data in session for persistent access across pages
$_SESSION['user'] = [
    'id' => (int)$user['id'],
    'name' => $user['name'] ?? $user['email'],
    'email' => $user['email'],
    'phone' => $user['phone'] ?? '',
    'profile_image' => $user['profile_image'] ?? '/uploads/profile/default-avatar.png',
    'role' => $user['role'] ?? 'student',
    'theme' => $user['theme'] ?? 'dark',
];

// Optional: Remember-me implementation (secure cookie + token stored in DB)
// 1) Create columns in users table: remember_token, remember_expires_at (we include remember_token in migration).
// 2) If $remember is true, generate a secure token, store hashed token in DB and set cookie with token id.
// Example implementation (UNCOMMENT to enable):
/*
if ($remember) {
    $rawToken = bin2hex(random_bytes(32)); // secure random token
    $tokenHash = password_hash($rawToken, PASSWORD_DEFAULT); // store hashed token
    $expires = (new DateTime('+30 days', new DateTimeZone('UTC')))->format('Y-m-d H:i:s');
    $update = $pdo->prepare("UPDATE users SET remember_token = :token, remember_expires_at = :expires WHERE id = :id");
    $update->execute([':token' => $tokenHash, ':expires' => $expires, ':id' => $user['id']]);
    setcookie('remember_me', $user['id'] . ':' . $rawToken, [
        'expires' => time() + 60*60*24*30,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    ]);
}
*/

echo json_encode(['success' => true, 'user' => $_SESSION['user']]);
exit;
