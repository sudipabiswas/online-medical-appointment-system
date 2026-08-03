<?php
session_start();

// Guard Clause: Allow access if session exists OR if user registered via frontend
// (Frontend registration stores loggedIn flag; PHP session set after login.php)
if (!isset($_SESSION['user_email'])) {
    // We'll let the JS on the page handle the localStorage check
    // Set a placeholder so PHP doesn't crash on echo below
    $_SESSION['user_email'] = 'Guest';
    $_SESSION['user_role']  = 'New User';
    $_SESSION['login_time'] = time();
}

// Session Timeout Safety Check (5 minutes)
$timeout_duration = 300;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout_duration)) {
    session_unset();
    session_destroy();
    header("Location: login.php?msg=Session timed out due to inactivity");
    exit();
}

// Update activity timestamp
$_SESSION['last_activity'] = time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HealthSuite - User Console</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
<nav class="bg-blue-950 text-white px-6 py-4 flex justify-between items-center shadow-md">
  <span class="text-xl font-bold tracking-wider">&#9829; MedTreat Dashboard</span>
  <div class="flex items-center gap-4">
    <span class="text-sm bg-blue-800 px-3 py-1 rounded-full" id="user-role-badge">
      <?php echo htmlspecialchars($_SESSION['user_role']); ?>
    </span>
    <!-- Logout button — clicking destroys session and returns to login -->
    <a href="logout.php"
       onclick="localStorage.removeItem('loggedIn'); localStorage.removeItem('registered_user');"
       style="background:#dc2626; color:#fff; font-weight:700; padding:10px 22px; border-radius:8px; text-decoration:none; font-size:14px; display:flex; align-items:center; gap:8px; transition:background .2s;"
       onmouseover="this.style.background='#b91c1c'"
       onmouseout="this.style.background='#dc2626'">
      &#x2192; Log Out
    </a>
  </div>
</nav>
<main class="max-w-4xl mx-auto p-6 mt-8">
<div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 mb-8">
  <h2 class="text-2xl font-bold text-blue-900 mb-2">Welcome Back!</h2>
  <p class="text-gray-600">You are securely authenticated as:
    <span class="font-bold text-blue-800" id="display-email"><?php echo htmlspecialchars($_SESSION['user_email']); ?></span>
  </p>
  <div class="mt-6 border-t border-gray-100 pt-6">
    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Security Logging Metadata</h3>
    <ul class="text-xs text-gray-500 space-y-1">
      <li>Session Token: <code class="bg-gray-100 px-1 py-0.5 rounded text-blue-800"><?php echo session_id(); ?></code></li>
      <li>Authentication Time: <?php echo date("Y-m-d H:i:s", $_SESSION['login_time']); ?></li>
    </ul>
  </div>

  <div class="mt-6 flex flex-wrap gap-4 border-t border-gray-100 pt-6">
    <a href="../book_appointment.php" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold px-5 py-2.5 rounded-lg transition duration-200">
      + Book Appointment
    </a>
    <a href="../appointment_history.php" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold px-5 py-2.5 rounded-lg transition duration-200">
      View Appointment History
    </a>
  </div>
</div>

<h2 class="text-xl font-bold text-gray-800 mb-4">Live Statistics</h2>
<div id="stats-container"></div>
</main>

<script>
// Reusable Component Definition from Week 3
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
{ name: "Pending Emergency Alerts", count: "5", color: "bg-red-500" }
];

// Compile metrics into a container grid component
function renderStatsSection() {
const sectionElement = document.createElement('section');
sectionElement.className = 'grid grid-cols-1 md:grid-cols-3 gap-6';
let innerContentHTML = '';
systemStats.forEach(item => {
innerContentHTML += StatsCardComponent(item.name, item.count, item.color);
});
sectionElement.innerHTML = innerContentHTML;
document.getElementById('stats-container').appendChild(sectionElement);
}

// Run compiled components render cycle
renderStatsSection();
// If user came from registration form, show their registered email
const registeredUser = localStorage.getItem('registered_user');
if (registeredUser) {
  const emailEl = document.getElementById('display-email');
  if (emailEl && emailEl.textContent === 'Guest') {
    emailEl.textContent = registeredUser;
  }
  const roleEl = document.getElementById('user-role-badge');
  if (roleEl && roleEl.textContent.trim() === 'New User') {
    roleEl.textContent = 'Registered Patient';
  }
}
</script>
</body>
</html>
