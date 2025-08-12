<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Health Aid Maldives</title>
  <link rel="stylesheet" href="../styles.css" />
</head>
<body>

<?php
  $siteTitle = "Helping Maldivians Heal – 100% Free. 100% Transparent.";
  $description = "We do not collect or handle donations — all funds go directly to the patients' bank accounts.";
?>

<header>
  <h1><?= htmlspecialchars($siteTitle) ?></h1>
  <p><?= htmlspecialchars($description) ?></p>
  <div class="btn-group">
    <a href="../index.php"  class="btn">Home</a>
    <a href="../cases.php" class="btn">View Health Cases</a>
    <a href="contact.php" class="btn">Contact Us</a>
  </div>
</header>

<main>
  <div class="container">
    
    <h2>Submit Form</h2>
    <form action="./submit.php" method="POST" enctype="multipart/form-data">
      <!-- Basic Info -->
      <div class="form-group">
        <label for="fullname">Full Name*</label>
        <input type="text" name="fullname" id="fullname" required autocomplete="name" placeholder="e.g. Ismail Mohamed" />
      </div>

      <div class="form-group">
        <label for="idcard">ID Card*</label>
        <input type="text" name="idcard" id="idcard" required placeholder="e.g. A1234567" />
      </div>

      <div class="form-group">
        <label for="age">Age*</label>
        <input type="text" name="age" id="age" required inputmode="numeric" placeholder="e.g. 34" />
      </div>

      <div class="form-group">
        <label for="contact">Contact Number*</label>
        <input type="text" name="contact" id="contact" required inputmode="tel" autocomplete="tel" placeholder="e.g. 7xxxxxx" />
      </div>

      <div class="form-group">
        <label for="diagnosis">Illness / Diagnosis*</label>
        <input type="text" name="diagnosis" id="diagnosis" required placeholder="e.g. Surgery for..." />
      </div>

      <div class="form-group">
        <label for="amount">Amount Needed*</label>
        <input type="text" name="amount" id="amount" required inputmode="decimal" placeholder="e.g. 25000" />
      </div>

      <div class="form-group">
        <label for="deadline">Deadline*</label>
        <input type="date" name="deadline" id="deadline" required />
      </div>

      <div class="form-group">
        <label for="notes">Notes (Optional)</label>
        <textarea name="notes" id="notes" rows="4" placeholder="Anything donors should know..."></textarea>
      </div>

      <!-- Bank -->
      <div class="form-group">
        <label for="bank">Bank*</label>
        <select name="bank" id="bank" required>
          <option value="">-- Select Bank --</option>
          <option value="Bank Of Maldives">Bank Of Maldives</option>
          <option value="Maldives Islamic Bank">Maldives Islamic Bank</option>
        </select>
      </div>

      <!-- Accounts (hidden until bank selected) -->
      <div class="form-group" id="account-section" style="display:none;">
        <div class="account-container">
          <!-- USD -->
          <div class="account-pair">
            <div>
              <label for="account-number-usd">Account No (USD)</label>
              <input type="text" id="account-number-usd" name="account_number_usd" placeholder="USD account number" />
            </div>
            <div>
              <label for="account-name-usd">Account Name (USD)</label>
              <input type="text" id="account-name-usd" name="account_name_usd" placeholder="USD account name" />
            </div>
          </div>
          <!-- MVR -->
          <div class="account-pair">
            <div>
              <label for="account-number-mvr">Account No (MVR)</label>
              <input type="text" id="account-number-mvr" name="account_number_mvr" placeholder="MVR account number" />
            </div>
            <div>
              <label for="account-name-mvr">Account Name (MVR)</label>
              <input type="text" id="account-name-mvr" name="account_name_mvr" placeholder="MVR account name" />
            </div>
          </div>
        </div>
      </div>

      <!-- Files -->
      <div class="form-group">
        <label>Upload Supporting Document(s) (Optional)</label>
        <div id="upload-container">
          <input type="file" name="supporting_file[]" class="form-input" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" />
        </div>
        <button type="button" id="add-upload" class="add-btn" type="button">+ Add another file</button>
        <small class="note">Max ~10MB each.</small>
      </div>

      <button type="submit" class="submit-btn">Submit</button>
      <?php if (!empty($_GET['ok'])): ?>
  <div class="alert success" style="margin-top:10px; text-align:center;">
    ✅ Submission saved.
  </div>
<?php elseif (!empty($_GET['error'])): ?>
  <div class="alert error" style="margin-top:10px; text-align:center;">
    ❌ Please fill all required fields.
  </div>
<?php endif; ?>
    </form>
  </div>
</main>

<footer>
  ⚠️ Donations go directly to patients. We do not collect funds.
</footer>

<script>
  const bankSelect = document.getElementById("bank");
  const accountSection = document.getElementById("account-section");
  const requiredIds = ["account-number-usd","account-name-usd","account-number-mvr","account-name-mvr"];
  const requiredFields = requiredIds.map(id => document.getElementById(id));

  function toggleAccountSection(){
    const show = bankSelect.value !== "";
    accountSection.style.display = show ? "block" : "none";
    if (!show) requiredFields.forEach(el => { if (!el) return; el.value = ""; });
  }
  bankSelect.addEventListener("change", toggleAccountSection);
  toggleAccountSection();

  document.getElementById("add-upload").addEventListener("click", () => {
    const uploadContainer = document.getElementById("upload-container");
    const input = document.createElement("input");
    input.type = "file";
    input.name = "supporting_file[]";
    input.className = "form-input";
    input.accept = ".jpg,.jpeg,.png,.pdf,.doc,.docx";
    uploadContainer.appendChild(input);
  });
</script>

</body>
</html>
