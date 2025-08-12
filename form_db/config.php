
<?php
$host = "sql12.freesqldatabase.com";
$user = "sql12794497";
$pass = "XkySYVqzmN";
$db   = "sql12794497";
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("DB fail: " . $conn->connect_error); }
