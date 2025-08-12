<?php
// form_db/submit.php
require __DIR__ . '/config.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// If not POST, bounce back to the correct form location (same folder)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ./form.php');  // <— was ../form.php (wrong)
  exit;
}

/* 1) Read fields */
$fullname = $_POST['fullname'] ?? '';
$idcard   = $_POST['idcard'] ?? '';
$age      = $_POST['age'] ?? '';
$contact  = $_POST['contact'] ?? '';
$diagnosis= $_POST['diagnosis'] ?? '';
$amount   = $_POST['amount'] ?? '';
$deadline = $_POST['deadline'] ?? '';
$notes    = $_POST['notes'] ?? '';
$bank     = $_POST['bank'] ?? '';

$account_name_usd   = $_POST['account_name_usd'] ?? '';
$account_number_usd = $_POST['account_number_usd'] ?? '';
$account_name_mvr   = $_POST['account_name_mvr'] ?? '';
$account_number_mvr = $_POST['account_number_mvr'] ?? '';

/* 2) Insert submission */
$sql = "INSERT INTO submissions
(fullname, idcard, age, contact, diagnosis, amount, deadline, notes, bank,
 account_name_usd, account_number_usd, account_name_mvr, account_number_mvr)
VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param(
  "sssssssssssss",
  $fullname, $idcard, $age, $contact, $diagnosis, $amount, $deadline, $notes, $bank,
  $account_name_usd, $account_number_usd, $account_name_mvr, $account_number_mvr
);
$stmt->execute();
$submission_id = $stmt->insert_id;
$stmt->close();

/* 3) Handle file uploads -> save to disk + record in uploads table */
$uploadDir = __DIR__ . '/uploads';
if (!is_dir($uploadDir)) { mkdir($uploadDir, 0775, true); }

$allowedExt = ['jpg','jpeg','png','pdf','doc','docx'];
$maxBytes   = 10 * 1024 * 1024; // 10MB

if (!empty($_FILES['supporting_file']) && is_array($_FILES['supporting_file']['name'])) {
  $names = $_FILES['supporting_file']['name'];
  $tmps  = $_FILES['supporting_file']['tmp_name'];
  $errs  = $_FILES['supporting_file']['error'];
  $sizes = $_FILES['supporting_file']['size'];
  $types = $_FILES['supporting_file']['type'];

  $uSql = "INSERT INTO uploads (submission_id, file_name, file_path, filename, mime, size)
           VALUES (?,?,?,?,?,?)";
  $uStmt = $conn->prepare($uSql);

  for ($i = 0; $i < count($names); $i++) {
    if (empty($names[$i])) continue;
    if ($errs[$i] !== UPLOAD_ERR_OK) continue;
    if (!is_uploaded_file($tmps[$i])) continue;
    if ($sizes[$i] > $maxBytes) continue;

    $orig = $names[$i];
    $mime = $types[$i] ?: 'application/octet-stream';
    $size = (int)$sizes[$i];

    $base = pathinfo($orig, PATHINFO_FILENAME);
    $ext  = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
    if ($ext && !in_array($ext, $allowedExt, true)) continue;

    $safeBase = preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $base);
    $newName  = $safeBase . '_' . time() . '_' . bin2hex(random_bytes(3)) . ($ext ? ".$ext" : '');
    $dest     = $uploadDir . '/' . $newName;

    if (move_uploaded_file($tmps[$i], $dest)) {
      $relPath = 'form_db/uploads/' . $newName; // web path
      $uStmt->bind_param("issssi", $submission_id, $newName, $relPath, $orig, $mime, $size);
      $uStmt->execute();
    }
  }
  $uStmt->close();
}

$conn->close();

// Redirect back to the correct form path (same folder) with success flag
header('Location: ./form.php?ok=1');  // <— was ../form.php?ok=1 (wrong)
exit;
