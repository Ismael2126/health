<?php
$successMsg = '';
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic sanitization and validation
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    if (!$name || !$email || !$message) {
        $errorMsg = "Please fill in all fields with valid information.";
    } else {
        // Prepare email (change to your desired recipient)
        $to = "info@healthaidmv.com";
        $subject = "New Contact Message from $name";
        $body = "Name: $name\nEmail: $email\n\nMessage:\n$message";
        $headers = "From: $email\r\nReply-To: $email";

        if (mail($to, $subject, $body, $headers)) {
            $successMsg = "Thank you for contacting us, $name. We will get back to you soon.";
        } else {
            $errorMsg = "Sorry, there was an issue sending your message. Please try again later.";
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

<?php 
  $siteTitle = "Helping Maldivians Heal – 100% Free. 100% Transparent.";
  $description = "We do not collect or handle donations — all funds go directly to the patients' bank accounts.";
?>
  <header>

     <h1><?php echo $siteTitle; ?></h1>
      <p><?php echo $description; ?></p>
      <div class="btn-group">
      <a href="index.php" class="btn">Home</a>
      <a href="https://docs.google.com/forms/d/e/1FAIpQLSfdkNRuy1r5GOTJ1_CrSbf_MIbJOR5thE5nuBAItUHihxggbg/viewform?usp=dialog" target="_blank" class="btn">Submit a Request</a>
      <a href="cases.php" class="btn">View Health Cases</a>
      <a href="contact.php" class="btn">Contact Us</a>
  </div>
  </header>

  <main>
    <h2>Contact Us</h2>
    <p>If you have any questions or need assistance, please fill out the form below or email us directly at <a href="mailto:info@healthaidmv.com">info@healthaidmv.com</a>.</p>

    <?php if ($successMsg): ?>
      <div class="alert success"><?= htmlspecialchars($successMsg) ?></div>
    <?php endif; ?>
    <?php if ($errorMsg): ?>
      <div class="alert error"><?= htmlspecialchars($errorMsg) ?></div>
    <?php endif; ?>

    <form method="post" class="contact-form" novalidate>
      <label for="name">Full Name:</label>
      <input type="text" id="name" name="name" required value="<?= isset($name) ? htmlspecialchars($name) : '' ?>" />

      <label for="email">Email Address:</label>
      <input type="email" id="email" name="email" required value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" />

      <label for="message">Message:</label>
      <textarea id="message" name="message" rows="5" required><?= isset($message) ? htmlspecialchars($message) : '' ?></textarea>

      <button type="submit" class="btn">Send Message</button>
    </form>

    <section class="our-story">
      <h2>Our Story</h2>
      <p>
        This platform was inspired and created by <strong>Ismail Ahmed</strong> and <strong>Afsal Ahmed Aboobakur</strong> with the vision to help the community by providing a single platform to find and support those who need medical help.
      </p>
      <p>
        Their motivation is to make healthcare support transparent, easy, and accessible for every Maldivian in need.
      </p>
    </section>
  </main>

  <footer>
    ⚠️ Disclaimer: We do not collect or transfer money. Donations go directly to the bank accounts provided by the patients.
  </footer>
</body>
</html>
