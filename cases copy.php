<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Health Cases - Health Aid Maldives</title>
  <link rel="stylesheet" href="styles.css" />
</head>
<body>
  <?php
    // Load Site Title & Description from Health_headings sheet
    $metaCsvUrl = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vSa_ezOjb8bYPdK7gzUJ-86u0r28L5f1BVzz_9vsXw80AFLO2CqjgAV2pAv_rtaXIswY__ns_-HdOBq/pubhtml?gid=2017425736&single=true';

    $siteTitle = "Helping Maldivians Heal – 100% Free. 100% Transparent.";
    $description = "We do not collect or handle donations — all funds go directly to the patients' bank accounts.";

    if (($handle = fopen($metaCsvUrl, "r")) !== false) {
      $row = fgetcsv($handle); // Get first row
      if ($row) {
        $siteTitle = htmlspecialchars($row[22] ?? $siteTitle);
        $description = htmlspecialchars($row[22] ?? $description);
      }
      fclose($handle);
    }
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
    <h2>Current Health Cases</h2>
    <p>Below are some of the health cases submitted to our platform. Donations go directly to the patient’s bank accounts.</p>

    <section class="cases-list">
      <?php
        $casesCsvUrl = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vSa_ezOjb8bYPdK7gzUJ-86u0r28L5f1BVzz_9vsXw80AFLO2CqjgAV2pAv_rtaXIswY__ns_-HdOBq/pub?output=csv';

        $foundAny = false;

        if (($handle = fopen($casesCsvUrl, "r")) !== false) {
          $isHeader = true;
          while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            if ($isHeader) {
              $isHeader = false;
              continue;
            }

            // ✅ Only show cases marked as "yes" in column 15 (index 14)
            if (strtolower(trim($data[14] ?? '')) !== 'yes') {
              continue;
            }

            $foundAny = true;

            $name = htmlspecialchars($data[1] ?? '');
            $contact = htmlspecialchars($data[3] ?? '');
            $illness = htmlspecialchars($data[4] ?? '');
            $amount = floatval(str_replace(',', '', $data[5] ?? 0));
            $received = floatval(str_replace(',', '', $data[13] ?? 0));
            $bank = htmlspecialchars($data[7] ?? '');
            $account = htmlspecialchars($data[8] ?? '');
            $medicalDocsUrl = htmlspecialchars($data[9] ?? '');
            $additionalNotes = htmlspecialchars($data[11] ?? '');

            $progressPercent = ($amount > 0) ? min(100, round(($received / $amount) * 100)) : 0;

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

            echo "<div style='width: 100%; background: #ddd; border-radius: 10px; height: 20px; margin: 15px 0;'>
                    <div style='width: {$progressPercent}%; height: 100%; background: #4caf50; border-radius: 10px; text-align: center; color: white; line-height: 20px; font-weight: bold;'>
                      {$progressPercent}%
                    </div>
                  </div>
                  </article>";
          }
          fclose($handle);

          if (!$foundAny) {
            echo "<p>No approved cases available at this time.</p>";
          }
        } else {
          echo "<p>Unable to load health cases. Please try again later.</p>";
        }
      ?>
    </section>
  </main>

  <footer>
    ⚠️ Disclaimer: We do not collect or transfer money. Donations go directly to the bank accounts provided by the patients.
  </footer>
</body>
</html>
