<?php
require_once 'config.php';

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Generate CSRF token
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Display message
function displayMessage() {
    $html = '';
    if (isset($_SESSION['success'])) {
        $html .= '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        $html .= '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
        unset($_SESSION['error']);
    }
    return $html;
}

// Redirect
function redirect($url) {
    header("Location: $url");
    exit;
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Format date
function formatDate($date) {
    return date('Y-m-d', strtotime($date));
}

// Format currency
function formatCurrency($amount) {
    return number_format($amount, 2);
}

// Redirect with message
function redirectWithMessage($url, $message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header("Location: $url");
    exit;
}

// Function to sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate phone number
function isValidPhone($phone) {
    return preg_match('/^[0-9]{10}$/', $phone);
}

// Function to get department options
function getDepartmentOptions($selected = '') {
    $departments = ['IT', 'HR', 'Finance', 'Marketing', 'Operations'];
    $options = '';
    foreach ($departments as $dept) {
        $selected_attr = ($selected === $dept) ? 'selected' : '';
        $options .= "<option value=\"$dept\" $selected_attr>$dept</option>";
    }
    return $options;
}

// Function to get status options
function getStatusOptions($selected = '') {
    $statuses = ['active', 'inactive'];
    $options = '';
    foreach ($statuses as $status) {
        $selected_attr = ($selected === $status) ? 'selected' : '';
        $options .= "<option value=\"$status\" $selected_attr>$status</option>";
    }
    return $options;
}
?> 