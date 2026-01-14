<?php
session_start();
include '../includes/db.php'; // database connection

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Fetch user from database
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: ../pages/admin_dashboard.php");
            } else {
                header("Location: ../pages/customer_dashboard.php");
            }
            exit();
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "No user found with that username!";
    }
}
?>
