const contactForm = document.getElementById("contactForm");
const contactMessage = document.getElementById("contactMessage");

if (contactForm) {
  contactForm.addEventListener("submit", function (e) {
    e.preventDefault();

    const name = document.getElementById("contactName").value.trim();
    const email = document.getElementById("contactEmail").value.trim();
    const subject = document.getElementById("contactSubject").value.trim();
    const messageText = document.getElementById("contactMessageText").value.trim();

    if (!name || !email || !subject || !messageText) {
      showContactMessage("Please fill in all fields.", "error");
      return;
    }

    showContactMessage("Your message has been recorded successfully.", "success");
    contactForm.reset();
  });
}

function showContactMessage(message, type) {
  contactMessage.textContent = message;
  contactMessage.className = `form-message ${type}`;
}