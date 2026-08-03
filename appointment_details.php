<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: auth_system/login.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: appointment_history.php");
    exit();
}

$appointment_id = $_GET['id'];
$email = $_SESSION['user_email'];

// Retrieve detailed appointment record
$sql = "SELECT 
            a.appointment_id as id,
            a.appointment_date,
            TIME(a.appointment_time) as appointment_time,
            a.status,
            a.created_at,
            d.full_name as doctor_name, 
            'Specialist' as specialty, 
            p.full_name as patient_name
        FROM appointments a 
        INNER JOIN users d ON a.doctor_id = d.user_id 
        INNER JOIN users p ON a.patient_id = p.user_id
        WHERE a.appointment_id = ? AND p.email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "is", $appointment_id, $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    die("Appointment not found or you do not have permission to view it.");
}

$appointment = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Details - HealthSuite</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-blue-950 text-white px-6 py-4 flex justify-between items-center shadow-md">
        <span class="text-xl font-bold tracking-wider">&#9829; MedTreat</span>
        <a href="appointment_history.php" class="text-sm bg-blue-800 hover:bg-blue-700 px-4 py-2 rounded-lg transition">&larr; Back to History</a>
    </nav>

    <main class="max-w-3xl mx-auto p-6 mt-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden p-8">
            <h2 class="text-2xl font-bold text-blue-900 mb-6 border-b pb-4">Appointment Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Doctor Information</h3>
                    <p class="text-lg font-bold text-gray-800"><?php echo htmlspecialchars($appointment['doctor_name']); ?></p>
                    <p class="text-gray-600 mb-1"><?php echo htmlspecialchars($appointment['specialty']); ?></p>
                    <p class="text-sm text-gray-500">Room: Consultation Room</p>
                </div>
                
                <div>
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Appointment Schedule</h3>
                    <p class="text-lg font-bold text-gray-800">
                        <?php echo date("F d, Y", strtotime($appointment['appointment_date'])); ?>
                    </p>
                    <p class="text-gray-600 mb-4">
                        Time: <?php echo date("h:i A", strtotime($appointment['appointment_time'])); ?>
                    </p>
                    
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Current Status</h3>
                    <?php 
                        $statusClass = "bg-yellow-100 text-yellow-800";
                        if ($appointment['status'] == "Confirmed") $statusClass = "bg-green-100 text-green-800";
                        if ($appointment['status'] == "Cancelled") $statusClass = "bg-red-100 text-red-800";
                    ?>
                    <span class="px-4 py-2 rounded-full text-sm font-bold inline-block <?php echo $statusClass; ?>">
                        <?php echo htmlspecialchars($appointment['status']); ?>
                    </span>
                </div>
            </div>

            <div class="mt-10 bg-gray-50 p-6 rounded-lg border border-gray-100">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-2">Patient Details</h3>
                <p class="text-gray-800"><span class="font-semibold">Name:</span> <?php echo htmlspecialchars($appointment['patient_name']); ?></p>
                <p class="text-gray-800"><span class="font-semibold">Booking Reference:</span> #APT-2026-<?php echo str_pad($appointment['id'], 4, '0', STR_PAD_LEFT); ?></p>
                <p class="text-gray-500 text-sm mt-2">Booked on <?php echo date("M d, Y H:i", strtotime($appointment['created_at'])); ?></p>
            </div>
        </div>
    </main>
</body>
</html>
