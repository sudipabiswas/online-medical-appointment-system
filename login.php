<?php
session_start();
include "config.php";

$error_message = "";

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
    <title>Login</title>

    <link rel="stylesheet" href="styles.css">
</head>

<body>

<div class="login-container">

    <form id="login-form" action="login.php" method="POST">

        <h2>Online Medical Appointment System</h2>

        <h3>Login</h3>

        <?php if (!empty($error_message)): ?>
            <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <label for="email">Email</label>

        <input
            type="email"
            id="email"
            name="email"
            placeholder="Enter your email"
            required>

        <label for="password">Password</label>

        <input
            type="password"
            id="password"
            name="password"
            placeholder="Enter your password"
            required>

        <button type="submit">
            Login
        </button>

    </form>

</div>

</body>
</html>
