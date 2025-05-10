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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'includes/sidebar.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
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

                <!-- Recent Employees -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Recent Employees</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Department</th>
                                        <th>Job Title</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmt = $conn->query("SELECT * FROM employees ORDER BY id DESC LIMIT 5");
                                    while ($employee = $stmt->fetch()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($employee['department']) . "</td>";
                                        echo "<td>" . htmlspecialchars($employee['job_title']) . "</td>";
                                        echo "<td><span class='badge bg-" . ($employee['status'] === 'active' ? 'success' : 'danger') . "'>" . 
                                             htmlspecialchars($employee['status']) . "</span></td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 