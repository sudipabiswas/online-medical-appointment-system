<?php
session_start();
include "config.php";

$error_message = "";
$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $full_name = trim($_POST["full_name"] ?? '');
    $email     = trim($_POST["email"] ?? '');
    $password  = trim($_POST["password"] ?? '');

    if (empty($full_name) || empty($email) || empty($password)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        
        // 1. First check if the email already exists in the database
        $check_sql = "SELECT user_id FROM users WHERE email = ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "s", $email);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            // Friendly error message for the frontend
            $error_message = "This email address is already registered. Please log in or use a different email.";
            mysqli_stmt_close($check_stmt);
        } else {
            mysqli_stmt_close($check_stmt);

            // 2. Hash password securely
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // 3. Try inserting the new record securely
            try {
                $sql = "INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, 'Patient')";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "sss", $full_name, $email, $hashed_password);

                if (mysqli_stmt_execute($stmt)) {
                    header("Location: login.php?registered=success");
                    exit();
                } else {
                    $error_message = "Registration failed. Please try again.";
                }
                mysqli_stmt_close($stmt);
            } catch (mysqli_sql_exception $e) {
                // Catch any duplicate key exceptions safely without crashing the page
                if ($e->getCode() == 1062) { // Error 1062 = Duplicate entry
                    $error_message = "This email address is already registered. Please log in instead.";
                } else {
                    $error_message = "An unexpected error occurred during registration.";
                }
            }
        }
    }
}
?>