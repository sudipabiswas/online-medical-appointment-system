<<<<<<< HEAD

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

    <form id="login-form">

        <h2>Online Medical Appointment System</h2>

        <h3>Login</h3>

        <label for="email">Email</label>

        <input
            type="email"
            id="email"
            placeholder="Enter your email"
            required>

        <label for="password">Password</label>

        <input
            type="password"
            id="password"
            placeholder="Enter your password"
            required>

        <button type="button">
            Login
        </button>

        <p class="success">Demo Login Form</p>

    </form>

</div>

</body>
</html>
=======
<?php
include "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid Email Format");
    }

    // Find user by email
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user["password"])) {

            echo "Login Successful!";

            // We'll replace this with a dashboard redirect later
            // header("Location: dashboard.php");

        } else {

            echo "Wrong Password";

        }

    } else {

        echo "Email Not Found";

    }

}
?>
>>>>>>> origin/Mostafizur
