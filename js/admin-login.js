import { auth } from "./firebase-config.js";
import { signInWithEmailAndPassword } from "https://www.gstatic.com/firebasejs/10.12.2/firebase-auth.js";

const adminLoginForm = document.getElementById("adminLoginForm");
const loginMessage = document.getElementById("loginMessage");

adminLoginForm.addEventListener("submit", async (e) => {
  e.preventDefault();

  const email = document.getElementById("adminEmail").value.trim();
  const password = document.getElementById("adminPassword").value;

  try {
    await signInWithEmailAndPassword(auth, email, password);
    showMessage("Login successful. Redirecting...", "success");
    setTimeout(() => {
      window.location.href = "dashboard.html";
    }, 800);
  } catch (error) {
    console.error("Login error code:", error.code);
    console.error("Login error message:", error.message);
    showMessage(`Login failed: ${error.code}`, "error");
  }
});

function showMessage(message, type) {
  loginMessage.textContent = message;
  loginMessage.className = `form-message ${type}`;
}