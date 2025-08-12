<?php
// /form_db/moderate.php
require __DIR__ . '/config.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../admin/approvals.php?msg=Use+the+buttons+to+submit'); exit;
}

$id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$action = $_POST['action'] ?? '';

if (!$id || !in_array($action, ['approve','reject','complete'], true)) {
  header('Location: ../admin/approvals.php?msg=Invalid+request'); exit;
}

if ($action === 'approve') {
  $stmt = $conn->prepare("UPDATE submissions SET status='approved', approved_at=NOW() WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute(); $stmt->close();
  header('Location: ../admin/approvals.php?msg=Approved+%23'.$id); exit;
}

if ($action === 'reject') {
  $stmt = $conn->prepare("UPDATE submissions SET status='rejected' WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute(); $stmt->close();
  header('Location: ../admin/approvals.php?msg=Rejected+%23'.$id); exit;
}

if ($action === 'complete') {
  $stmt = $conn->prepare("UPDATE submissions SET status='completed' WHERE id=?");
  $stmt->bind_param("i", $id);
  $stmt->execute(); $stmt->close();
  header('Location: ../cases.php?admin=1&msg=Marked+as+completed+%23'.$id); exit;
}
