<?php
// cases.php (root)
$adminMode = isset($_GET['admin']);   // /cases.php?admin=1

if ($adminMode) {
  require __DIR__ . '/admin/auth.php';
  require_admin_login(); // also ensures session
}

require __DIR__ . '/form_db/config.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$siteTitle = "Helping Maldivians Heal – 100% Free. 100% Transparent.";
$description = "We do not collect or handle donations — all funds go directly to the patients' bank accounts.";

// get approved cases
$q = $conn->query("SELECT * FROM submissions WHERE status='approved' ORDER BY approved_at DESC, created_at DESC");
$cases = $q->fetch_all(MYSQLI_ASSOC);

function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Health Cases - Health Aid Maldives</title>
  <link rel="stylesheet" href="styles.css" />
  <style>
    .topnav{display:flex;gap:10px;margin:8px 0;flex-wrap:wrap;justify-content:center}
    .badge{background:#6c757d;color:#fff;cursor:default}
    .btn-complete{background:#ff9800;color:#fff;border:0;border-radius:6px;padding:8px 14px;cursor:pointer}
  </style>
</head>
<body>
<header>
  <h1><?= e($siteTitle) ?></h1>
  <p><?= e($description) ?></p>
  <div class="btn-group topnav">
    <a href="index.php" class="btn">Home</a>
    <a href="form_db/form.php" class="btn">Submit a Request</a>
    <a href="cases.php" class="btn">View Health Cases</a>
    <a href="contact.php" class="btn">Contact Us</a>
    <?php if ($adminMode): ?>
      <a href="admin/approvals.php" class="btn">Approvals</a>
      <span class="btn badge"><?= e($_SESSION['admin_username'] ?? 'admin') ?></span>
      <a href="admin/logout.php" class="btn" style="background:#dc3545">Logout</a>
    <?php endif; ?>
  </div>
</header>

<main>
  <h2>Current Health Cases</h2>
  <p>Below are approved health cases. Donations go directly to the patient’s bank accounts.</p>

  <section class="cases-list">
    <?php if (!$cases): ?>
      <p>No approved cases available at this time.</p>
    <?php else: foreach ($cases as $c): ?>
      <article class="case">
        <h3><?= e($c['fullname']) ?> needs support for <?= e($c['diagnosis']) ?>. Amount needed: <?= e($c['amount']) ?></h3>
        <p>
          <strong>Bank:</strong> <?= e($c['bank']) ?>,
          <strong>Account (MVR):</strong> <?= e($c['account_number_mvr'] ?: '-') ?>,
          <strong>Account (USD):</strong> <?= e($c['account_number_usd'] ?: '-') ?>
        </p>
        <p><strong>Contact No:</strong> <?= e($c['contact']) ?></p>

        <?php
          $files = $conn->query("SELECT file_name,file_path,filename FROM uploads WHERE submission_id=".(int)$c['id']." ORDER BY id")->fetch_all(MYSQLI_ASSOC);
          if ($files):
        ?>
          <p><strong>Documents:</strong>
            <?php foreach ($files as $f): ?>
              <a href="<?= e($f['file_path']) ?>" target="_blank" rel="noopener">
                <?= e($f['filename'] ?: $f['file_name']) ?>
              </a>
            <?php endforeach; ?>
          </p>
        <?php endif; ?>

        <?php if (!empty($c['notes'])): ?>
          <p><strong>Additional Notes:</strong> <?= nl2br(e($c['notes'])) ?></p>
        <?php endif; ?>

        <?php if ($adminMode): ?>
          <form method="post" action="form_db/moderate.php" style="margin-top:10px;">
            <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
            <input type="hidden" name="action" value="complete">
            <button type="submit" class="btn-complete">Mark as Completed</button>
          </form>
        <?php endif; ?>
      </article>
    <?php endforeach; endif; ?>
  </section>
</main>

<footer>
  ⚠️ Disclaimer: We do not collect or transfer money. Donations go directly to the bank accounts provided by the patients.
</footer>
</body>
</html>
