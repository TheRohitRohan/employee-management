<?php
require_once 'includes/config.php';
require_once 'includes/db.php';

try {
    // Test database connection
    $database = new Database();
    $conn = $database->getConnection();
    echo "Database connection successful!<br>";

    // Check if tables exist
    $tables = ['users', 'employees'];
    foreach ($tables as $table) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "Table '$table' exists.<br>";
            
            // Count records in table
            $count = $conn->query("SELECT COUNT(*) as count FROM $table")->fetch()['count'];
            echo "Number of records in '$table': $count<br>";
        } else {
            echo "Table '$table' does not exist!<br>";
        }
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 