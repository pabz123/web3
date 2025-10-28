<?php
// Path: includes/csrf.php
// Minimal CSRF token helpers for forms and AJAX.

require_once __DIR__ . '/session.php';

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token(?string $token): bool {
    if (empty($_SESSION['csrf_token'])) return false;
    return hash_equals($_SESSION['csrf_token'], (string)$token);
}

// Usage:
// - in forms include <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
//- in AJAX send header 'X-CSRF-Token' or POST field 'csrf_token'
