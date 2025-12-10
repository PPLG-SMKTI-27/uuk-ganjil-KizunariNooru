<?php
/**
 * Application bootstrap
 * - Start session
 * - Set constants
 * - Generate CSRF token
 * - Basic error reporting
 */

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'] ?? '',
        'secure' => $secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Application environment
if (!defined('APP_ENV')) {
    define('APP_ENV', getenv('APP_ENV') ?: 'development');
}

// Base URL (attempt to detect; allow override via env)
if (!defined('BASE_URL')) {
    $base = getenv('BASE_URL');
    if (!$base) {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        // For CLI, use simple localhost. For browser, calculate from SCRIPT_NAME
        if (php_sapi_name() === 'cli') {
            $base = 'http://localhost/';
        } else {
            $script = dirname($_SERVER['SCRIPT_NAME'] ?? '/');
            if ($script === '\\' || $script === '/') {
                $base = $scheme . '://' . $host . '/';
            } else {
                $base = $scheme . '://' . $host . str_replace('\\', '/', $script) . '/';
            }
        }
    }
    define('BASE_URL', $base);
}

// Error reporting
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
}

// CSRF token generation (if not set)
if (!isset($_SESSION['csrf_token']) || empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Simple autoload for Helpers if needed
spl_autoload_register(function ($class) {
    $helpersPath = __DIR__ . '/../Helpers/' . $class . '.php';
    if (file_exists($helpersPath)) {
        require_once $helpersPath;
    }
});

?>
