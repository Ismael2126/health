<?php
require __DIR__ . '/../form_db/config.php';

$username = 'test';
$password = 'test';
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hash);
$stmt->execute();

echo "Admin user created: $username / $password";
