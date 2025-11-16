<?php
/**
 * Single mysqli connection file.
 * Original hard-coded development/InfinityFree credentials are preserved below as comments per request.
 * Now supports environment variables for production without exposing secrets in source control.
 * Env vars: DB_HOST, DB_USER, DB_PASS, DB_NAME
 */

// === ORIGINAL REFERENCE (DO NOT DELETE) ===
// $servername = "localhost"; 
// //$servername = "sql113.infinityfree.com";
// $username = "root";
// //$username = "if0_40185804"; // your InfinityFree username
// $password = ""; 
// //$password = "careerhub12"; // the password from your InfinityFree control panel
// $dbname = "uniconnect_db"; 
// //$dbname = "if0_40185804_uniconnect_db";// your full database name
// ==========================================

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Try environment variables first
$servername = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? '');
$username   = getenv('DB_USER') ?: ($_ENV['DB_USER'] ?? $_SERVER['DB_USER'] ?? '');
$password   = getenv('DB_PASS') ?: ($_ENV['DB_PASS'] ?? $_SERVER['DB_PASS'] ?? '');
$dbname     = getenv('DB_NAME') ?: ($_ENV['DB_NAME'] ?? $_SERVER['DB_NAME'] ?? '');

// If env vars are empty, try loading from config file
if (empty($servername) || empty($dbname)) {
    $configPath = __DIR__ . '/../config/config.php';
    if (file_exists($configPath)) {
        $config = require $configPath;
        
        // Determine which environment we're in
        $isLocal = (isset($_SERVER['SERVER_NAME']) && 
                   (strpos($_SERVER['SERVER_NAME'], 'localhost') !== false || 
                    strpos($_SERVER['SERVER_NAME'], '127.0.0.1') !== false));
        
        if ($isLocal) {
            // Use local database config
            $servername = $config['DB_HOST_LOCAL'] ?? 'localhost';
            $username   = $config['DB_USER_LOCAL'] ?? 'root';
            $password   = $config['DB_PASS_LOCAL'] ?? '';
            $dbname     = $config['DB_NAME_LOCAL'] ?? 'uniconnect_db';
        } else {
            // Use InfinityFree database config
            $servername = $config['DB_HOST_INFINITYFREE'] ?? '';
            $username   = $config['DB_USER_INFINITYFREE'] ?? '';
            $password   = $config['DB_PASS_INFINITYFREE'] ?? '';
            $dbname     = $config['DB_NAME_INFINITYFREE'] ?? '';
        }
    }
}

// Final fallback to local defaults
if (empty($servername)) $servername = 'localhost';
if (empty($username)) $username = 'root';
if (empty($password)) $password = '';
if (empty($dbname)) $dbname = 'uniconnect_db';

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('DB connection error: ' . $e->getMessage());
    http_response_code(500);
    die('Database unavailable');
}
?>