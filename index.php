<?php
/**
 * Root Entry Point & Built-In Server Router
 * Serves static assets from public/ and routes all application requests
 */
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

// Serve static assets directly if running under PHP built-in CLI web server
$publicFile = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($publicFile) && !is_dir($publicFile)) {
    return false;
}

require_once __DIR__ . '/public/index.php';
