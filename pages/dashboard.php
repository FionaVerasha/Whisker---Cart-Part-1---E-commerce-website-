<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // Redirect to the login page if the user is not logged in
    header("Location: login.php");
    exit();
}

// Check the role of the logged-in user (assuming it's stored in $_SESSION['role'])
$userRole = $_SESSION['role']; // 'admin' or 'customer'

// Redirect the user to the appropriate dashboard based on their role
if ($userRole == 'admin') {
    header("Location: admin_dashboard.php");
    exit();
} elseif ($userRole == 'customer') {
    header("Location: customer_dashboard.php");
    exit();
} else {
    // In case of an unknown role, redirect to login (this should ideally never happen)
    header("Location: login.php");
    exit();
}
?>
