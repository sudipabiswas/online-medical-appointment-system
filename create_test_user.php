<?php

include "config.php";

$name = "Test User";
$email = "test@gmail.com";
$password = "123456";

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Check if the user already exists
$check = "SELECT * FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $check);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    echo "Test user already exists.";
    exit();
}

// Insert the user
$sql = "INSERT INTO users (name, email, password)
        VALUES (?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashedPassword);

if (mysqli_stmt_execute($stmt)) {
    echo "Test user created successfully!";
    echo "<br><br>";
    echo "Email: test@gmail.com";
    echo "<br>";
    echo "Password: 123456";
} else {
    echo "Error creating user.";
}

?>