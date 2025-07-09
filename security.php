<?php
/**
 * Security Configuration File
 * This file should be included on every page to ensure consistent security settings
 */

// Prevent direct access
if (!defined('SECURITY_INCLUDED')) {
    define('SECURITY_INCLUDED', true);
}

/**
 * Configure session settings before starting the session
 * This function must be called before session_start()
 */
function configure_session_settings() {
    // Only configure if session is not already active
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS
        ini_set('session.cookie_samesite', 'Strict');
        ini_set('session.use_strict_mode', 1);
        ini_set('session.entropy_file', '/dev/urandom');
        ini_set('session.entropy_length', 32);
        ini_set('session.hash_function', 'sha256');
    }
}

// Only set headers if they haven't been sent yet
if (!headers_sent()) {
    // Security Headers
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // For HTTPS sites, uncomment the following line:
    // header('Strict-Transport-Security: max-age=31536000; includeSubDomains');

    // Content Security Policy - Comprehensive policy for Bootstrap, jQuery, DataTables, Select2, and Font Awesome
    $csp = "default-src 'self'; " .
           "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://code.jquery.com https://cdn.datatables.net https://ajax.googleapis.com https://cdnjs.cloudflare.com; " .
           "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdn.datatables.net https://fonts.googleapis.com https://cdnjs.cloudflare.com; " .
           "font-src 'self' https://cdn.jsdelivr.net https://fonts.gstatic.com https://cdnjs.cloudflare.com; " .
           "img-src 'self' data: https: http:; " .
           "connect-src 'self' https://cdn.datatables.net https://cdn.datatables.net/plug-ins/; " .
           "frame-src 'none'; " .
           "object-src 'none'; " .
           "base-uri 'self';";
    header("Content-Security-Policy: $csp");
}

// Error reporting (disable in production)
if (defined('APP_ENV') && APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// File upload security
ini_set('file_uploads', 1);
ini_set('upload_max_filesize', '10M');
ini_set('max_file_uploads', 5);

// Other security settings
ini_set('expose_php', 0);
ini_set('allow_url_fopen', 0);
ini_set('allow_url_include', 0);

?>
