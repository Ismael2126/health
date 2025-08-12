<?php
$host = "sql100.infinityfree.com"; // Your MySQL Host Name
$user = "if0_39687447";            // Your MySQL Username
$pass = "9TKPHkHPjqXQ";            // Your MySQL Password
$db   = "if0_39687447_health";     // Your MySQL Database Name

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
