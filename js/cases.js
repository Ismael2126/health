import { db } from "./firebase-config.js";
import { collection, getDocs } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore.js";

const publicCases = document.getElementById("publicCases");
const casesMessage = document.getElementById("casesMessage");

loadApprovedCases();

async function loadApprovedCases() {
  publicCases.innerHTML = "<p>Loading approved cases...</p>";

  try {
    const snapshot = await getDocs(collection(db, "cases"));

    const allCases = [];
    snapshot.forEach((docSnap) => {
      allCases.push({
        id: docSnap.id,
        ...docSnap.data()
      });
    });

    const approved = allCases
      .filter((item) => item.status === "approved")
      .sort((a, b) => {
        const aTime = new Date(a.createdAt || 0).getTime();
        const bTime = new Date(b.createdAt || 0).getTime();
        return bTime - aTime;
      });

    publicCases.innerHTML = "";

    if (approved.length === 0) {
      publicCases.innerHTML = "<p>No approved cases available at the moment.</p>";
      return;
    }

    approved.forEach((item) => {
      const card = createPublicCaseCard(item);
      publicCases.appendChild(card);
    });
  } catch (error) {
    console.error("Public cases load error:", error);
    showMessage("Error loading approved cases.", "error");
    publicCases.innerHTML = "";
  }
}

function createPublicCaseCard(data) {
  const card = document.createElement("div");
  card.className = "card public-case-card";
  card.style.textAlign = "left";

  const documentsHtml = buildPublicDocumentsHtml(data.documents || []);

  card.innerHTML = `
    <h3>${escapeHtml(data.caseTitle || "Untitled Case")}</h3>
    <p><strong>Patient:</strong> ${escapeHtml(data.patientName || "-")}</p>
    <p><strong>Age:</strong> ${escapeHtml(data.age || "-")}</p>
    <p><strong>Gender:</strong> ${escapeHtml(data.gender || "-")}</p>
    <p><strong>Island:</strong> ${escapeHtml(data.island || "-")}</p>
    <p><strong>Hospital:</strong> ${escapeHtml(data.hospital || "-")}</p>
    <p><strong>Diagnosis:</strong> ${escapeHtml(data.diagnosis || "-")}</p>
    <p><strong>Target Amount:</strong> MVR ${escapeHtml(data.targetAmount || "-")}</p>
    <p><strong>Story:</strong> ${escapeHtml(data.story || "-")}</p>

    <div class="public-bank-section">
      <h4>Direct Donation Accounts</h4>
      <div class="public-bank-grid">
        <div class="public-bank-box">
          <h5>MVR Account</h5>
          <p><strong>Bank:</strong> ${escapeHtml(data.mvrBankName || "-")}</p>
          <p><strong>Account Name:</strong> ${escapeHtml(data.mvrAccountName || "-")}</p>
          <p><strong>Account Number:</strong> ${escapeHtml(data.mvrAccountNumber || "-")}</p>
        </div>

        <div class="public-bank-box">
          <h5>USD Account</h5>
          <p><strong>Bank:</strong> ${escapeHtml(data.usdBankName || "-")}</p>
          <p><strong>Account Name:</strong> ${escapeHtml(data.usdAccountName || "-")}</p>
          <p><strong>Account Number:</strong> ${escapeHtml(data.usdAccountNumber || "-")}</p>
        </div>
      </div>
    </div>

    <div class="admin-documents">
      <strong>Supporting Documents:</strong>
      ${documentsHtml}
    </div>
  `;

  return card;
}

function buildPublicDocumentsHtml(documents) {
  if (!documents || documents.length === 0) {
    return "<p>No documents available.</p>";
  }

  let html = '<div class="document-preview-list">';

  documents.forEach((file) => {
    if (typeof file === "string") {
      html += `
        <div class="document-item">
          <p>${escapeHtml(file)}</p>
        </div>
      `;
      return;
    }

    const fileName = file.name || "Attached File";
    const fileUrl = file.url || "#";
    const fileType = (file.type || "").toLowerCase();
    const lowerName = fileName.toLowerCase();

    const isImage =
      fileType.startsWith("image/") ||
      lowerName.endsWith(".jpg") ||
      lowerName.endsWith(".jpeg") ||
      lowerName.endsWith(".png") ||
      lowerName.endsWith(".webp") ||
      lowerName.endsWith(".gif");

    const isPdf =
      fileType === "application/pdf" ||
      lowerName.endsWith(".pdf");

    if (isImage) {
      html += `
        <div class="document-item">
          <img src="${fileUrl}" alt="${escapeHtml(fileName)}" class="doc-thumb" />
          <p>
            <a href="${fileUrl}" target="_blank" rel="noopener noreferrer">
              Open Image: ${escapeHtml(fileName)}
            </a>
          </p>
        </div>
      `;
    } else if (isPdf) {
      html += `
        <div class="document-item">
          <p>
            📄
            <a href="${fileUrl}" target="_blank" rel="noopener noreferrer">
              Open PDF: ${escapeHtml(fileName)}
            </a>
          </p>
        </div>
      `;
    } else {
      html += `
        <div class="document-item">
          <p>
            📎
            <a href="${fileUrl}" target="_blank" rel="noopener noreferrer">
              Open File: ${escapeHtml(fileName)}
            </a>
          </p>
        </div>
      `;
    }
  });

  html += "</div>";
  return html;
}

function showMessage(message, type) {
  casesMessage.textContent = message;
  casesMessage.className = `form-message ${type}`;
}

function escapeHtml(value) {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}