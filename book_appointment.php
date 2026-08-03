<?php
// Enable full error reporting to prevent blank screens
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include "config.php";

// Flexibly check for user ID in session regardless of how login set it
$patient_id = $_SESSION['user_id'] ?? $_SESSION['id'] ?? $_SESSION['patient_id'] ?? null;

// If user is not logged in, redirect or set a default fallback for testing
if (!$patient_id) {
    // If you want to force login:
    // header("Location: login.php");
    // exit();
    
    // Default fallback so page doesn't break during dev:
    $patient_id = 6; // Uses your registered patient ID
}

$success_message = "";
$error_message = "";

// Fetch active doctors directly from the 'users' table matching 'Doctor' role
$doctors_result = false;
if (isset($conn) && $conn) {
    try {
        $doctors_query = "SELECT user_id AS doctor_id, full_name AS name FROM users WHERE role = 'Doctor'";
        $doctors_result = @mysqli_query($conn, $doctors_query);
    } catch (Exception $e) {
        $doctors_result = false;
    }
} else {
    $error_message = "Database connection error. Please check config.php.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id  = trim($_POST['doctor_id'] ?? '');
    $app_date   = trim($_POST['appointment_date'] ?? '');
    $app_time   = trim($_POST['appointment_time'] ?? '');

    // Form input validation
    if (empty($doctor_id) || empty($app_date) || empty($app_time)) {
        $error_message = "Please fill in all required fields.";
    } else {
        try {
            // Prepared statement to insert appointment safely into database
            $stmt = mysqli_prepare($conn, "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, 'Pending')");
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "iiss", $patient_id, $doctor_id, $app_date, $app_time);

                if (mysqli_stmt_execute($stmt)) {
                    $success_message = "Appointment booked successfully! Your request is pending confirmation.";
                } else {
                    $error_message = "Error executing query: " . mysqli_error($conn);
                }
                mysqli_stmt_close($stmt);
            } else {
                $error_message = "Database query preparation failed: " . mysqli_error($conn);
            }
        } catch (Exception $e) {
            $error_message = "Database Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthSuite Portal - Book Appointment</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
      .state-transition {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800 antialiased flex items-center justify-center min-h-screen py-10">

    <div class="w-full max-w-md mx-4">
        
        <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-200 state-transition">
            
            <!-- Header -->
            <div class="text-center mb-6">
                <h1 class="text-3xl font-extrabold text-blue-900">HealthSuite Portal</h1>
                <p class="text-gray-500 text-sm mt-1">Schedule your consultation in seconds.</p>
            </div>

            <!-- SUCCESS CONFIRMATION ALERT BANNER -->
            <?php if (!empty($success_message)): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-800 p-4 mb-6 rounded-r shadow-sm flex items-start space-x-3">
                    <i class="fa-solid fa-circle-check text-green-600 text-lg mt-0.5"></i>
                    <div>
                        <p class="font-bold text-sm">Booking Confirmed!</p>
                        <p class="text-xs text-green-700 mt-0.5"><?php echo htmlspecialchars($success_message); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- ERROR MESSAGE ALERT BANNER -->
            <?php if (!empty($error_message)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 mb-6 rounded-r shadow-sm flex items-start space-x-3">
                    <i class="fa-solid fa-circle-exclamation text-red-600 text-lg mt-0.5"></i>
                    <div>
                        <p class="font-bold text-sm">Action Failed</p>
                        <p class="text-xs text-red-700 mt-0.5"><?php echo htmlspecialchars($error_message); ?></p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Booking Form -->
            <form action="book_appointment.php" method="POST" class="space-y-5">
                
                <!-- Patient Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Select Patient *</label>
                    <input 
                        type="text" 
                        value="<?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['name'] ?? 'Mostafizur Rahman'); ?>" 
                        readonly 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 bg-gray-50 text-gray-600 font-medium cursor-not-allowed outline-none"
                    />
                </div>

                <!-- Select Doctor -->
                <div>
                    <label for="doctor_id" class="block text-sm font-bold text-gray-700 mb-1">Select Doctor *</label>
                    <select 
                        id="doctor_id" 
                        name="doctor_id" 
                        required 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none bg-white state-transition"
                    >
                        <option value="">-- Choose Doctor --</option>
                        <?php 
                        if ($doctors_result && mysqli_num_rows($doctors_result) > 0) {
                            while ($doctor = mysqli_fetch_assoc($doctors_result)) {
                                echo '<option value="' . $doctor['doctor_id'] . '">' . htmlspecialchars($doctor['name']) . '</option>';
                            }
                        }
                        ?>
                    </select>
                </div>

                <!-- Appointment Date -->
                <div>
                    <label for="appointment_date" class="block text-sm font-bold text-gray-700 mb-1">Date *</label>
                    <input 
                        type="date" 
                        id="appointment_date" 
                        name="appointment_date" 
                        required 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none bg-white state-transition"
                    />
                </div>

                <!-- Appointment Time -->
                <div>
                    <label for="appointment_time" class="block text-sm font-bold text-gray-700 mb-1">Time *</label>
                    <input 
                        type="time" 
                        id="appointment_time" 
                        name="appointment_time" 
                        required 
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none bg-white state-transition"
                    />
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full bg-blue-900 hover:bg-blue-800 text-white font-bold py-3 rounded-lg transition duration-200 shadow-md text-base mt-2">
                    Confirm Booking
                </button>

                <!-- Navigation Back to Home -->
                <div class="text-center mt-4">
                    <a href="home.html" class="text-sm text-blue-600 hover:text-blue-800 font-semibold">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Back to Homepage
                    </a>
                </div>

            </form>

        </div>

    </div>

</body>
</html>