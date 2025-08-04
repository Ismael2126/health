<?php
$successMsg = '';
$errorMsg = '';
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $successMsg = "Form submitted! (Simulation)"; // Placeholder
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Submit Health Request</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <div class="container">
    <h1>Submit Health Request</h1>

    <?php if ($successMsg): ?>
      <div class="alert success"><?= $successMsg ?></div>
    <?php endif; ?>
    <?php if ($errorMsg): ?>
      <div class="alert error"><?= $errorMsg ?></div>
    <?php endif; ?>

    <form action="submit.php" method="POST" enctype="multipart/form-data">
      <div class="form-group">
        <label>Full Name*</label>
        <input type="text" name="name" required />
      </div>

      <div class="form-group">
        <label>Age*</label>
        <input type="number" name="age" min="0" max="120" required />
      </div>

      <div class="form-group">
        <label>Address / Island*</label>
        <input type="text" name="address" required />
      </div>

      <div class="form-group">
        <label>Contact Number*</label>
        <input type="text" name="contact" required placeholder="+960 ..." />
      </div>

      <div class="form-group">
        <label>Illness / Diagnosis*</label>
        <textarea name="diagnosis" required></textarea>
      </div>

      <div class="form-group">
        <label>Amount Needed (MVR)*</label>
        <input type="number" name="amount" required />
      </div>

      <div class="form-group">
        <label>Deadline (if urgent)</label>
        <input type="date" name="deadline" />
      </div>

      <div class="form-group">
        <label>Bank Name*</label>
        <input type="text" name="bankname" required />
      </div>

      <div class="form-group">
        <label>Bank Account Number*</label>
        <input type="text" name="account" required />
      </div>

      <div class="form-group">
        <label>Upload Medical Documents* (PDF/JPG/PNG)</label>
        <input type="file" name="documents" accept=".pdf,.jpg,.jpeg,.png" required />
        <small class="note">Only valid medical documents will be accepted.</small>
      </div>

      <div class="form-group">
        <label>Profile Photo (optional)</label>
        <input type="file" name="photo" accept=".jpg,.jpeg,.png" />
      </div>

      <div class="form-group">
        <label>Additional Notes (optional)</label>
        <textarea name="notes"></textarea>
      </div>

      <button type="submit">Send for Review</button>
    </form>
  </div>
</body>
</html>
