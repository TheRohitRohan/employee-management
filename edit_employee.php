<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/db.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';
$employee = null;

// Get employee ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: employees.php');
    exit;
}

// Get employee data
$database = new Database();
$conn = $database->getConnection();

$stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->execute([$id]);
$employee = $stmt->fetch();

if (!$employee) {
    header('Location: employees.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request';
    } else {
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
            $error = 'Please fill in all required fields';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email format';
        } else {
            try {
                // Check if email already exists for other employees
                $stmt = $conn->prepare("SELECT id FROM employees WHERE email = ? AND id != ?");
                $stmt->execute([$email, $id]);
                if ($stmt->rowCount() > 0) {
                    $error = 'Email already exists';
                } else {
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

                    $success = 'Employee updated successfully';
                    
                    // Refresh employee data
                    $stmt = $conn->prepare("SELECT * FROM employees WHERE id = ?");
                    $stmt->execute([$id]);
                    $employee = $stmt->fetch();
                }
            } catch (PDOException $e) {
                $error = 'Error updating employee: ' . $e->getMessage();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee - Employee Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Edit Employee</h1>
                    <a href="employees.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Employees
                    </a>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <form id="editEmployeeForm" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                            <input type="hidden" name="id" value="<?php echo $employee['id']; ?>">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">First Name *</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="<?php echo htmlspecialchars($employee['first_name']); ?>" required>
                                    <div class="invalid-feedback">Please enter first name</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Last Name *</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="<?php echo htmlspecialchars($employee['last_name']); ?>" required>
                                    <div class="invalid-feedback">Please enter last name</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($employee['email']); ?>" required>
                                    <div class="invalid-feedback">Please enter a valid email address</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone *</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo htmlspecialchars($employee['phone']); ?>" required>
                                    <div class="invalid-feedback">Please enter phone number</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="department" class="form-label">Department *</label>
                                    <select class="form-select" id="department" name="department" required>
                                        <option value="">Select Department</option>
                                        <option value="IT" <?php echo $employee['department'] === 'IT' ? 'selected' : ''; ?>>IT</option>
                                        <option value="HR" <?php echo $employee['department'] === 'HR' ? 'selected' : ''; ?>>HR</option>
                                        <option value="Finance" <?php echo $employee['department'] === 'Finance' ? 'selected' : ''; ?>>Finance</option>
                                        <option value="Marketing" <?php echo $employee['department'] === 'Marketing' ? 'selected' : ''; ?>>Marketing</option>
                                        <option value="Sales" <?php echo $employee['department'] === 'Sales' ? 'selected' : ''; ?>>Sales</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a department</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="job_title" class="form-label">Job Title *</label>
                                    <input type="text" class="form-control" id="job_title" name="job_title" 
                                           value="<?php echo htmlspecialchars($employee['job_title']); ?>" required>
                                    <div class="invalid-feedback">Please enter job title</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="hire_date" class="form-label">Hire Date *</label>
                                    <input type="date" class="form-control" id="hire_date" name="hire_date" 
                                           value="<?php echo htmlspecialchars($employee['hire_date']); ?>" required>
                                    <div class="invalid-feedback">Please select hire date</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="salary" class="form-label">Salary *</label>
                                    <input type="number" class="form-control" id="salary" name="salary" 
                                           value="<?php echo htmlspecialchars($employee['salary']); ?>" required>
                                    <div class="invalid-feedback">Please enter salary</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" <?php echo $employee['status'] === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo $employee['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update Employee
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="assets/js/employee.js"></script>
</body>
</html> 