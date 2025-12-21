// 🌙 DARK MODE TOGGLE

const toggleBtn = document.getElementById("darkToggle");

// Load saved preference
if (localStorage.getItem("theme") === "dark") {
  document.body.classList.add("dark");
}

toggleBtn?.addEventListener("click", () => {
  document.body.classList.toggle("dark");

  // Save preference
  if (document.body.classList.contains("dark")) {
    localStorage.setItem("theme", "dark");
    toggleBtn.textContent  = "☀️";
  } else {
    localStorage.setItem("theme", "light");
    toggleBtn.textContent = "🌙";
  }
});

// Set correct icon on load
if (toggleBtn) {
  toggleBtn.textContent =
    document.body.classList.contains("dark") ? "☀️" : "🌙";
}
