<?php
require __DIR__ . '/../form_db/config.php';

$username = 'test';
$newPassword = 'newpassword';
$hash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE admins SET password_hash=? WHERE username=?");
$stmt->bind_param("ss", $hash, $username);
$stmt->execute();

echo "Password reset for $username";
