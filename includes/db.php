<?php
$host = "localhost";
$user = "root"; // change if your MySQL username is different
$pass = "";     // change if your MySQL has a password
$dbname = "user_auth";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

