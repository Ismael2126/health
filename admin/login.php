<?php
// admin/login.php
session_start();
require __DIR__ . '/../form_db/config.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = (string)($_POST['password'] ?? '');

    if ($u === '' || $p === '') {
        $error = 'Enter username and password.';
    } else {
        $stmt = $conn->prepare("SELECT id, username, password_hash FROM admins WHERE username=? LIMIT 1");
        $stmt->bind_param("s", $u);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        // Check if password matches (supports hashed or plain)
        if ($row && (password_verify($p, $row['password_hash']) || $p === $row['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username']  = $row['username'];
            $conn->close();
            header('Location: /health/admin/approvals.php'); // Always go here
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="/health/styles.css">
  <style>
    .card{max-width:420px;margin:10vh auto;background:#fff;padding:24px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.06)}
    .card h1{margin:0 0 12px}
    .form-group{margin:10px 0}
    .form-group label{display:block;margin-bottom:6px;font-weight:600}
    .form-group input{width:100%;padding:10px;border:1px solid #ccc;border-radius:6px}
    .btn{display:inline-block;padding:10px 16px;border-radius:6px;background:#0d6efd;color:#fff;border:0;cursor:pointer}
    .alert{margin-top:10px;padding:10px;border-radius:6px}
    .error{background:#f8d7da;color:#842029}
  </style>
</head>
<body>
  <div class="card">
    <h1>Admin Login</h1>
    <form method="post">
      <div class="form-group">
        <label>Username</label>
        <input name="username" autofocus required>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input name="password" type="password" required>
      </div>
      <button class="btn" type="submit">Sign in</button>
      <?php if ($error): ?>
        <div class="alert error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
    </form>
  </div>
</body>
</html>
