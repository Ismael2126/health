<?php
// admin/approvals.php
require __DIR__ . '/auth.php';
require_admin_login();

require __DIR__ . '/../form_db/config.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$msg = $_GET['msg'] ?? '';

$q = $conn->query("
  SELECT s.*,
         (SELECT COUNT(*) FROM uploads u WHERE u.submission_id = s.id) AS file_count
  FROM submissions s
  WHERE s.status = 'pending'
  ORDER BY s.created_at DESC
");
$rows = $q->fetch_all(MYSQLI_ASSOC);

function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Approvals - Health Aid Maldives</title>
  <link rel="stylesheet" href="../styles.css" />
  <style>
    .admin-wrap{max-width:1000px;margin:2rem auto;padding:1rem;background:#fff;border-radius:10px;box-shadow:0 3px 10px rgba(0,0,0,.08)}
    .pending-card{border:1px solid #ddd;border-radius:10px;padding:1rem;margin-bottom:1rem;background:#fafafa}
    .pending-header{display:flex;justify-content:space-between;gap:10px;align-items:center;margin-bottom:.5rem}
    .k{color:#555;width:160px;display:inline-block}
    .actions{display:flex;gap:8px;flex-wrap:wrap}
    .btn-approve{background:#198754;color:#fff;border:0;border-radius:6px;padding:8px 14px;cursor:pointer}
    .btn-reject{background:#dc3545;color:#fff;border:0;border-radius:6px;padding:8px 14px;cursor:pointer}
    .small{color:#666;font-size:.9rem}
    .files{margin-top:.5rem}
    .files a{display:inline-block;margin-right:8px;word-break:break-all}
    .notice{margin:10px 0;padding:10px;border-radius:6px}
    .success{background:#d1e7dd;color:#0f5132}
    .topnav{display:flex;gap:10px;margin:8px 0;flex-wrap:wrap;justify-content:center}
    .badge{background:#6c757d;color:#fff;cursor:default}
    .btn-logout{background:#dc3545}
  </style>
</head>
<body>
<header>
  <h1>Submission Approvals</h1>
  <p>Review pending records. Approve to publish, or reject to hide.</p>
  <div class="btn-group topnav">
    <a href="../index.php" class="btn">Home</a>
    <a href="../form_db/form.php" class="btn">New Submission</a>
    <a href="../admin/approvals.php" class="btn">Approvals</a>
    <a href="../cases.php?admin=1" class="btn">Public Cases (Admin)</a>
    <span class="btn badge"><?= e($_SESSION['admin_username'] ?? 'admin') ?></span>
    <a href="logout.php" class="btn btn-logout">Logout</a>
  </div>
</header>

<main>
  <div class="admin-wrap">
    <?php if ($msg): ?>
      <div class="notice success"><?= e($msg) ?></div>
    <?php endif; ?>

    <?php if (!$rows): ?>
      <div class="pending-card">No pending submissions.</div>
    <?php else: foreach ($rows as $r): ?>
      <div class="pending-card">
        <div class="pending-header">
          <strong>#<?= (int)$r['id'] ?> • <?= e($r['fullname']) ?></strong>
          <div class="actions">
            <!-- Approve -->
            <form method="post" action="../form_db/moderate.php">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <input type="hidden" name="action" value="approve">
              <button class="btn-approve" type="submit">Approve</button>
            </form>
            <!-- Reject -->
            <form method="post" action="../form_db/moderate.php">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <input type="hidden" name="action" value="reject">
              <button class="btn-reject" type="submit">Reject</button>
            </form>
          </div>
        </div>

        <div><span class="k">ID Card</span> <?= e($r['idcard']) ?></div>
        <div><span class="k">Age</span> <?= e($r['age']) ?></div>
        <div><span class="k">Contact</span> <?= e($r['contact']) ?></div>
        <div><span class="k">Diagnosis</span> <?= e($r['diagnosis']) ?></div>
        <div><span class="k">Amount Needed</span> <?= e($r['amount']) ?></div>
        <div><span class="k">Deadline</span> <?= e($r['deadline']) ?></div>
        <div><span class="k">Bank</span> <?= e($r['bank']) ?></div>
        <div class="small"><span class="k">USD Acc</span> <?= e($r['account_number_usd'] ?: '-') ?> • <?= e($r['account_name_usd'] ?: '-') ?></div>
        <div class="small"><span class="k">MVR Acc</span> <?= e($r['account_number_mvr'] ?: '-') ?> • <?= e($r['account_name_mvr'] ?: '-') ?></div>

        <?php
          $uf = $conn->query("SELECT file_name,file_path,filename FROM uploads WHERE submission_id=".(int)$r['id']." ORDER BY id")->fetch_all(MYSQLI_ASSOC);
          if ($uf):
        ?>
          <div class="files">
            <strong>Files (<?= count($uf) ?>):</strong>
            <?php foreach ($uf as $f): ?>
              <a href="<?= '../' . e($f['file_path']) ?>" target="_blank" rel="noopener">
                <?= e($f['filename'] ?: $f['file_name']) ?>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($r['notes'])): ?>
          <div style="margin-top:.5rem;"><span class="k">Notes</span> <?= nl2br(e($r['notes'])) ?></div>
        <?php endif; ?>
      </div>
    <?php endforeach; endif; ?>
  </div>
</main>
</body>
</html>
