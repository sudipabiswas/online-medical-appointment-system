<?php
require_once 'config.php';

$booking_id = $_GET['booking_id'] ?? null;
$booking = null;

if ($booking_id) {
    // JOIN query based on Listing 3 of the lab manual
    $sql = "SELECT 
                a.appointment_id,
                a.appointment_time,
                a.status,
                p.full_name AS patient_name,
                p.email AS patient_email,
                d.full_name AS doctor_name
            FROM appointments a
            INNER JOIN users p ON a.patient_id = p.user_id
            INNER JOIN users d ON a.doctor_id = d.user_id
            WHERE a.appointment_id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $booking = $result->fetch_assoc();
        $stmt->close();
    }
}

if (!$booking) {
    die("Invalid booking reference ID or record not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation</title>
    <style>
        .confirmation-card { max-width: 480px; margin: 50px auto; padding: 25px; border: 2px solid #28a745; border-radius: 8px; font-family: Arial, sans-serif; text-align: center; }
        .details { text-align: left; margin-top: 20px; line-height: 1.8; }
        .status { font-weight: bold; color: #17a2b8; }
    </style>
</head>
<body>

<div class="confirmation-card">
    <h2 style="color: #28a745;">Booking Confirmed!</h2>
    <p>Your appointment has been successfully booked.</p>

    <div class="details">
        <p><strong>Appointment ID:</strong> #<?php echo htmlspecialchars($booking['appointment_id']); ?></p>
        <p><strong>Patient Name:</strong> <?php echo htmlspecialchars($booking['patient_name']); ?></p>
        <p><strong>Doctor:</strong> <?php echo htmlspecialchars($booking['doctor_name']); ?></p>
        <p><strong>Scheduled Time:</strong> <?php echo htmlspecialchars($booking['appointment_time']); ?></p>
        <p><strong>Status:</strong> <span class="status"><?php echo htmlspecialchars($booking['status']); ?></span></p>
    </div>

    <br>
    <a href="book_appointment.php">Book Another Appointment</a>
</div>

</body>
</html>