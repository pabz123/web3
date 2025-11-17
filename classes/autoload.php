<?php
/**
 * Autoloader for classes + .env loader
 * Include this file to automatically load all class files
 */

// Load Composer autoload (for phpdotenv and other vendor libs)
$vendorAutoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($vendorAutoload)) {
    require_once $vendorAutoload;
}

// Load .env if present (non-fatal if missing)
if (class_exists('Dotenv\\Dotenv')) {
    try {
        // createMutable allows .env to override existing environment vars (helpful on Windows)
        $dotenv = Dotenv\Dotenv::createMutable(__DIR__ . '/..');
        $dotenv->safeLoad(); // don't throw if .env not found
    } catch (Throwable $e) {
        error_log('Dotenv load warning: ' . $e->getMessage());
    }
} else {
    // Fallback: minimal .env parser if phpdotenv isn't installed
    $envPath = __DIR__ . '/../.env';
    if (file_exists($envPath) && is_readable($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $trim = trim($line);
            if ($trim === '' || $trim[0] === '#' || str_starts_with($trim, ';')) { continue; }
            $pos = strpos($trim, '=');
            if ($pos === false) { continue; }
            $key = rtrim(substr($trim, 0, $pos));
            $val = ltrim(substr($trim, $pos + 1));
            // Strip quotes if present
            if ((str_starts_with($val, '"') && str_ends_with($val, '"')) || (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
                $val = substr($val, 1, -1);
            }
            if ($key !== '') {
                // Set in all common places
                putenv($key . '=' . $val);
                $_ENV[$key] = $val;
                $_SERVER[$key] = $_SERVER[$key] ?? $val;
            }
        }
    }
}

spl_autoload_register(function ($className) {
    $classFile = __DIR__ . '/' . $className . '.php';
    if (file_exists($classFile)) {
        require_once $classFile;
    }
});

// Ensure mysqli connection available
if (!isset($GLOBALS['conn'])) {
    require_once __DIR__ . '/../includes/db.php';
}
