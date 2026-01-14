<?php
// Simple script to generate a hashed password

$password = "admin123";  // Change this to the password you want
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

echo "Plain password: " . $password . "<br>";
echo "Hashed password: " . $hashedPassword;
?>
