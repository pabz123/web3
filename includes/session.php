<?php
// Path: includes/session.php
// Centralized session settings. Include at top of any API endpoint before output.

if (session_status() === PHP_SESSION_NONE) {
    // Secure cookie params
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? true : false;
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params([
        'lifetime' => 0, // session cookie; change if you want persistent server-side sessions
        'path' => $cookieParams['path'] ?? '/',
        'domain' => $cookieParams['domain'] ?? '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax', // or 'Strict' if your app does not need third-party POSTs
    ]);

    session_start();
}

// Optional helper: get current user quickly
function getCurrentUserFromSession(): ?array {
    return $_SESSION['user'] ?? null;
}
