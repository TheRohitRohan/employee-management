<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get employee ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    redirect('employees.php');
}

// Database connection
require_once 'includes/db.php';
$database = new Database();
$conn = $database->getConnection();

try {
    // Check if employee exists
    $stmt = $conn->prepare("SELECT id FROM employees WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() === 0) {
        $_SESSION['error'] = 'Employee not found';
        redirect('employees.php');
    }
    
    // Delete employee
    $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
    $stmt->execute([$id]);
    
    $_SESSION['success'] = 'Employee deleted successfully';
} catch (PDOException $e) {
    $_SESSION['error'] = 'Error deleting employee: ' . $e->getMessage();
}

redirect('employees.php'); 