import { auth, db, storage } from "./firebase-config.js";
import {
  onAuthStateChanged,
  signOut
} from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";
import {
  collection,
  getDocs,
  doc,
  updateDoc,
  deleteDoc
} from "https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore.js";
import {
  ref,
  deleteObject
} from "https://www.gstatic.com/firebasejs/10.12.2/firebase-storage.js";

const pendingCases = document.getElementById("pendingCases");
const approvedCases = document.getElementById("approvedCases");
const adminStatus = document.getElementById("adminStatus");
const logoutBtn = document.getElementById("logoutBtn");

onAuthStateChanged(auth, async (user) => {
  if (!user) {
    window.location.href = "login.html";
    return;
  }

  showStatus(`Logged in as ${user.email}`, "success");
  await loadCases();
});

if (logoutBtn) {
  logoutBtn.addEventListener("click", async (e) => {
    e.preventDefault();

    try {
      await signOut(auth);
      window.location.href = "login.html";
    } catch (error) {
      console.error("Logout error:", error);
      showStatus("Error logging out.", "error");
    }
  });
}

async function loadCases() {
  pendingCases.innerHTML = "<p>Loading pending cases...</p>";
  approvedCases.innerHTML = "<p>Loading approved cases...</p>";

  try {
    const snapshot = await getDocs(collection(db, "cases"));

    pendingCases.innerHTML = "";
    approvedCases.innerHTML = "";

    const allCases = [];

    snapshot.forEach((docSnap) => {
      allCases.push({
        id: docSnap.id,
        ...docSnap.data()
      });
    });

    allCases.sort((a, b) => {
      const aTime = new Date(a.createdAt || 0).getTime();
      const bTime = new Date(b.createdAt || 0).getTime();
      return bTime - aTime;
    });

    let pendingCount = 0;
    let approvedCount = 0;

    allCases.forEach((data) => {
      const caseId = data.id;
      const card = createCaseCard(caseId, data);

      if (data.status === "approved") {
        approvedCases.appendChild(card);
        approvedCount++;
      } else {
        pendingCases.appendChild(card);
        pendingCount++;
      }
    });

    if (pendingCount === 0) {
      pendingCases.innerHTML = "<p>No pending cases.</p>";
    }

    if (approvedCount === 0) {
      approvedCases.innerHTML = "<p>No approved cases.</p>";
    }
  } catch (error) {
    console.error("Load cases error code:", error.code);
    console.error("Load cases error message:", error.message);
    console.error("Full load cases error:", error);
    showStatus(`Error loading cases: ${error.code || error.message}`, "error");
  }
}

function createCaseCard(caseId, data) {
  const card = document.createElement("div");
  card.className = "card";
  card.style.textAlign = "left";

  const documentsHtml = buildDocumentsHtml(data.documents || []);

  card.innerHTML = `
    <h3>${escapeHtml(data.caseTitle || "Untitled Case")}</h3>
    <p><strong>Patient:</strong> ${escapeHtml(data.patientName || "-")}</p>
    <p><strong>Guardian:</strong> ${escapeHtml(data.guardianName || "-")}</p>
    <p><strong>Age:</strong> ${escapeHtml(data.age || "-")}</p>
    <p><strong>Gender:</strong> ${escapeHtml(data.gender || "-")}</p>
    <p><strong>Phone:</strong> ${escapeHtml(data.phone || "-")}</p>
    <p><strong>Email:</strong> ${escapeHtml(data.email || "-")}</p>
    <p><strong>Island:</strong> ${escapeHtml(data.island || "-")}</p>
    <p><strong>Hospital:</strong> ${escapeHtml(data.hospital || "-")}</p>
    <p><strong>Diagnosis:</strong> ${escapeHtml(data.diagnosis || "-")}</p>
    <p><strong>Target Amount:</strong> ${escapeHtml(data.targetAmount || "-")}</p>
    <p><strong>MVR Bank:</strong> ${escapeHtml(data.mvrBankName || "-")}</p>
    <p><strong>MVR Account Name:</strong> ${escapeHtml(data.mvrAccountName || "-")}</p>
    <p><strong>MVR Account Number:</strong> ${escapeHtml(data.mvrAccountNumber || "-")}</p>
    <p><strong>USD Bank:</strong> ${escapeHtml(data.usdBankName || "-")}</p>
    <p><strong>USD Account Name:</strong> ${escapeHtml(data.usdAccountName || "-")}</p>
    <p><strong>USD Account Number:</strong> ${escapeHtml(data.usdAccountNumber || "-")}</p>
    <p><strong>Status:</strong> ${escapeHtml(data.status || "pending")}</p>
    <p><strong>Story:</strong> ${escapeHtml(data.story || "-")}</p>

    <div class="admin-documents">
      <strong>Attached Files:</strong>
      ${documentsHtml}
    </div>

    <div style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
      ${
        data.status !== "approved"
          ? `<button class="submit-btn publish-btn" data-id="${caseId}" style="width:auto;padding:10px 16px;">Publish</button>`
          : ""
      }
      ${
        data.status !== "pending"
          ? `<button class="submit-btn pending-btn" data-id="${caseId}" style="width:auto;padding:10px 16px;background:#92400e;">Move to Pending</button>`
          : ""
      }
      <button class="submit-btn delete-btn" data-id="${caseId}" style="width:auto;padding:10px 16px;background:#b91c1c;">Delete</button>
    </div>
  `;

  const publishBtn = card.querySelector(".publish-btn");
  const pendingBtn = card.querySelector(".pending-btn");
  const deleteBtn = card.querySelector(".delete-btn");

  if (publishBtn) {
    publishBtn.addEventListener("click", async () => {
      await updateCaseStatus(caseId, "approved");
    });
  }

  if (pendingBtn) {
    pendingBtn.addEventListener("click", async () => {
      await updateCaseStatus(caseId, "pending");
    });
  }

  if (deleteBtn) {
    deleteBtn.addEventListener("click", async () => {
      const confirmed = confirm("Are you sure you want to delete this case?");
      if (confirmed) {
        await deleteCase(caseId, data);
      }
    });
  }

  return card;
}

function buildDocumentsHtml(documents) {
  if (!documents || documents.length === 0) {
    return "<p>No files attached.</p>";
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
          <img
            src="${fileUrl}"
            alt="${escapeHtml(fileName)}"
            class="doc-thumb"
          />
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

async function updateCaseStatus(caseId, status) {
  try {
    await updateDoc(doc(db, "cases", caseId), { status });
    showStatus(`Case updated to ${status}.`, "success");
    await loadCases();
  } catch (error) {
    console.error("Update status error:", error);
    showStatus("Error updating case.", "error");
  }
}

async function deleteCase(caseId, data) {
  try {
    if (data.documents && data.documents.length > 0) {
      for (const file of data.documents) {
        if (typeof file === "object" && file.path) {
          const fileRef = ref(storage, file.path);
          try {
            await deleteObject(fileRef);
          } catch (fileError) {
            console.warn("File delete warning:", fileError);
          }
        }
      }
    }

    await deleteDoc(doc(db, "cases", caseId));
    showStatus("Case deleted successfully.", "success");
    await loadCases();
  } catch (error) {
    console.error("Delete case error:", error);
    showStatus("Error deleting case.", "error");
  }
}

function showStatus(message, type) {
  adminStatus.textContent = message;
  adminStatus.className = `form-message ${type}`;
}

function escapeHtml(value) {
  return String(value)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#39;");
}