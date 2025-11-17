<?php
// includes/auth_check.php

// Start session if not already started (with timeout handling)
require_once __DIR__ . '/session.php';



// Check if user is logged in
if (!isset($_SESSION['user'])) {
    // Store the requested page to redirect after login
    $requested_page = $_SERVER['REQUEST_URI'];
    $_SESSION['redirect_after_login'] = $requested_page;
    
    // redirect to login page if not authenticated
    header("Location: /career_hub/pages/login.php");
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
