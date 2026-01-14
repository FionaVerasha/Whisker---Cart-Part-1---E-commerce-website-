<?php
session_start();
include '../includes/db.php'; // database connection

// Error messages
$error_message = "";

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error_message = "Username is already taken!";
        } else {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error_message = "Email is already in use!";
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Insert new user into the database
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                $role = 'customer'; // Default role for new users
                $stmt->bind_param("ssss", $username, $email, $hashed_password, $role);

                if ($stmt->execute()) {
                    // Redirect to login page after successful registration
                    header("Location: login.php");
                    exit();
                } else {
                    $error_message = "Error: Unable to register user.";
                }
            }
        }
    }
}
?>
