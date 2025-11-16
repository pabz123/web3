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

// Session timeout - 30 minutes of inactivity
define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds

// Check session timeout
if (isset($_SESSION['LAST_ACTIVITY'])) {
    $inactive = time() - $_SESSION['LAST_ACTIVITY'];
    
    if ($inactive > SESSION_TIMEOUT) {
        // Session expired - destroy and redirect to login
        session_unset();
        session_destroy();
        
        // Only redirect if not already on login/index/signup pages
        $current_page = basename($_SERVER['PHP_SELF']);
        $public_pages = ['index.php', 'login.php', 'signup.php', 'admin-login.php'];
        
        if (!in_array($current_page, $public_pages)) {
            header("Location: /career_hub/pages/login.php?timeout=1");
            exit;
        }
    }
}

// Update last activity timestamp
$_SESSION['LAST_ACTIVITY'] = time();

// Optional helper: get current user quickly
function getCurrentUserFromSession(): ?array {
    return $_SESSION['user'] ?? null;
}
