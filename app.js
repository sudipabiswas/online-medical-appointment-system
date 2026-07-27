const form = document.getElementById("register-form");
const nameInput = document.getElementById("patient-name");
const email = document.getElementById("email");
const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirm-password");

const emailFeedback = document.getElementById("email-feedback");
const passwordFeedback = document.getElementById("password-feedback");
const successMessage = document.getElementById("success-message");

if (!form) {
  console.log("Registration form not found yet.");
} else {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  email.addEventListener("input", function () {
    if (emailRegex.test(email.value)) {
      emailFeedback.textContent = "Valid Email";
      emailFeedback.style.color = "green";
    } else {
      emailFeedback.textContent = "Invalid Email Address";
      emailFeedback.style.color = "red";
    }
  });

  password.addEventListener("input", function () {
    const value = password.value;

    let strength = 0;

    if (value.length >= 8) strength++;

    if (/[A-Z]/.test(value)) strength++;

    if (/[0-9]/.test(value)) strength++;

    if (/[!@#$%^&*(),.?":{}|<>]/.test(value)) strength++;

    if (strength === 4) {
      passwordFeedback.textContent = "Strong Password";
      passwordFeedback.style.color = "green";
    } else if (strength >= 2) {
      passwordFeedback.textContent = "Medium Password";
      passwordFeedback.style.color = "orange";
    } else {
      passwordFeedback.textContent = "Weak Password";
      passwordFeedback.style.color = "red";
    }
  });

  form.addEventListener("submit", function (event) {
    event.preventDefault();

    if (!emailRegex.test(email.value)) {
      alert("Please enter a valid email.");
      return;
    }

    if (password.value !== confirmPassword.value) {
      alert("Passwords do not match.");
      return;
    }

    successMessage.textContent = "Registration Successful!";
    successMessage.style.color = "green";
    const user = {
      name: nameInput.value,
      email: email.value,
      password: password.value,
    };

    localStorage.setItem("user", JSON.stringify(user));

    form.reset();

    emailFeedback.textContent = "";
    passwordFeedback.textContent = "";
  });
}

// Book Appointment Button

const bookBtn = document.getElementById("book-btn");

if (bookBtn) {
  bookBtn.addEventListener("click", function (event) {
    event.preventDefault();

    const isLoggedIn = localStorage.getItem("isLoggedIn");

    if (isLoggedIn === "true") {
      window.location.href = "appointment.html";
    } else {
      window.location.href = "login.html";
    }
  });
}
