<?php
/**
 * Helper Functions
 * 
 * Common utility functions used throughout the application
 */

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current logged-in user ID
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current logged-in user role
 */
function getCurrentUserRole() {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Check if user has specific role
 */
function hasRole($role) {
    return getCurrentUserRole() === $role;
}

/**
 * Check if user has any of the specified roles
 */
function hasAnyRole($roles) {
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    return in_array(getCurrentUserRole(), $roles);
}

/**
 * Get base URL path for the application
 */
function baseUrl() {
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    $dir = dirname($scriptName);
    return ($dir === '/' || $dir === '\\') ? '' : $dir;
}

/**
 * URL Generator Helper
 */
function url($path = '') {
    $path = ltrim($path, '/');
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
    return $scriptName . "?url=" . $path;
}

/**
 * Asset URL Generator Helper
 */
function asset($path = '') {
    $path = ltrim($path, '/');
    $base = baseUrl();
    return $base . '/' . $path;
}

/**
 * Require user to be logged in (redirects if not)
 */
function requireAuth($redirectUrl = 'auth/login') {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        redirect($redirectUrl);
    }
}

/**
 * Require user to have specific role
 */
function requireRole($role, $redirectUrl = 'events') {
    requireAuth();
    
    if (!hasRole($role)) {
        $_SESSION['error_message'] = 'You do not have permission to access this page.';
        redirect($redirectUrl);
    }
}

/**
 * Require user to have any of the specified roles
 */
function requireAnyRole($roles, $redirectUrl = 'events') {
    requireAuth();
    
    if (!hasAnyRole($roles)) {
        $_SESSION['error_message'] = 'You do not have permission to access this page.';
        redirect($redirectUrl);
    }
}

/**
 * Validate CSRF token
 */
function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string) $token);
}

/**
 * Require valid CSRF token
 */
function requireCsrf() {
    $token = $_POST['csrf_token'] ?? '';
    
    if (!validateCsrfToken($token)) {
        $_SESSION['error_message'] = 'Invalid CSRF token';
        redirect('events');
    }
}

/**
 * Sanitize output for display
 */
function e($string) {
    return htmlspecialchars((string) $string, ENT_QUOTES, 'UTF-8');
}

/**
 * Format price
 */
function formatPrice($amount) {
    return '$' . number_format((float) $amount, 2);
}

/**
 * Format date
 */
function formatDate($date, $format = 'F j, Y g:i a') {
    $timestamp = strtotime($date);
    return $timestamp ? date($format, $timestamp) : $date;
}

/**
 * Generate random string
 */
function generateRandomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Generate password reset token
 */
function generateResetToken() {
    return generateRandomString(64);
}

/**
 * Redirect helper
 */
function redirect($routePath) {
    header("Location: " . url($routePath));
    exit();
}

/**
 * Set flash message
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Send email (basic implementation)
 */
function sendEmail($to, $subject, $body, $isHtml = false) {
    $headers = "From: " . SMTP_FROM_NAME . " <" . SMTP_FROM_EMAIL . ">\r\n";
    $headers .= "Reply-To: " . SMTP_FROM_EMAIL . "\r\n";
    
    if ($isHtml) {
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    }
    
    return mail($to, $subject, $body, $headers);
}

/**
 * Check if request is AJAX
 */
function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

/**
 * Return JSON response
 */
function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

/**
 * Pagination helper
 */
function paginate($total, $perPage, $currentPage) {
    $totalPages = max(1, (int) ceil($total / $perPage));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}
