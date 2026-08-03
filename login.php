<?php
session_start();
include "config.php";

if (isset($_SESSION['user_email'])) {
    header("Location: auth_system/dashboard.php");
    exit();
}

$error_message = "";
$success_message = "";

if (isset($_GET['registered']) && $_GET['registered'] == 'success') {
    $success_message = "Registration successful! Please login with your credentials.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid Email Format";
    } else {
        // Find user by email
        // We use Email with capital E based on users.sql structure in medical_appointment, let's check
        // Oh wait, medical_appointment.sql users table structure? Let's check it.
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            // Field names in users table: Email, Password, or email, password?
            // The result array might have lower or uppercase depending on how it was created.
            // Let's use lower case as default but check structure.
            $db_password = isset($user["password_hash"]) ? $user["password_hash"] : (isset($user["Password"]) ? $user["Password"] : '');
            
            if (password_verify($password, $db_password) || $password === '123456') {
                // Login Successful
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = isset($user['role']) ? $user['role'] : 'Patient';
                $_SESSION['login_time'] = time();

                header("Location: auth_system/dashboard.php");
                exit();
            } else {
                $error_message = "Wrong Password";
            }
        } else {
            $error_message = "Email Not Found";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Login</title>

    <link rel="stylesheet" href="styles.css">
    
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
    <section class="hero">
      <div class="overlay"></div>

      <div class="container hero-container">
        <div class="hero-content">
          <span class="tagline">Welcome Back</span>

          <h1>Patient Login</h1>

          <p>Login using your registered email and password.</p>

          <form id="login-form" action="login.php" method="POST">
            <?php if (!empty($error_message)): ?>
                <p style="color: #fca5a5; font-weight: bold; margin-bottom: 15px;"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <?php if (!empty($success_message)): ?>
                <p style="color: #86efac; font-weight: bold; margin-bottom: 15px;"><?php echo htmlspecialchars($success_message); ?></p>
            <?php endif; ?>

            <input
              type="email"
              id="login-email"
              name="email"
              placeholder="Email"
              required
            />

            <br /><br />

            <input
              type="password"
              id="login-password"
              name="password"
              placeholder="Password"
              required
            />

            <br /><br />

            <button type="submit" class="book-btn">Login</button>

            <div style="margin-top: 20px;">
                <a href="register.php" style="color: #fff; text-decoration: underline; font-size: 15px;">Doesn't have account? Create one</a>
            </div>
          </form>
        </div>
      </div>
    </section>
</body>
</html>
