<?php
session_start();
include 'config.php';
$dbStats = ['doctors'=>0, 'patients'=>0, 'appointments'=>0, 'pending'=>0];
$recent_appointments = [];
if (isset($conn)) {
    $res = mysqli_query($conn, "SELECT 
        (SELECT COUNT(*) FROM users WHERE role='Doctor') as doctors,
        (SELECT COUNT(*) FROM users WHERE role='Patient') as patients,
        (SELECT COUNT(*) FROM appointments) as appointments,
        (SELECT COUNT(*) FROM appointments WHERE status='Scheduled' OR status='Pending') as pending");
    if ($res) {
        $dbStats = mysqli_fetch_assoc($res);
    }
    
    $appt_sql = "SELECT a.appointment_id as id, p.full_name as patient, d.full_name as doctor, a.appointment_date as date, a.status 
                 FROM appointments a 
                 JOIN users p ON a.patient_id = p.user_id 
                 JOIN users d ON a.doctor_id = d.user_id 
                 ORDER BY a.appointment_date DESC LIMIT 5";
    $appt_res = mysqli_query($conn, $appt_sql);
    if ($appt_res) {
        while($row = mysqli_fetch_assoc($appt_res)) {
            $recent_appointments[] = $row;
        }
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Medical Appointment System</title>

    <link rel="stylesheet" href="styles.css" />

    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    />
  </head>

  <body>
    <div class="top-bar">
      <div class="container">
        <div class="top-left">
          <i class="fa-solid fa-phone"></i>
          +880 1311998252
        </div>

        <div class="top-right">
          <a href="#">Emergency</a>

          <a href="#">Support</a>

          <a href="#">Contact</a>
        </div>
      </div>
    </div>

    <header>
      <div class="navbar container">
        <!-- Logo -->
        <div class="logo">
          <i class="fa-solid fa-heart-pulse"></i>
          <div>
            <h2>MedTreat</h2>
          </div>
        </div>

        <div class="menu-toggle" id="menu-toggle">
          <i class="fa-solid fa-bars"></i>
        </div>
        <nav id="nav-menu">
          <ul>
            <li><a class="active" href="home.php">Home</a></li>
            <li><a href="#">Doctors</a></li>
            <li><a href="#">Appointments</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">About</a></li>
            <li><a href="#">Contact</a></li>
          </ul>
        </nav>
        <div class="nav-buttons">
          <a href="login.php" class="login-btn">
            <i class="fa-regular fa-user"></i> Login
          </a>

          <a href="#" class="book-btn" id="book-btn-nav">Book Appointment</a>
        </div>
      </div>
    </header>

    <section class="hero">
      <div class="overlay"></div>

      <div class="container hero-container">
        <div class="hero-content">
          <span class="tagline"> Trusted Healthcare Platform </span>

          <h1>
            Your Health Is Our <br />
            Highest Priority
          </h1>

          <p>
            Easily book appointments with experienced doctors, manage your
            medical records, receive digital prescriptions, and access quality
            healthcare services anytime and anywhere.
          </p>

          <div class="hero-buttons">
            <a href="#" class="book-btn" id="book-btn-hero">Book Appointment</a>

            <a href="login.php" class="login-btn">
              <i class="fa-regular fa-user"></i>
              Login
            </a>
          </div>

          <div class="hero-info">
            <div class="info-box">
              <h2>50+</h2>

              <span>Qualified Doctors</span>
            </div>

            <div class="info-box">
              <h2>24/7</h2>

              <span>Medical Support</span>
            </div>

            <div class="info-box">
              <h2>100%</h2>

              <span>Secure System</span>
            </div>
          </div>
        </div>

        <!-- Right Side -->

        <div class="hero-image">
          <img src="images/doctor.png" alt="Doctor" />

          <div class="floating-card">
            <h3>Why Choose Us?</h3>

            <ul>
              <li>
                <i class="fa-solid fa-check"></i>
                Expert Doctors
              </li>

              <li>
                <i class="fa-solid fa-check"></i>
                Online Appointment
              </li>

              <li>
                <i class="fa-solid fa-check"></i>
                Digital Prescription
              </li>

              <li>
                <i class="fa-solid fa-check"></i>
                24/7 Patient Support
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== STATISTICS DASHBOARD SECTION ===== -->
    <section id="dashboard" style="background:#f0f4ff; padding: 60px 0;">
      <div class="container">
        <div style="text-align:center; margin-bottom:40px;">
          <span style="background:#e0e7ff; color:#3730a3; font-size:13px; font-weight:600; padding:6px 16px; border-radius:20px; letter-spacing:1px; text-transform:uppercase;">Live Overview</span>
          <h2 style="font-size:2rem; font-weight:700; color:#1e3a5f; margin-top:12px;">System Statistics Dashboard</h2>
          <p style="color:#6b7280; font-size:15px;">Real-time data overview of the medical appointment platform.</p>
        </div>

        <!-- Stats Cards Grid -->
        <div id="stats-grid" style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:24px; margin-bottom:48px;"></div>

        <!-- Recent Appointments Table -->
        <div style="background:#fff; border-radius:16px; padding:32px; box-shadow:0 4px 20px rgba(0,0,0,0.07);">
          <h3 style="font-size:1.1rem; font-weight:700; color:#1e3a5f; margin-bottom:20px;">
            <i class="fa-solid fa-calendar-check" style="color:#3b82f6; margin-right:8px;"></i>
            Recent Appointments
          </h3>
          <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; font-size:14px;">
              <thead>
                <tr style="background:#f8faff; text-align:left;">
                  <th style="padding:12px 16px; color:#6b7280; font-weight:600; border-bottom:2px solid #e5e7eb;">#</th>
                  <th style="padding:12px 16px; color:#6b7280; font-weight:600; border-bottom:2px solid #e5e7eb;">Patient</th>
                  <th style="padding:12px 16px; color:#6b7280; font-weight:600; border-bottom:2px solid #e5e7eb;">Doctor</th>
                  <th style="padding:12px 16px; color:#6b7280; font-weight:600; border-bottom:2px solid #e5e7eb;">Date</th>
                  <th style="padding:12px 16px; color:#6b7280; font-weight:600; border-bottom:2px solid #e5e7eb;">Status</th>
                </tr>
              </thead>
              <tbody id="appointments-table-body"></tbody>
            </table>
          </div>
        </div>
      </div>
    </section>
    <!-- ===== END DASHBOARD SECTION ===== -->

    <footer style="background:#1e3a5f; color:#fff; text-align:center; padding:20px;">
      <p style="margin:0; font-size:14px;">© 2026 MedTreat. All Rights Reserved.</p>
    </footer>

    <script>
      const menuToggle = document.getElementById("menu-toggle");
      const navMenu = document.getElementById("nav-menu");

      menuToggle.addEventListener("click", function () {
        navMenu.classList.toggle("active");
      });

      // Book Appointment Buttons
      const bookButtons = document.querySelectorAll(".book-btn");
      const isLoggedIn = <?php echo isset($_SESSION['user_email']) ? 'true' : 'false'; ?>;
      
      bookButtons.forEach((button) => {
        button.addEventListener("click", function (event) {
          event.preventDefault();
          if (!isLoggedIn) {
              alert("Please sign in first to book an appointment.");
              window.location.href = "login.php";
          } else {
              window.location.href = "book_appointment.php";
          }
        });
      });

      // ---- DASHBOARD LOGIC ----

      // Reusable StatsCardComponent
      function StatsCardComponent(icon, title, count, color, bg) {
        return `
          <div style="background:#fff; border-radius:16px; padding:28px 24px; box-shadow:0 4px 20px rgba(0,0,0,0.07); display:flex; flex-direction:column; gap:12px; transition:transform .2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="width:48px; height:48px; border-radius:12px; background:${bg}; display:flex; align-items:center; justify-content:center;">
              <i class="${icon}" style="color:${color}; font-size:20px;"></i>
            </div>
            <p style="font-size:2rem; font-weight:700; color:#1e3a5f; margin:0;">${count}</p>
            <p style="font-size:14px; color:#6b7280; margin:0; font-weight:500;">${title}</p>
            <div style="display:flex; align-items:center; gap:6px;">
              <span style="width:8px; height:8px; border-radius:50%; background:${color};"></span>
              <span style="font-size:12px; color:#9ca3af;">Active Monitoring</span>
            </div>
          </div>`;
      }

        // Stats data array dynamically built
        const systemStats = [
          { icon: "fa-solid fa-user-doctor",    title: "Total Doctors",            count: "<?php echo $dbStats['doctors']; ?>",   color: "#10b981", bg: "#d1fae5" },
          { icon: "fa-solid fa-hospital-user",  title: "Registered Patients",      count: "<?php echo $dbStats['patients']; ?>", color: "#3b82f6", bg: "#dbeafe" },
          { icon: "fa-solid fa-calendar-check", title: "Total Appointments",     count: "<?php echo $dbStats['appointments']; ?>",    color: "#f59e0b", bg: "#fef3c7" },
          { icon: "fa-solid fa-clock", title: "Pending Appointments",  count: "<?php echo $dbStats['pending']; ?>",     color: "#ef4444", bg: "#fee2e2" }
        ];

      // Render cards
      const statsGrid = document.getElementById("stats-grid");
      systemStats.forEach(stat => {
        statsGrid.innerHTML += StatsCardComponent(stat.icon, stat.title, stat.count, stat.color, stat.bg);
      });

        // Recent appointments data from database
        const appointments = <?php echo json_encode($recent_appointments); ?>;

      const statusColors = {
          "Scheduled": { bg: "#fef3c7", color: "#92400e" },
          "Completed": { bg: "#d1fae5", color: "#065f46" },
          "Confirmed": { bg: "#d1fae5", color: "#065f46" },
          "Pending":   { bg: "#fef3c7", color: "#92400e" },
          "Cancelled": { bg: "#fee2e2", color: "#991b1b" },
      };

      const tbody = document.getElementById("appointments-table-body");
      appointments.forEach(appt => {
        const s = statusColors[appt.status];
        tbody.innerHTML += `
          <tr style="border-bottom:1px solid #f3f4f6;">
            <td style="padding:14px 16px; color:#9ca3af;">${appt.id}</td>
            <td style="padding:14px 16px; font-weight:600; color:#1e3a5f;">${appt.patient}</td>
            <td style="padding:14px 16px; color:#374151;">${appt.doctor}</td>
            <td style="padding:14px 16px; color:#6b7280;">${appt.date}</td>
            <td style="padding:14px 16px;">
              <span style="background:${s.bg}; color:${s.color}; padding:4px 12px; border-radius:20px; font-size:12px; font-weight:600;">${appt.status}</span>
            </td>
          </tr>`;
      });
    </script>
  </body>
</html>
</html>

