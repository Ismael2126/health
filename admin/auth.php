<?php
// admin/auth.php
session_start();
function require_admin_login() {
  if (empty($_SESSION['admin_logged_in'])) {
    $next = $_SERVER['REQUEST_URI'] ?? '/admin/approvals.php';
    header('Location: /admin/login.php?next=' . urlencode($next));
    exit;
  }
}
