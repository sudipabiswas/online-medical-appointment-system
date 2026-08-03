<?php
session_start();
include "config.php";

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // Find user by email
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {

            $user = mysqli_fetch_assoc($result);

            if (password_verify($password, $user["password_hash"])) {

                // Store session variables
                $_SESSION['user_id']   = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role']      = $user['role'];

                // Redirect to booking page
                header("Location: book_appointment.php");
                exit();

            } else {
                $error_message = "Wrong password.";
            }

        } else {
            $error_message = "Email address not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthSuite Portal - Login</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      .state-transition {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800 antialiased flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md mx-4">
        
        <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-200 state-transition">
            
            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-3xl font-extrabold text-blue-900">HealthSuite Portal</h1>
                <p class="text-gray-500 text-sm mt-1">Please provide credential details to continue.</p>
            </div>

            <!-- Registration Success Alert -->
            <?php if (isset($_GET['registered']) && $_GET['registered'] == 'success'): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded text-sm">
                    Account created successfully! Please log in below.
                </div>
            <?php endif; ?>

            <!-- Error Message Alert -->
            <?php if (!empty($error_message)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded text-sm">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="login.php" method="POST" class="space-y-6">
                
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-bold text-gray-700 mb-1">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="e.g., student@email.com" 
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                        required 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none state-transition"
                    />
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-bold text-gray-700 mb-1">Security Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        placeholder="Enter security key" 
                        required 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none state-transition"
                    />
                </div>

                <!-- Register Link -->
                <div class="text-right">
                    <a href="register.php" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">
                        Doesn't have account? Create one
                    </a>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full bg-blue-900 hover:bg-blue-800 text-white font-bold py-3 rounded-lg transition duration-200 shadow-md">
                    Secure Sign-In
                </button>
            </form>

        </div>

    </div>

</body>
</html>