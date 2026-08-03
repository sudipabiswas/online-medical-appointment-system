/* ============================================================
   systemStats
   Array of data objects driving the dashboard cards.
   Add/remove objects here and the UI updates automatically —
   no HTML editing required.
   ============================================================ */
const systemStats = [
  { label: "Total Doctors", value: 10 },
  { label: "Total Patients", value: 1000 },
  { label: "Pending Appointments", value: 35 },
];

/* ============================================================
   StatsCardComponent(stat)
   Reusable "component" function: takes a single stat object
   ({ label, value }) and returns a fully built <div class="card">
   DOM node using createElement / appendChild / textContent.
   ============================================================ */
function StatsCardComponent(stat) {
  // Card container
  const card = document.createElement("div");
  card.classList.add("card");

  // Title
  const title = document.createElement("h3");
  title.textContent = stat.label;

  // Value
  const value = document.createElement("p");
  value.textContent = stat.value;

  // Assemble card
  card.appendChild(title);
  card.appendChild(value);

  return card;
}

/* ============================================================
   renderDashboard(stats)
   Loops over the systemStats array and appends a
   StatsCardComponent for each entry into the #stats-grid
   container. Clears any existing content first so it can be
   called again safely (e.g. after updating systemStats).
   ============================================================ */
function renderDashboard(stats) {
  const grid = document.getElementById("stats-grid");

  // Clear existing cards before re-rendering
  grid.textContent = "";

  stats.forEach((stat) => {
    const card = StatsCardComponent(stat);
    grid.appendChild(card);
  });
}

/* ============================================================
   Run once the DOM is ready.
   ============================================================ */
document.addEventListener("DOMContentLoaded", () => {
  renderDashboard(systemStats);
});
