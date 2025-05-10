<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    redirect('login.php');
}

// Get user ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    redirect('users.php');
}

// Prevent self-deletion
if ($id === $_SESSION['user_id']) {
    $_SESSION['error'] = 'You cannot delete your own account';
    redirect('users.php');
}

// Database connection
require_once 'includes/db.php';
$database = new Database();
$conn = $database->getConnection();

try {
    // Check if user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() === 0) {
        $_SESSION['error'] = 'User not found';
        redirect('users.php');
    }
    
    // Delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    
    $_SESSION['success'] = 'User deleted successfully';
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error deleting user: ' . $e->getMessage();
}

redirect('users.php'); 