import { db, storage } from "./firebase-config.js";
import { collection, addDoc } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-firestore.js";
import { ref, uploadBytes, getDownloadURL } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-storage.js";

const aidForm = document.getElementById("aidForm");
const formMessage = document.getElementById("formMessage");

aidForm.addEventListener("submit", async function (e) {
  e.preventDefault();

  const submitBtn = aidForm.querySelector(".submit-btn");
  submitBtn.disabled = true;
  submitBtn.textContent = "Submitting...";

  const formData = {
    patientName: document.getElementById("patientName").value.trim(),
    guardianName: document.getElementById("guardianName").value.trim(),
    age: document.getElementById("age").value.trim(),
    gender: document.getElementById("gender").value,
    phone: document.getElementById("phone").value.trim(),
    email: document.getElementById("email").value.trim() || null, // optional
    island: document.getElementById("island").value.trim(),
    hospital: document.getElementById("hospital").value.trim(),
    caseTitle: document.getElementById("caseTitle").value.trim(),
    diagnosis: document.getElementById("diagnosis").value.trim(),
    story: document.getElementById("story").value.trim(),
    targetAmount: document.getElementById("targetAmount").value.trim(),

    mvrBankName: document.getElementById("mvrBankName").value,
    mvrAccountName: document.getElementById("mvrAccountName").value.trim(),
    mvrAccountNumber: document.getElementById("mvrAccountNumber").value.trim(),

    usdBankName: document.getElementById("usdBankName").value,
    usdAccountName: document.getElementById("usdAccountName").value.trim(),
    usdAccountNumber: document.getElementById("usdAccountNumber").value.trim(),

    confirmInfo: document.getElementById("confirmInfo").checked,
    status: "pending",
    createdAt: new Date().toISOString()
  };

  const documentsInput = document.getElementById("documents");
  const files = documentsInput.files;

  const hasMvrAccount = formData.mvrBankName && formData.mvrAccountName && formData.mvrAccountNumber;
  const hasUsdAccount = formData.usdBankName && formData.usdAccountName && formData.usdAccountNumber;

  if (!formData.confirmInfo) {
    showMessage("Please confirm that the information is accurate.", "error");
    resetSubmitButton(submitBtn);
    return;
  }

  // Required fields check
  if (
    !formData.patientName ||
    !formData.guardianName ||
    !formData.age ||
    !formData.gender ||
    !formData.phone ||
    !formData.island ||
    !formData.hospital ||
    !formData.caseTitle ||
    !formData.diagnosis ||
    !formData.story ||
    !formData.targetAmount
  ) {
    showMessage("Please fill in all required fields.", "error");
    resetSubmitButton(submitBtn);
    return;
  }

  if (!hasMvrAccount && !hasUsdAccount) {
    showMessage("Please provide at least one complete bank account: MVR or USD.", "error");
    resetSubmitButton(submitBtn);
    return;
  }

  try {
    const uploadedDocuments = [];

    for (let i = 0; i < files.length; i++) {
      const file = files[i];
      const safeName = `${Date.now()}_${file.name.replace(/\s+/g, "_")}`;
      const filePath = `cases/${safeName}`;
      const fileRef = ref(storage, filePath);

      await uploadBytes(fileRef, file);
      const downloadURL = await getDownloadURL(fileRef);

      uploadedDocuments.push({
        name: file.name,
        url: downloadURL,
        path: filePath,
        type: file.type
      });
    }

    formData.documents = uploadedDocuments;

    await addDoc(collection(db, "cases"), formData);

    showMessage("Case submitted successfully. Waiting for admin approval.", "success");
    aidForm.reset();
  } catch (error) {
    console.error("Submit error:", error);
    showMessage("Error submitting form. Please try again.", "error");
  } finally {
    resetSubmitButton(submitBtn);
  }
});

function showMessage(message, type) {
  formMessage.textContent = message;
  formMessage.className = `form-message ${type}`;
}

function resetSubmitButton(button) {
  button.disabled = false;
  button.textContent = "Submit Request";
}