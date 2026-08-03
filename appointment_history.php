<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: auth_system/login.php");
    exit();
}

$email = $_SESSION['user_email'];

// Retrieve appointment records
$sql = "SELECT 
            a.appointment_id as id, 
            d.doctor_name, 
            DATE(a.appointment_time) as appointment_date, 
            TIME(a.appointment_time) as appointment_time, 
            a.status 
        FROM appointments a 
        INNER JOIN doctors d ON a.doctor_id = d.doctor_id 
        INNER JOIN users p ON a.patient_id = p.user_id
        WHERE p.email = ?
        ORDER BY a.appointment_time DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment History - HealthSuite</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="bg-blue-950 text-white px-6 py-4 flex justify-between items-center shadow-md">
        <span class="text-xl font-bold tracking-wider">&#9829; MedTreat</span>
        <a href="auth_system/dashboard.php" class="text-sm bg-blue-800 hover:bg-blue-700 px-4 py-2 rounded-lg transition">Back to Dashboard</a>
    </nav>

    <main class="max-w-4xl mx-auto p-6 mt-8">
        <h2 class="text-2xl font-bold text-blue-900 mb-6">Your Appointment History</h2>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 text-sm uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">Doctor</th>
                        <th class="px-6 py-4 font-semibold">Date & Time</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4 font-medium text-gray-800"><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                                <td class="px-6 py-4 text-gray-600">
                                    <?php echo date("M d, Y", strtotime($row['appointment_date'])); ?> <br>
                                    <span class="text-xs text-gray-400"><?php echo date("h:i A", strtotime($row['appointment_time'])); ?></span>
                                </td>
                                <td class="px-6 py-4">
                                    <?php 
                                        $statusClass = "bg-yellow-100 text-yellow-800";
                                        if ($row['status'] == "Confirmed") $statusClass = "bg-green-100 text-green-800";
                                        if ($row['status'] == "Cancelled") $statusClass = "bg-red-100 text-red-800";
                                    ?>
                                    <span class="px-3 py-1 rounded-full text-xs font-bold <?php echo $statusClass; ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="appointment_details.php?id=<?php echo $row['id']; ?>" class="text-blue-600 hover:text-blue-800 text-sm font-semibold underline">View Details</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">No appointments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
