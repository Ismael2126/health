<?php
$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $idcard = filter_input(INPUT_POST, 'idcard', FILTER_SANITIZE_STRING);
    $age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
    $contact = filter_input(INPUT_POST, 'contact', FILTER_SANITIZE_STRING);
    $illness = filter_input(INPUT_POST, 'illness', FILTER_SANITIZE_STRING);
    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);
    $deadline = filter_input(INPUT_POST, 'deadline', FILTER_SANITIZE_STRING);
    $bank = filter_input(INPUT_POST, 'bank', FILTER_SANITIZE_STRING);
    $account = filter_input(INPUT_POST, 'account', FILTER_SANITIZE_STRING);
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING);

    // File uploads
    $medicalDocPath = '';
    if (!empty($_FILES['medical_documents']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $medicalDocPath = $targetDir . basename($_FILES['medical_documents']['name']);
        move_uploaded_file($_FILES['medical_documents']['tmp_name'], $medicalDocPath);
    }

    $photoPath = '';
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
        $photoPath = $targetDir . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
    }

    // Prepare data for SheetDB
   $sheetData = [
    [
        "Full Name" => $name,
        "ID Card Number" => $idcard,
        "Age" => $age,
        "Contact Number" => $contact,
        "Illness / Diagnosis" => $illness,
        "Amount Needed" => $amount,
        "Deadline" => $deadline,
        "Bank Name" => $bank,
        "Bank Account Number" => $account,
        "Upload Medical Documents" => $medicalDocPath,
        "Upload Photo" => $photoPath,
        "Additional Notes" => $notes
    ]
];


    // Initialize cURL
    $ch = curl_init('https://sheetdb.io/api/v1/6ubgncb4hr8vc');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sheetData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, 1);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error) {
        $errorMsg = "CURL Error: " . $curl_error;
    } elseif ($http_code === 201 || $http_code === 200) {
        $successMsg = "Your request was submitted successfully!";
    } else {
        $errorMsg = "Something went wrong while submitting your request. HTTP code: $http_code. Response: $response";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Submit Health Request - Health Aid Maldives</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>

<header>
  <h1>Health Aid Maldives</h1>
  <nav>
    <a href="index.php" class="btn">Home</a>
    <a href="cases.php" class="btn">Health Cases</a>
    <a href="contact.php" class="btn">Contact Us</a>
  </nav>
</header>

<main>
  <div class="container">
    <h2>Submit Health Request</h2>

    <?php if ($successMsg): ?>
      <div class="alert success"><?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>
    <?php if ($errorMsg): ?>
      <div class="alert error"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" action="submit.php">
      <div class="form-group">
        <label>Full Name*</label>
        <input type="text" name="name" required />
      </div>

      <div class="form-group">
        <label>Id Card Number*</label>
        <input type="text" name="idcard" required />
      </div>

      <div class="form-group">
        <label>Age*</label>
        <input type="number" name="age" min="0" max="120" required />
      </div>

      <div class="form-group">
        <label>Contact Number*</label>
        <input type="text" name="contact" required placeholder="+960 ..." />
      </div>

      <div class="form-group">
        <label>Illness / Diagnosis*</label>
        <textarea name="illness" required></textarea>
      </div>

      <div class="form-group">
        <label>Amount Needed (MVR)*</label>
        <input type="number" name="amount" step="0.01" required />
      </div>

      <div class="form-group">
        <label>Deadline (if urgent)</label>
        <input type="date" name="deadline" />
      </div>

      <div class="form-group">
        <label>Bank Name*</label>
        <input type="text" name="bank" required />
      </div>

      <div class="form-group">
        <label>Bank Account Number*</label>
        <input type="text" name="account" required />
      </div>

      <div class="form-group">
        <label>Upload Medical Documents* (PDF/JPG/PNG)</label>
        <input type="file" name="medical_documents" accept=".pdf,.jpg,.jpeg,.png" required />
      </div>

      <div class="form-group">
        <label>Upload Photo (optional)</label>
        <input type="file" name="photo" accept=".jpg,.jpeg,.png" />
      </div>

      <div class="form-group">
        <label>Additional Notes (optional)</label>
        <textarea name="notes"></textarea>
      </div>

      <button type="submit">Send for Review</button>
    </form>
  </div>
</main>

<footer>
  ⚠️ Disclaimer: We do not collect or transfer money. Donations go directly to the bank accounts provided by the patients.
</footer>

</body>
</html>
