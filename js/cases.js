import { db } from "./firebase.js";
import {
  collection,
  query,
  where,
  getDocs
} from "https://www.gstatic.com/firebasejs/10.7.1/firebase-firestore.js";

const container = document.getElementById("approvedCases");

async function loadApprovedCases() {
  container.innerHTML = "";

  const q = query(
    collection(db, "cases"),
    where("status", "==", "approved")
  );

  const snapshot = await getDocs(q);

  if (snapshot.empty) {
    container.innerHTML  = "<p>No approved cases available.</p>";
    return;
  }

  snapshot.forEach((docSnap) => {
    const c = docSnap.data();
    console.log("CASE DATA:", c);

    let docsHtml = "<em>No documents provided</em>";
    if (c.documents && typeof c.documents === "object") {
      docsHtml = c.documents
        .map(
          (url, i) =>
            `<a href="${url}" target="_blank" class="doc-link">
              📄 View Medical Document ${i + 1}
            </a>`
        )
        .join("<br>");
    }

    const approvedDate = c.approvedAt?.seconds
      ? new Date(c.approvedAt.seconds * 1000).toLocaleDateString()
      : "";

    container.innerHTML += `
      <div class="case-card">
        <h3>${c.name}</h3>
        <p>${c.condition}</p>
        <p><strong>Amount Needed:</strong> MVR ${c.amount}</p>
        <P><strong>Account Name:</strong>  ${c.accountname}</p>
        <P><strong>Account Number:</strong>  ${c.accountNumber}</p>
        ${approvedDate ? `<small>Approved on: ${approvedDate}</small>` : ""}
        <div class="case-documents">
          <strong>Medical Documents:</strong><br>
          ${docsHtml}
        </div>
      </div>
    `;
  });
}

loadApprovedCases();
