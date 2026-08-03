// First, dynamically build the Form Interface Component inside the HTML
const appContainer = document.getElementById("app");
appContainer.innerHTML = `
<header class="mb-8">
<h1 class="text-3xl font-extrabold text-blue-900">Secure Patient
Registration</h1>
<p class="text-gray-600">Please provide register credentials to access
dashboard services.</p>
</header>
<main class="bg-white p-8 rounded-xl shadow-md border border-gray-100 max-w-lg mx-auto">
<form id="registration-form" novalidate class="space-y-6">
<div>
<label class="block text-sm font-bold text-gray-700 mb-1" for="user-email">Email Address</label>
<input type="email" id="user-email" required
class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none state-transition"
placeholder="e.g., student@email.com">
<p id="email-error" class="text-red-500 text-xs mt-1 hidden">
Please provide a valid email address.</p>
</div>
<div>
<label class="block text-sm font-bold text-gray-700 mb-1" for="user-password">Secure Password</label>
<input type="password" id="user-password" required
class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none state-transition"
placeholder="Min 8 characters, with number">
<!-- Password Strength Indicator -->
<div class="h-2 w-full bg-gray-200 rounded-full mt-2 overflow-hidden">
<div id="strength-meter" class="h-full w-0 bg-red-500 state-transition"></div>
</div>
<p id="strength-text" class="text-xs text-gray-500 mt-1">
Strength: Empty</p>
</div>
<button type="submit" class="w-full bg-blue-900 hover:bg-blue-800 text-white font-bold py-3 px-4 rounded-lg shadowstate-transition">
Create Secure Account
</button>
</form>
</main>
`;

// DOM Reference Selectors
const registrationForm = document.getElementById("registration-form");
const userEmail = document.getElementById("user-email");
const emailError = document.getElementById("email-error");
const userPassword = document.getElementById("user-password");
const strengthMeter = document.getElementById("strength-meter");
const strengthText = document.getElementById("strength-text");

// 1. Interactive Email Real-Time Validation
userEmail.addEventListener("input", function () {
  // Regex checking valid standard email string structure
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (emailRegex.test(userEmail.value)) {
    userEmail.classList.remove("border-red-500", "focus:ring-red-500");
    userEmail.classList.add("border-green-500", "focus:ring-green-500");
    emailError.classList.add("hidden");
  } else {
    userEmail.classList.remove("border-green-500", "focus:ring-green-500");
    userEmail.classList.add("border-red-500", "focus:ring-red-500");
    emailError.classList.remove("hidden");
  }
});

// 2. Real-Time Password Complexity Analysis (Regular Expressions)
userPassword.addEventListener("input", function () {
  const value = userPassword.value;
  let score = 0;
  if (value.length >= 8) score++; // Length threshold
  if (/[A-Z]/.test(value)) score++; // Has uppercase letter
  if (/[0-9]/.test(value)) score++; // Has numerical digit
  if (/[^A-Za-z0-9]/.test(value)) score++; // Has special character

  // Match visual state metrics depending on matched score
  if (value.length === 0) {
    strengthMeter.style.width = "0%";
    strengthText.textContent = "Strength: Empty";
    strengthText.className = "text-xs text-gray-500 mt-1";
  } else if (score <= 1) {
    strengthMeter.style.width = "25%";
    strengthMeter.className = "h-full bg-red-500 state-transition";
    strengthText.textContent = "Strength: Weak (Unsafe)";
    strengthText.className = "text-xs text-red-500 mt-1 font-semibold";
  } else if (score <= 3) {
    strengthMeter.style.width = "60%";
    strengthMeter.className = "h-full bg-yellow-500 state-transition";
    strengthText.textContent = "Strength: Moderate";
    strengthText.className = "text-xs text-yellow-600 mt-1 font-semibold";
  } else {
    strengthMeter.style.width = "100%";
    strengthMeter.className = "h-full bg-green-500 state-transition";
    strengthText.textContent = "Strength: Strong (Secure)";
    strengthText.className = "text-xs text-green-600 mt-1 font-semibold";
  }
});

// 3. Secure Event Submission Handler
registrationForm.addEventListener("submit", function (e) {
  e.preventDefault();
  const isEmailValid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(userEmail.value);
  const isPasswordStrong = userPassword.value.length >= 8;

  if (isEmailValid && isPasswordStrong) {
    // Save registered user info to localStorage for dashboard display
    localStorage.setItem("registered_user", userEmail.value);
    localStorage.setItem("loggedIn", "true");
    // Redirect directly to dashboard — no intermediate screen
    window.location.href = "../auth_system/dashboard.php";
  } else {
    alert(
      "Validation failed! Please correct your email and password before submitting.",
    );
  }
});

// Reusable Component Definition
function StatsCardComponent(title, count, statusColor) {
  return `
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 state-transition hover:shadow-md">
<h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">${title}</h3>
<p class="text-3xl font-bold text-gray-900 mt-2">${count}</p>
<div class="flex items-center mt-3">
<span class="h-2.5 w-2.5 rounded-full ${statusColor} mr-2"></span>
<span class="text-xs text-gray-600">Active Monitoring</span>
</div>
</div>
`;
}

// Simulated data model array fetched from backend APIs
const systemStats = [
  { name: "Total Clinic Registrations", count: "1,412", color: "bg-green-500" },
  { name: "Admitted Consultations", count: "38", color: "bg-blue-500" },
  { name: "Pending Emergency Alerts", count: "5", color: "bg-red-500" },
];

// Compile metrics into a container grid component
function renderStatsSection() {
  const sectionElement = document.createElement("section");
  sectionElement.className = "grid grid-cols-1 md:grid-cols-3 gap-6 mt-8";
  // Iterating over standard data object using map patterns
  let innerContentHTML = "";
  systemStats.forEach((item) => {
    innerContentHTML += StatsCardComponent(item.name, item.count, item.color);
  });
  sectionElement.innerHTML = innerContentHTML;
  appContainer.appendChild(sectionElement);
}

// Run compiled components render cycle
renderStatsSection();
