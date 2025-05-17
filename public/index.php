<?php
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', '0'); // Don't show errors to users
ini_set('log_errors', '1');
ini_set('error_log', __DIR__.'/../logs/error.log');

// Define application constants
define('APP_ROOT', dirname(__DIR__));
define('VIEW_PATH', APP_ROOT.'/app/views/');

// Register autoloader
spl_autoload_register(function ($className) {
    $file = APP_ROOT.'/'.str_replace('\\', '/', $className).'.php';
    if (file_exists($file)) {
        require $file;
    }
});

try {
    // Initialize session
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_secure' => true,
            'cookie_httponly' => true,
            'use_strict_mode' => true
        ]);
    }

    // Generate CSRF token if not exists
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    // Initialize and run router
    require_once APP_ROOT.'/app/core/Router.php';
    $router = new Router();
    $router->route();

} catch (Throwable $e) {
    // Log the error
    error_log("Uncaught exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());

    // Show error page
    http_response_code(500);
    if (file_exists(VIEW_PATH.'errors/500.php')) {
        require VIEW_PATH.'errors/500.php';
    } else {
        echo "<h1>500 Internal Server Error</h1>";
        echo "<p>An unexpected error occurred. Please try again later.</p>";
    }
    exit;
}