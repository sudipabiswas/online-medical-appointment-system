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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedTreat - Create Account</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col justify-between antialiased">

    <!-- Top Navigation Header -->
    <header class="bg-white border-b border-gray-200 py-4 shadow-sm">
        <div class="max-w-6xl mx-auto px-4 flex justify-between items-center">
            <a href="home.html" class="flex items-center gap-2 text-2xl font-bold text-blue-900">
                <i class="fa-solid fa-heart-pulse text-blue-600"></i>
                <span>MedTreat</span>
            </a>
            <a href="login.php" class="text-sm font-semibold text-blue-900 hover:text-blue-700 transition">
                Already have an account? <span class="underline">Login</span>
            </a>
        </div>
    </header>

    <!-- Main Registration Form Container -->
    <main class="flex-grow flex items-center justify-center p-6">
        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl border border-gray-100 p-8">
            
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Create Account</h1>
                <p class="text-sm text-gray-500 mt-1">Join MedTreat to book appointments and manage records.</p>
            </div>

            <!-- Error Message Alert Box -->
            <?php if (!empty($error_message)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-r mb-6 text-sm flex items-start gap-3">
                    <i class="fa-solid fa-circle-exclamation text-lg mt-0.5"></i>
                    <div>
                        <p class="font-semibold">Registration Issue</p>
                        <p><?php echo htmlspecialchars($error_message); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Registration Form -->
            <form action="register.php" method="POST" class="space-y-5">
                
                <!-- Full Name Input -->
                <div>
                    <label for="full_name" class="block text-sm font-semibold text-gray-700 mb-1">Full Name</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <i class="fa-regular fa-user"></i>
                        </span>
                        <input 
                            type="text" 
                            id="full_name" 
                            name="full_name" 
                            value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" 
                            placeholder="e.g. Rahim Hossain" 
                            required 
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition"
                        />
                    </div>
                </div>

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <i class="fa-regular fa-envelope"></i>
                        </span>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                            placeholder="name@example.com" 
                            required 
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition"
                        />
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            placeholder="••••••••" 
                            required 
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition"
                        />
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full bg-blue-900 hover:bg-blue-800 text-white font-semibold py-3 rounded-xl transition shadow-md hover:shadow-lg flex items-center justify-center gap-2">
                    <i class="fa-solid fa-user-plus text-sm"></i>
                    Register Account
                </button>

            </form>

            <div class="mt-6 text-center text-xs text-gray-500">
                By registering, you agree to MedTreat's Terms of Service & Privacy Policy.
            </div>

        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 py-4 text-center text-xs text-gray-500">
        © 2026 MedTreat. All Rights Reserved.
    </footer>

</body>
</html>