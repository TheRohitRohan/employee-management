<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Check if it's an AJAX request
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

// Database connection
require_once '../includes/db.php';
$database = new Database();
$conn = $database->getConnection();

$action = $_POST['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

switch ($action) {
    case 'add':
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $response = ['success' => false, 'message' => 'Invalid request'];
            break;
        }

        // Get and sanitize input
        $first_name = sanitize($_POST['first_name'] ?? '');
        $last_name = sanitize($_POST['last_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $department = sanitize($_POST['department'] ?? '');
        $job_title = sanitize($_POST['job_title'] ?? '');
        $hire_date = sanitize($_POST['hire_date'] ?? '');
        $salary = sanitize($_POST['salary'] ?? '');
        $status = sanitize($_POST['status'] ?? 'active');

        // Validate input
        if (empty($first_name) || empty($last_name) || empty($email) || empty($phone) || 
            empty($department) || empty($job_title) || empty($hire_date) || empty($salary)) {
            $response = ['success' => false, 'message' => 'Please fill in all required fields'];
            break;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response = ['success' => false, 'message' => 'Invalid email format'];
            break;
        }

        try {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM employees WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $response = ['success' => false, 'message' => 'Email already exists'];
                break;
            }

            // Insert new employee
            $stmt = $conn->prepare("
                INSERT INTO employees (first_name, last_name, email, phone, department, 
                                     job_title, hire_date, salary, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            $stmt->execute([
                $first_name, $last_name, $email, $phone, $department,
                $job_title, $hire_date, $salary, $status
            ]);
            
            $response = [
                'success' => true, 
                'message' => 'Employee added successfully',
                'employee' => [
                    'id' => $conn->lastInsertId(),
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'phone' => $phone,
                    'department' => $department,
                    'job_title' => $job_title,
                    'hire_date' => $hire_date,
                    'salary' => $salary,
                    'status' => $status
                ]
            ];
        } catch (PDOException $e) {
            $response = ['success' => false, 'message' => 'Error adding employee: ' . $e->getMessage()];
        }
        break;

    case 'edit':
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $response = ['success' => false, 'message' => 'Invalid request'];
            break;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $response = ['success' => false, 'message' => 'Invalid employee ID'];
            break;
        }

        // Get and sanitize input
        $first_name = sanitize($_POST['first_name'] ?? '');
        $last_name = sanitize($_POST['last_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $department = sanitize($_POST['department'] ?? '');
        $job_title = sanitize($_POST['job_title'] ?? '');
        $hire_date = sanitize($_POST['hire_date'] ?? '');
        $salary = sanitize($_POST['salary'] ?? '');
        $status = sanitize($_POST['status'] ?? 'active');

        // Validate input
        if (empty($first_name) || empty($last_name) || empty($email) || empty($phone) || 
            empty($department) || empty($job_title) || empty($hire_date) || empty($salary)) {
            $response = ['success' => false, 'message' => 'Please fill in all required fields'];
            break;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response = ['success' => false, 'message' => 'Invalid email format'];
            break;
        }

        try {
            // Check if email already exists for other employees
            $stmt = $conn->prepare("SELECT id FROM employees WHERE email = ? AND id != ?");
            $stmt->execute([$email, $id]);
            if ($stmt->rowCount() > 0) {
                $response = ['success' => false, 'message' => 'Email already exists'];
                break;
            }

            // Update employee
            $stmt = $conn->prepare("
                UPDATE employees 
                SET first_name = ?, last_name = ?, email = ?, phone = ?, 
                    department = ?, job_title = ?, hire_date = ?, 
                    salary = ?, status = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $first_name, $last_name, $email, $phone, $department,
                $job_title, $hire_date, $salary, $status, $id
            ]);

            $response = [
                'success' => true, 
                'message' => 'Employee updated successfully',
                'employee' => [
                    'id' => $id,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'phone' => $phone,
                    'department' => $department,
                    'job_title' => $job_title,
                    'hire_date' => $hire_date,
                    'salary' => $salary,
                    'status' => $status
                ]
            ];
        } catch (PDOException $e) {
            $response = ['success' => false, 'message' => 'Error updating employee: ' . $e->getMessage()];
        }
        break;

    case 'delete':
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            $response = ['success' => false, 'message' => 'Invalid request'];
            break;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $response = ['success' => false, 'message' => 'Invalid employee ID'];
            break;
        }

        try {
            // Check if employee exists
            $stmt = $conn->prepare("SELECT id FROM employees WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($stmt->rowCount() === 0) {
                $response = ['success' => false, 'message' => 'Employee not found'];
                break;
            }
            
            // Delete employee
            $stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
            $stmt->execute([$id]);
            
            $response = ['success' => true, 'message' => 'Employee deleted successfully'];
        } catch (PDOException $e) {
            $response = ['success' => false, 'message' => 'Error deleting employee: ' . $e->getMessage()];
        }
        break;

    case 'search':
        $search = sanitize($_POST['search'] ?? '');
        $department = sanitize($_POST['department'] ?? '');
        $status = sanitize($_POST['status'] ?? '');
        $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
        $records_per_page = 10;
        $offset = ($page - 1) * $records_per_page;

        // Build query
        $query = "SELECT * FROM employees WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $query .= " AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $search_param = "%$search%";
            $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
        }

        if (!empty($department)) {
            $query .= " AND department = ?";
            $params[] = $department;
        }

        if (!empty($status)) {
            $query .= " AND status = ?";
            $params[] = $status;
        }

        try {
            // Get total records for pagination
            $stmt = $conn->prepare(str_replace('*', 'COUNT(*) as count', $query));
            $stmt->execute($params);
            $total_records = $stmt->fetch()['count'];
            $total_pages = ceil($total_records / $records_per_page);

            // Get employees with pagination
            $query .= " ORDER BY id DESC LIMIT $records_per_page OFFSET $offset";
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $response = [
                'success' => true,
                'employees' => $employees,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $total_pages,
                    'total_records' => $total_records
                ]
            ];
        } catch (PDOException $e) {
            $response = ['success' => false, 'message' => 'Error searching employees: ' . $e->getMessage()];
        }
        break;
}

header('Content-Type: application/json');
echo json_encode($response); 