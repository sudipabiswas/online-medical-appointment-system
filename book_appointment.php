<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_email'])) {
    echo "<script>alert('Please sign in first to book an appointment.'); window.location.href = 'home.php';</script>";
    exit();
}

$user_email = $_SESSION['user_email'];
$current_patient_name = "";

// Fetch patient info
try {
    $sql_patient = "SELECT full_name FROM users WHERE email = ?";
    $stmt_patient = mysqli_prepare($conn, $sql_patient);
    mysqli_stmt_bind_param($stmt_patient, "s", $user_email);
    mysqli_stmt_execute($stmt_patient);
    $res_patient = mysqli_stmt_get_result($stmt_patient);
    if ($row = mysqli_fetch_assoc($res_patient)) {
        $current_patient_name = $row['full_name'];
    }
} catch (Exception $e) {}

// Get doctor_id from URL if passed from doctors.php
$selected_doctor_id = $_GET['doctor_id'] ?? '';

// Fetch active doctors list from database for the dropdown
$doctors_list = [];
try {
    $sql = "SELECT user_id, full_name FROM users WHERE role = 'Doctor'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $doctors_list[] = $row;
        }
    }
} catch (Exception $e) {
    // Handle database error gracefully
}

$error_message = "";
$success_message = "";

// Handle appointment submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = trim($_POST['doctor_id'] ?? '');
    $patient_name = trim($_POST['patient_name'] ?? '');
    $appointment_date = trim($_POST['appointment_date'] ?? '');
    $appointment_time = date('Y-m-d H:i:s', strtotime("$appointment_date 10:00:00"));

    if (empty($doctor_id) || empty($patient_name) || empty($appointment_date)) {
        $error_message = "Please fill in all required fields.";
    } elseif (!isset($_SESSION['user_email'])) {
        $error_message = "Please login to book an appointment.";
    } else {
        // Fetch patient_id based on session email
        $email = $_SESSION['user_email'];
        $sql = "SELECT user_id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($res) === 1) {
            $user = mysqli_fetch_assoc($res);
            $patient_id = $user['user_id'];
            
            $insert_sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, 'Scheduled')";
            $insert_stmt = mysqli_prepare($conn, $insert_sql);
            mysqli_stmt_bind_param($insert_stmt, "iiss", $patient_id, $doctor_id, $appointment_date, $appointment_time);
            if (mysqli_stmt_execute($insert_stmt)) {
                $last_id = mysqli_insert_id($conn);
                // Redirect to the new confirmation page from Mostafizur's code
                header("Location: booking_confirmation.php?booking_id=" . $last_id);
                exit();
            } else {
                $error_message = "Failed to book appointment: " . mysqli_error($conn);
            }
        } else {
            $error_message = "User not found in system.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedTreat - Book Appointment</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gray-100 min-h-screen py-10">

    <div class="max-w-xl mx-auto px-4">
        
        <!-- Header -->
        <div class="bg-white rounded-t-2xl shadow-sm border border-gray-200 p-6 text-center border-b-0">
            <h1 class="text-2xl font-bold text-blue-900 flex items-center justify-center gap-2">
                <i class="fa-solid fa-calendar-plus text-blue-600"></i>
                Book Consultation
            </h1>
            <p class="text-xs text-gray-500 mt-1">Select your preferred doctor and appointment details below.</p>
        </div>

        <div class="bg-white rounded-b-2xl shadow-md border border-gray-200 p-8">

            <?php if (!empty($success_message)): ?>
                <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded mb-6 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <span><?php echo htmlspecialchars($success_message); ?></span>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded mb-6 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                    <span><?php echo htmlspecialchars($error_message); ?></span>
                </div>
            <?php endif; ?>

            <form action="book_appointment.php" method="POST" class="space-y-5">
                
                <!-- Doctor Selection Dropdown -->
                <div>
                    <label for="doctor_id" class="block text-sm font-semibold text-gray-700 mb-1">Select Doctor</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <i class="fa-solid fa-user-doctor"></i>
                        </span>
                        <select 
                            id="doctor_id" 
                            name="doctor_id" 
                            required 
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition">
                            <option value="">-- Choose Doctor --</option>
                            <?php foreach ($doctors_list as $doc): ?>
                                <option 
                                    value="<?php echo $doc['user_id']; ?>" 
                                    <?php echo ($selected_doctor_id == $doc['user_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($doc['full_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Patient Name -->
                <div>
                    <label for="patient_name" class="block text-sm font-semibold text-gray-700 mb-1">Patient Name</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <i class="fa-regular fa-user"></i>
                        </span>
                        <input 
                            type="text" 
                            id="patient_name" 
                            name="patient_name" 
                            value="<?php echo htmlspecialchars($current_patient_name); ?>"
                            placeholder="Enter full name" 
                            required 
                            readonly
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition"
                        />
                    </div>
                </div>

                <!-- Date Selection -->
                <div>
                    <label for="appointment_date" class="block text-sm font-semibold text-gray-700 mb-1">Appointment Date</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400">
                            <i class="fa-regular fa-calendar"></i>
                        </span>
                        <input 
                            type="date" 
                            id="appointment_date" 
                            name="appointment_date" 
                            required 
                            class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none transition"
                        />
                    </div>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit" 
                    class="w-full bg-blue-900 hover:bg-blue-800 text-white font-semibold py-3 rounded-xl transition shadow-md flex items-center justify-center gap-2 text-sm">
                    <i class="fa-solid fa-check"></i>
                    Confirm Appointment
                </button>

            </form>

            <div class="mt-6 text-center">
                <a href="doctors.php" class="text-xs text-blue-700 hover:underline font-medium">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Back to Doctor Directory
                </a>
            </div>

        </div>
    </div>

</body>
</html>