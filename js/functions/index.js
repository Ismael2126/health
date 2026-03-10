const { onDocumentCreated } = require("firebase-functions/v2/firestore");

exports.notifyTelegramOnNewCase = onDocumentCreated("cases/{caseId}", async (event) => {
  const data = event.data.data();
  const caseId = event.params.caseId;

  const botToken = 8614574689:AAH7XGMKY15W10CDn8K4z96KfDErUlC9tIY;
  const chatId = 939528688;

  const message =
`🚨 New Health Aid Case

Case ID: ${caseId}
Patient: ${data.patientName || "-"}
Guardian: ${data.guardianName || "-"}
Title: ${data.caseTitle || "-"}
Diagnosis: ${data.diagnosis || "-"}
Island: ${data.island || "-"}
Hospital: ${data.hospital || "-"}
Phone: ${data.phone || "-"}
Target: ${data.targetAmount || "-"}
Status: ${data.status || "pending"}`;

  const response = await fetch(`https://api.telegram.org/bot${botToken}/sendMessage`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json"
    },
    body: JSON.stringify({
      chat_id: chatId,
      text: message
    })
  });

  const result = await response.text();
  console.log("Telegram response:", result);
});