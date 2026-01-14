<?php
session_start();

// Destroy the session to log the user out
session_unset(); // Removes all session variables
session_destroy(); // Destroys the session

// Set a session variable for the logout message
$_SESSION['logout_message'] = "You are logged out. Please login to your account.";

// Redirect to index.php
header("Location: index.php");
exit();
?>
