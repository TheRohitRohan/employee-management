<?php
// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'employee_management');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application configuration
define('BASE_URL', 'http://localhost/Task1');
define('APP_NAME', 'Employee Management System');

// CSRF token configuration
define('CSRF_TOKEN_SECRET', 'your-secret-key-here');

// Session configuration - must be set before session starts
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?> 