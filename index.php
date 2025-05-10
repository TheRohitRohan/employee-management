<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Redirect to login page if not logged in
if (!isLoggedIn()) {
    redirect('login.php');
} else {
    redirect('dashboard.php');
}
?> 