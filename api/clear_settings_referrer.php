<?php
// api/clear_settings_referrer.php
session_start();

// Clear the settings referrer from session
if (isset($_SESSION['settings_referrer'])) {
    unset($_SESSION['settings_referrer']);
}

http_response_code(200);
echo json_encode(['success' => true]);
