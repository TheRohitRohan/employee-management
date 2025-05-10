<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Get user information
$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Employee Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .sidebar .active {
            background-color: #007bff;
        }
        .main-content {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <h3 class="text-white text-center mb-4">EMS</h3>
                <nav>
                    <a href="dashboard.php" class="active">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <a href="employees.php">
                        <i class="bi bi-people"></i> Employees
                    </a>
                    <a href="add_employee.php">
                        <i class="bi bi-person-plus"></i> Add Employee
                    </a>
                    <?php if ($role === 'admin'): ?>
                    <a href="users.php">
                        <i class="bi bi-person-gear"></i> Users
                    </a>
                    <?php endif; ?>
                    <a href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Dashboard</h2>
                    <div>
                        Welcome, <?php echo htmlspecialchars($username); ?> 
                        (<?php echo ucfirst($role); ?>)
                    </div>
                </div>

                <!-- Dashboard Content -->
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Employees</h5>
                                <h2 class="card-text">
                                    <?php
                                    require_once 'includes/db.php';
                                    $database = new Database();
                                    $conn = $database->getConnection();
                                    $count = $conn->query("SELECT COUNT(*) as count FROM employees")->fetch()['count'];
                                    echo $count;
                                    ?>
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Active Employees</h5>
                                <h2 class="card-text">
                                    <?php
                                    $count = $conn->query("SELECT COUNT(*) as count FROM employees WHERE status = 'active'")->fetch()['count'];
                                    echo $count;
                                    ?>
                                </h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Departments</h5>
                                <h2 class="card-text">
                                    <?php
                                    $count = $conn->query("SELECT COUNT(DISTINCT department) as count FROM employees")->fetch()['count'];
                                    echo $count;
                                    ?>
                                </h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 