<?php
// contact.php — styled like form.php
session_start();

$siteTitle   = "Helping Maldivians Heal – 100% Free. 100% Transparent.";
$description = "We do not collect or handle donations — all funds go directly to the patients' bank accounts.";

$successMsg = '';
$errorMsg   = '';

function field($k){ return isset($_POST[$k]) ? trim($_POST[$k]) : ''; }
function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// CSRF token
if (empty($_SESSION['csrf'])) {
  $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // CSRF check
  if (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
    $errorMsg = "Invalid session. Please reload and try again.";
  } else if (!empty($_POST['website'])) { // honeypot
    $errorMsg = "Spam detected.";
  } else {
    $name    = mb_substr(strip_tags(field('name')), 0, 120);
    $email   = filter_var(field('email'), FILTER_VALIDATE_EMAIL);
    $message = mb_substr(trim(field('message')), 0, 5000);

    if ($name === '' || !$email || $message === '') {
      $errorMsg = "Please fill in all fields with valid information.";
    } else {
      $to      = "info@healthaidmv.com"; // change if needed
      $subject = "New Contact Message from {$name}";
      $body    = "Name: {$name}\nEmail: {$email}\n\nMessage:\n{$message}";
      $safeEmail = str_replace(["\r","\n"], '', $email);
      $headers = "From: noreply@healthaidmv.com\r\nReply-To: {$safeEmail}\r\nContent-Type: text/plain; charset=UTF-8\r\n";

      if (@mail($to, $subject, $body, $headers)) {
        $successMsg = "Thank you for contacting us, {$name}. We will get back to you soon.";
        $_POST = []; // clear fields
        $_SESSION['csrf'] = bin2hex(random_bytes(16)); // new token
      } else {
        $errorMsg = "Sorry, there was an issue sending your message. Please try again later.";
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Contact Us - Health Aid Maldives</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>

<header>
  <h1><?= e($siteTitle) ?></h1>
  <p><?= e($description) ?></p>
  <div class="btn-group">
    <a href="index.php" class="btn">Home</a>
    <a href="form.php" class="btn">Submit a Request</a>
    <a href="cases.php" class="btn">View Health Cases</a>
    <a href="contact.php" class="btn">Contact Us</a>
  </div>
</header>

<main>
  <div class="container">
    <h2 style="margin-bottom:1rem;">Contact Us</h2>
    <p style="margin-bottom:1.25rem;">
      If you have any questions or need assistance, please fill out the form below or email us directly at
      <a href="mailto:info@healthaidmv.com">info@healthaidmv.com</a>.
    </p>

    <form method="post" class="contact-form" novalidate>
      <!-- CSRF -->
      <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
      <!-- Honeypot (hidden) -->
      <div style="position:absolute;left:-5000px;top:-5000px;">
        <label>Website</label>
        <input type="text" name="website" tabindex="-1" autocomplete="off">
      </div>

      <div class="form-group">
        <label for="name" class="form-label">Full Name*</label>
        <input type="text" id="name" name="name" class="form-input" required maxlength="120"
               placeholder="e.g. Ismail Mohamed" value="<?= e(field('name')) ?>" />
      </div>

      <div class="form-group">
        <label for="email" class="form-label">Email Address*</label>
        <input type="email" id="email" name="email" class="form-input" required
               placeholder="e.g. name@example.com" value="<?= e(field('email')) ?>" />
      </div>

      <div class="form-group">
        <label for="message" class="form-label">Message*</label>
        <textarea id="message" name="message" class="form-textarea" rows="5" required
                  placeholder="How can we help?"><?= e(field('message')) ?></textarea>
      </div>

      <button type="submit" class="submit-btn">Send Message</button>

      <?php if ($successMsg): ?>
        <div class="alert success" style="margin-top:10px;"><?= e($successMsg) ?></div>
      <?php elseif ($errorMsg): ?>
        <div class="alert error" style="margin-top:10px;"><?= e($errorMsg) ?></div>
      <?php endif; ?>
    </form>

    <section class="our-story" style="margin-top:1.5rem;">
      <h2>Our Story</h2>
      <p>
        This platform was inspired and created by <strong>Ismail Ahmed</strong> and <strong>Afsal Ahmed Aboobakur</strong>
        with the vision to help the community by providing a single platform to find and support those who need medical help.
      </p>
      <p>
        Our motivation is to make healthcare support transparent, easy, and accessible for every Maldivian in need.
      </p>
    </section>
  </div>
</main>

<footer>
  ⚠️ Disclaimer: We do not collect or transfer money. Donations go directly to the bank accounts provided by the patients.
</footer>
</body>
</html>
