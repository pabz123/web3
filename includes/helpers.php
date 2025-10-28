<?php
// includes/helpers.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getUserName(): string {
    $name = $_SESSION['user']['name'] ?? 'Student';
    return htmlspecialchars((string)$name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function isLoggedIn(): bool {
    return isset($_SESSION['user']);
}

function getUserEmail(): string {
    return htmlspecialchars((string)($_SESSION['user']['email'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
?>
