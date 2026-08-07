<?php
/**
 * Database Configuration
 * 
 * Environment-specific database settings
 * You can override these with environment variables or a .env file
 */

// Database configuration - Update these with your actual credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'eventpro');
define('DB_USER', 'root');
define('DB_PASS', 'toor'); // Change this in production!
define('DB_CHARSET', 'utf8mb4');

// Application settings
define('APP_NAME', 'EventPro');
define('APP_URL', 'http://localhost'); // Update with your domain
define('APP_EMAIL', 'noreply@eventpro.com');

// Stripe configuration (get from environment variables in production)
define('STRIPE_PUBLISHABLE_KEY', getenv('STRIPE_PUBLISHABLE_KEY') ?: '');
define('STRIPE_SECRET_KEY', getenv('STRIPE_SECRET_KEY') ?: '');

// Email configuration
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_USER', getenv('SMTP_USER') ?: '');
define('SMTP_PASS', getenv('SMTP_PASS') ?: '');
define('SMTP_FROM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: APP_EMAIL);
define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: APP_NAME);

// Session settings
define('SESSION_LIFETIME', 3600); // 1 hour in seconds

// Pagination
define('ITEMS_PER_PAGE', 10);

// Upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');

// Timezone
date_default_timezone_set('UTC');

// Error reporting (disable in production)
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', false);
}

if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}
