<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Health Cases - Health Aid Maldives</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <header>
    <h1>Health Aid Maldives</h1>
    <nav>
      <a href="index.php" class="btn">Home</a>
      <a href="cases.php" class="btn active">Health Cases</a>
      <a href="contact.php" class="btn">Contact Us</a>
    </nav>
  </header>

  <main>
    <h2>Current Health Cases</h2>
    <p>
      Below are some of the health cases submitted to our platform. Donations
      go directly to the patient’s bank accounts.
    </p>

    <section class="cases-list">
      <?php
        // Google Sheet CSV URL
        $csvUrl = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vSa_ezOjb8bYPdK7gzUJ-86u0r28L5f1BVzz_9vsXw80AFLO2CqjgAV2pAv_rtaXIswY__ns_-HdOBq/pub?output=csv';

        if (($handle = fopen($csvUrl, "r")) !== false) {
          $isHeader = true;
          while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            if ($isHeader) {
              $isHeader = false;
              continue;
            }

            $name = htmlspecialchars($data[1] ?? '');
            $contact = htmlspecialchars($data[3] ?? '');
            $illness = htmlspecialchars($data[4] ?? '');
            $amount = floatval(str_replace(',', '', $data[5] ?? 0)); // Needed amount
            $received = floatval(str_replace(',', '', $data[13] ?? 0)); // Received amount
            $bank = htmlspecialchars($data[7] ?? '');
            $account = htmlspecialchars($data[8] ?? '');
            $medicalDocsUrl = htmlspecialchars($data[9] ?? '');
            $additionalNotes = htmlspecialchars($data[11] ?? '');  // New line added
            

            // Calculate progress percent (max 100)
            $progressPercent = 0;
            if ($amount > 0) {
              $progressPercent = min(100, round(($received / $amount) * 100));
            }

            echo "<article class='case'>
                    <h3>$name needs support for $illness. Amount needed: $amount</h3>
                    <p><strong>Bank:</strong> $bank , <strong>Account number:</strong> $account</p>
                    <p><strong>Contact No:</strong> $contact</p>";

            if ($medicalDocsUrl) {
              echo "<p><strong>Medical Documents:</strong> <a href='$medicalDocsUrl' target='_blank' rel='noopener'>View Documents</a></p>";
            }
            if ($additionalNotes) {
  echo "<p><strong>Additional Notes:</strong> $additionalNotes</p>";
}

            // Green progress bar
            echo "<div style='width: 100%; background: #ddd; border-radius: 10px; height: 20px; margin: 15px 0;'>
                    <div style='width: {$progressPercent}%; height: 100%; background: #4caf50; border-radius: 10px; text-align: center; color: white; line-height: 20px; font-weight: bold;'>
                      {$progressPercent}%
                    </div>
                  </div>";

            echo "</article>";
          }
          fclose($handle);
        } else {
          echo "<p>Unable to load health cases. Please try again later.</p>";
        }
      ?>
    </section>
  </main>

  <footer>
    ⚠️ Disclaimer: We do not collect or transfer money. Donations go directly
    to the bank accounts provided by the patients.
  </footer>
</body>
</html>
