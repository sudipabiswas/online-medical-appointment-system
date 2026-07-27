<?php
session_start();


header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$servername = "localhost";
$username = "root"; // your DB username
$password = ""; // your DB password
$dbname = "medical_db"; // your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "DB connection failed"]));
}

// ✅ Create table automatically if not exists
$createTableSQL = "
CREATE TABLE IF NOT EXISTS committee (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(255) NOT NULL,
    role VARCHAR(100) NOT NULL
)";
$conn->query($createTableSQL);

$method = $_SERVER['REQUEST_METHOD'];

// Guard: if no active session, send the user back to login
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$user_email = htmlspecialchars($_SESSION['user_email']);
$user_role  = htmlspecialchars($_SESSION['user_role'] ?? 'N/A');
$login_time = htmlspecialchars($_SESSION['login_time'] ?? 'N/A');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="dashboard">
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
            <form action="logout.php" method="post" class="logout-form">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>

        <section class="session-info">
            <p><strong>Logged in as:</strong> <?php echo $user_email; ?></p>
            <p><strong>Role:</strong> <?php echo $user_role; ?></p>
            <p><strong>Login time:</strong> <?php echo $login_time; ?></p>
        </section>

        <!-- Cards are generated dynamically by script.js -->
        <section class="stats-grid" id="stats-grid">
        </section>
    </div>

    <script src="script.js"></script>
</body>
</html>
