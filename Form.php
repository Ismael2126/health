<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Health Aid Maldives</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>

<?php
  $siteTitle = "Helping Maldivians Heal – 100% Free. 100% Transparent.";
  $description = "We do not collect or handle donations — all funds go directly to the patients' bank accounts.";
?>

<!-- Header Section -->
<header>
  <h1><?php echo $siteTitle; ?></h1>
  <p><?php echo $description; ?></p>
  <div class="btn-group">
    <a href="form.php" class="btn">Submit a Request</a>
    <a href="cases.php" class="btn">View Health Cases</a>
    <a href="contact.php" class="btn">Contact Us</a>
  </div>
</header>

<!-- Main Section -->
<main>
  <div>
    <form id="aid-form">
  <div class="form-group">
    <label for="fullname" class="form-label">Full Name*</label>
    <input type="text" name="fullname" id="fullname" class="form-input" required />
  </div>

  <div class="form-group">
    <label for="idcard" class="form-label">ID Card*</label>
    <input type="text" name="idcard" id="idcard" class="form-input" required />
  </div>

  <div class="form-group">
    <label for="age" class="form-label">Age*</label>
    <input type="text" name="age" id="age" class="form-input" required />
  </div>

  <div class="form-group">
    <label for="contact" class="form-label">Contact Number*</label>
    <input type="text" name="contact" id="contact" class="form-input" required />
  </div>

  <div class="form-group">
    <label for="diagnosis" class="form-label">Illness / Diagnosis*</label>
    <input type="text" name="diagnosis" id="diagnosis" class="form-input" required />
  </div>

  <div class="form-group">
    <label for="amount" class="form-label">Amount Needed*</label>
    <input type="text" name="amount" id="amount" class="form-input" required />
  </div>

  <div class="form-group">
    <label for="deadline" class="form-label">Deadline*</label>
    <input type="date" name="deadline" id="deadline" class="form-input" required />
  </div>

  <div class="form-group">
    <label for="notes" class="form-label">Notes (Optional)</label>
    <textarea name="notes" id="notes" rows="4" class="form-textarea"></textarea>
  </div>

  <div class="form-group">
    <label for="bank" class="form-label">Bank</label>
    <select name="bank" id="bank" class="form-select" required>
      <option value="">-- Select Bank --</option>
      <option value="Bank Of Maldives">Bank Of Maldives</option>
      <option value="Maldives Islamic Bank">Maldives Islamic Bank</option>
    </select>
  </div>

  <div class="form-group" id="amounts-section" style="display:none;">
    <label for="amount-mvr" class="form-label">Amount (MVR)</label>
    <input type="number" id="amount-mvr" name="amount-mvr" min="0" step="0.01" />

    <label for="amount-usd" class="form-label" style="margin-top: 10px;">Amount (USD)</label>
    <input type="number" id="amount-usd" name="amount-usd" min="0" step="0.01" />
  </div>

  <button type="submit" class="submit-btn">Submit.</button>
</form>

    </div>

</div>
        

</main>

<!-- Footer Section -->
<footer>
  ⚠️ Disclaimer: We do not collect or transfer money. Donations go directly to the bank accounts provided by the patients.
</footer>
<script>
  const bankSelect = document.getElementById('Bank');
  const amountsSection = document.getElementById('amounts-section');

  bankSelect.addEventListener('change', () => {
    if (bankSelect.value) {
      amountsSection.style.display = 'block';
    } else {
      amountsSection.style.display = 'none';
    }
  });
</script>

<script>
  const form = document.getElementById("aid-form");
  const bankSelect = document.getElementById('bank');
  const amountsSection = document.getElementById('amounts-section');

  bankSelect.addEventListener('change', () => {
    if (bankSelect.value) {
      amountsSection.style.display = 'block';
    } else {
      amountsSection.style.display = 'none';
    }
  });

  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    const data = {};

    formData.forEach((value, key) => {
      data[key] = value;
    });

    fetch("https://script.google.com/macros/s/AKfycbzcVet3vPkXBt4_F544CyTYy6UyiOYCTQihUXyO9eA/exec", {
      method: "POST",
      body: JSON.stringify(data),
      headers: {
        "Content-Type": "application/json",
      },
    })
    .then((res) => res.json())
    .then((response) => {
      alert("✅ Form submitted successfully!");
      form.reset();
      amountsSection.style.display = "none";
    })
    .catch((error) => {
      alert("❌ Error submitting form. Please try again.");
      console.error("Error!", error.message);
    });
  });
</script>

</body>
</html>
