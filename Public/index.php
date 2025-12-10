<?php
/**
 * SISWA IZIN SYSTEM - Main Router
 * 
 * Entry point untuk semua request aplikasi.
 * Mengelola routing, controller loading, dan error handling.
 * 
 * USAGE:
 *   - index.php?action=auth.login          -> AuthController::login()
 *   - index.php?action=siswa.dashboard     -> SiswaController::dashboard()
 *   - index.php?action=admin.index         -> AdminController::index()
 * 
 * @author Development Team
 * @version 1.0.0
 * @since 2025-12
 */

// ============================================================
// BOOTSTRAP & INITIALIZATION
// ============================================================

// Load configuration & bootstrap
require_once __DIR__ . '/../App/Config/bootstrap.php';
require_once __DIR__ . '/../App/Config/Database.php';
require_once __DIR__ . '/../App/Config/config.php';
require_once __DIR__ . '/../App/Middleware/Auth.php';
require_once __DIR__ . '/../App/Helpers/Validator.php';

// ============================================================
// REQUEST PARSING
// ============================================================

/**
 * Get action dari query string
 * Format: action=controller.method
 * Example: action=siswa.dashboard
 * Default: login page
 */
$action = $_GET['action'] ?? 'auth.login';

// If no action specified and not a static file, redirect to login
if (empty($_GET['action']) && !preg_match('/\.(css|js|jpg|jpeg|png|gif|ico|woff|woff2|ttf)$/i', $_SERVER['REQUEST_URI'])) {
    $action = 'auth.login';
}

// Validate action format (alphanumeric + dot only)
if (!preg_match('/^[a-zA-Z0-9._]+$/', $action)) {
    http_response_code(400);
    die('Invalid action format');
}

// Parse action to controller and method
$parts = explode('.', $action, 2);
if (count($parts) !== 2) {
    http_response_code(400);
    die('Invalid action format. Use: action=controller.method');
}

list($controller, $method) = $parts;

// Normalize controller (e.g. 'siswa' -> 'Siswa'), but keep method casing
$controller = ucfirst(strtolower($controller));

// Validate controller & method names (allow camelCase for methods)
if (!preg_match('/^[A-Za-z0-9_]+$/', $controller) || !preg_match('/^[A-Za-z0-9_]+$/', $method)) {
    http_response_code(400);
    die('Invalid controller or method name');
}

// ============================================================
// CONTROLLER LOADING & EXECUTION
// ============================================================

try {
    // Construct controller file path
    $controllerPath = __DIR__ . '/../App/Controllers/' . $controller . 'Controller.php';

    // Check file exists
    if (!file_exists($controllerPath)) {
        http_response_code(404);
        die('Controller not found: ' . htmlspecialchars($controller));
    }

    // Load controller
    require_once $controllerPath;
    $controllerClass = $controller . 'Controller';

    // Check class exists
    if (!class_exists($controllerClass)) {
        http_response_code(500);
        die('Controller class not found: ' . htmlspecialchars($controllerClass));
    }

    // Instantiate controller
    $controller = new $controllerClass();

    // Check method exists
    if (!method_exists($controller, $method)) {
        http_response_code(404);
        die('Method not found: ' . htmlspecialchars($method));
    }

    // Call controller method
    $controller->$method();

} catch (Exception $e) {
    // Log error
    error_log('Router Error: ' . $e->getMessage());

    // Return error response
    http_response_code(500);
    
    // In production, show generic error
    if (APP_ENV !== 'development') {
        die('An error occurred. Please try again later.');
    }
    
    // In development, show details
    die('Error: ' . htmlspecialchars($e->getMessage()));
}
