<?php
// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', '0'); // Don't show errors to users
ini_set('log_errors', '1');

$logDirectory = dirname(__DIR__) . '/logs';
if (!is_dir($logDirectory)) {
    mkdir($logDirectory, 0775, true);
}
ini_set('error_log', $logDirectory . '/error.log');

// Define application constants
define('APP_ROOT', dirname(__DIR__));
define('VIEW_PATH', APP_ROOT.'/app/views/');

require_once APP_ROOT . '/config/database.php';
require_once APP_ROOT . '/app/core/helpers.php';

// Register autoloader
require_once APP_ROOT . '/app/core/Autoloader.php';
$autoloader = new Autoloader(APP_ROOT);
$autoloader->register();

try {
    // Initialize session
    if (session_status() === PHP_SESSION_NONE) {
        $isHttps = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        session_start([
            'cookie_secure' => $isHttps,
            'cookie_httponly' => true,
            'use_strict_mode' => true,
            'cookie_samesite' => 'Lax',
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
