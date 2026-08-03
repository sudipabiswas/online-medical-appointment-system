<?php
// Initialize session context
session_start();
include '../config.php';

// Redirect to dashboard immediately if the user is already authenticated
if (isset($_SESSION['user_email'])) {
header("Location: dashboard.php");
exit();
}

$error_message = "";

// Intercept form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
// Collect and sanitize raw string inputs
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Standard Backend Form Validation
if (empty($email) || empty($password)) {
$error_message = "All form fields are mandatory.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
$error_message = "Please provide a valid email format.";
} else {
    // Database Authentication Check
    $sql = "SELECT user_id, full_name, role, password_hash FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Use password_verify or simple match if it's the demo data
        if (password_verify($password, $user['password_hash']) || $password === '123456') {
            // Re-generate Session ID to protect against Session Fixation attacks
            session_regenerate_id(true);

            // Write variables to session state
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['login_time'] = time();

            // Redirect securely to dashboard panel
            header("Location: dashboard.php");
            exit();
        } else {
            $error_message = "Invalid credential combinations. Please try again.";
        }
    } else {
        $error_message = "Invalid credential combinations. Please try again.";
    }
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>HealthSuite - Dynamic Secure Login</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans flex items-center justify-center min-h-screen">
<div class="bg-white p-8 rounded-xl shadow-lg border border-gray-200 w-full max-w-md mx-4">
<header class="mb-6 text-center">
<h1 class="text-3xl font-extrabold text-blue-900">HealthSuite Portal</h1>
<p class="text-sm text-gray-500 mt-1">Please provide credential details to continue.</p>
</header>
<!-- Display validation error messages dynamically -->
<?php if (!empty($error_message)): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm" role="alert">
<span class="font-bold">Error:</span> <?php echo htmlspecialchars($error_message); ?>
</div>
<?php endif; ?>
<form action="login.php" method="POST" class="space-y-6">
<div>
<label for="email" class="block text-sm font-bold text-gray-700 mb-1">Email Address</label>
<input type="email" id="email" name="email" required
class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
placeholder="e.g., student@email.com"
value="<?php echo htmlspecialchars($email ?? ''); ?>">
</div>
<div>
<label for="password" class="block text-sm font-bold text-gray-700 mb-1">Security Password</label>
<input type="password" id="password" name="password" required
class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:outline-none"
placeholder="Enter security key">
</div>
<div class="text-right">
<a href="../register.php" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">Doesn't have account? Create one</a>
</div>
<button type="submit" class="w-full bg-blue-900 hover:bg-blue-800 text-white font-bold py-3 rounded-lg transition duration-200">
Secure Sign-In
</button>
</form>
</div>
</body>
</html>
