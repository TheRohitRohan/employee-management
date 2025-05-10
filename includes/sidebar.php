<?php
// Get current page for active menu highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!-- Sidebar -->
<nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <h3 class="text-white text-center mb-4">EMS</h3>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white <?php echo $current_page === 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo $current_page === 'employees.php' ? 'active' : ''; ?>" href="employees.php">
                    <i class="fas fa-users"></i> Employees
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo $current_page === 'add_employee.php' ? 'active' : ''; ?>" href="add_employee.php">
                    <i class="fas fa-user-plus"></i> Add Employee
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</nav> 