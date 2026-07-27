document.addEventListener("DOMContentLoaded", () => {

    const statsGrid = document.getElementById("stats-grid");

    if (!statsGrid) return;

    // Reusable Component
    function StatsCardComponent(title, count, statusColor) {
        return `
            <div class="card">
                <h3>${title}</h3>
                <p class="card-count">${count}</p>

                <div class="card-status">
                    <span class="status-dot" style="background:${statusColor};"></span>
                    <span>Active Monitoring</span>
                </div>
            </div>
        `;
    }
// DOM References
const registrationForm = document.getElementById("registration-form");
const userEmail = document.getElementById("user-email");
const emailError = document.getElementById("email-error");
const userPassword = document.getElementById("user-password");
const strengthMeter = document.getElementById("strength-meter");
const strengthText = document.getElementById("strength-text");

// Email Validation
userEmail.addEventListener("input", () => {

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (emailRegex.test(userEmail.value)) {
        emailError.style.display = "none";
        userEmail.style.borderColor = "green";
    } else {
        emailError.style.display = "block";
        userEmail.style.borderColor = "red";
    }

});

// Password Strength
userPassword.addEventListener("input", () => {

    const value = userPassword.value;

    let score = 0;

    if (value.length >= 8) score++;
    if (/[A-Z]/.test(value)) score++;
    if (/[0-9]/.test(value)) score++;
    if (/[^A-Za-z0-9]/.test(value)) score++;

    if (value.length === 0) {
        strengthMeter.style.width = "0%";
        strengthText.textContent = "Strength: Empty";
    }
    else if (score <= 1) {
        strengthMeter.style.width = "25%";
        strengthMeter.style.background = "red";
        strengthText.textContent = "Strength: Weak";
    }
    else if (score <= 3) {
        strengthMeter.style.width = "60%";
        strengthMeter.style.background = "orange";
        strengthText.textContent = "Strength: Moderate";
    }
    else {
        strengthMeter.style.width = "100%";
        strengthMeter.style.background = "green";
        strengthText.textContent = "Strength: Strong";
    }

});

// Form Submit
registrationForm.addEventListener("submit", (event) => {

    event.preventDefault();

    alert("Form Submitted Successfully!");

});
    // Data Array
    const systemStats = [
        {
            title: "Total Doctors",
            count: "10",
            color: "green"
        },
        {
            title: "Total Patients",
            count: "85",
            color: "blue"
        },
        {
            title: "Pending Appointments",
            count: "35",
            color: "red"
        }
    ];

    // Dynamic Rendering
    let cardsHTML = "";

    systemStats.forEach((stat) => {
        cardsHTML += StatsCardComponent(
            stat.title,
            stat.count,
            stat.color
        );
    });

    statsGrid.innerHTML = cardsHTML;
});