<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'employee_management');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application configuration
define('SITE_NAME', 'Employee Management System');
define('SITE_URL', 'http://localhost/Task1');

// CSRF token configuration
define('CSRF_TOKEN_SECRET', 'your-secret-key-here');
?> 