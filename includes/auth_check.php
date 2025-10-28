<?php
// includes/auth_check.php

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    // redirect to login page if not authenticated
    header("Location: ../login.php");
    exit;
}

// Define $currentUser safely for pages that include this file
$currentUser = $_SESSION['user'];

// Optionally ensure name and email exist to avoid undefined warnings
if (!isset($currentUser['name'])) {
    $currentUser['name'] = 'Student';
}
if (!isset($currentUser['email'])) {
    $currentUser['email'] = '';
}
