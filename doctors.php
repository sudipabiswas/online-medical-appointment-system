<?php
session_start();
include "config.php"; // Database connection

// Capture search query if user submitted search form
$search_query = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = trim($_GET['search']);
}

// Fetch doctors from database matching search term
try {
    if (!empty($search_query)) {
        // Search doctors by name or email
        $stmt = mysqli_prepare($conn, "SELECT user_id, full_name, email FROM users WHERE role = 'Doctor' AND (full_name LIKE ? OR email LIKE ?)");
        $param_search = "%" . $search_query . "%";
        mysqli_stmt_bind_param($stmt, "ss", $param_search, $param_search);
        mysqli_stmt_execute($stmt);
        $doctors_result = mysqli_stmt_get_result($stmt);
    } else {
        // Fetch all registered doctors
        $sql = "SELECT user_id, full_name, email FROM users WHERE role = 'Doctor'";
        $doctors_result = mysqli_query($conn, $sql);
    }
} catch (Exception $e) {
    $doctors_result = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthSuite Portal - Doctor Directory</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
      .card-transition {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-800 antialiased min-h-screen py-10">

    <div class="max-w-6xl mx-auto px-4">
        
        <!-- Header & Search Bar Section -->
        <div class="bg-white rounded-xl shadow-md p-8 border border-gray-200 mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-blue-900">Doctor Directory</h1>
                    <p class="text-gray-500 text-sm mt-1">Browse our specialist medical practitioners and book consultations.</p>
                </div>

                <!-- Search Form -->
                <form action="doctors.php" method="GET" class="flex items-center space-x-2">
                    <div class="relative w-full md:w-72">
                        <input 
                            type="text" 
                            name="search" 
                            value="<?php echo htmlspecialchars($search_query); ?>" 
                            placeholder="Search doctor by name..." 
                            class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-600 focus:border-transparent outline-none text-sm"
                        />
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-3.5 text-gray-400 text-sm"></i>
                    </div>
                    <button 
                        type="submit" 
                        class="bg-blue-900 hover:bg-blue-800 text-white font-semibold px-5 py-2.5 rounded-lg text-sm transition shadow-sm">
                        Search
                    </button>
                    <?php if (!empty($search_query)): ?>
                        <a href="doctors.php" class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold px-3 py-2.5 rounded-lg text-sm transition">
                            Reset
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- Doctor Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <?php if ($doctors_result && mysqli_num_rows($doctors_result) > 0): ?>
                <?php while ($doctor = mysqli_fetch_assoc($doctors_result)): ?>
                    
                    <!-- Doctor Profile Card -->
                    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 flex flex-col justify-between hover:shadow-xl card-transition">
                        <div>
                            <!-- Avatar & Title -->
                            <div class="flex items-center space-x-4 mb-4">
                                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center text-blue-900 font-bold text-2xl border-2 border-blue-900">
                                    <i class="fa-solid fa-user-doctor"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">
                                        <?php echo htmlspecialchars($doctor['full_name']); ?>
                                    </h2>
                                    <span class="inline-block bg-blue-50 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full mt-1 border border-blue-200">
                                        Medical Specialist
                                    </span>
                                </div>
                            </div>

                            <hr class="my-4 border-gray-100">

                            <!-- Details -->
                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-envelope text-blue-900 w-5"></i>
                                    <span><?php echo htmlspecialchars($doctor['email']); ?></span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-hospital text-blue-900 w-5"></i>
                                    <span>HealthSuite Medical Center</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fa-solid fa-circle-check text-green-600 w-5"></i>
                                    <span class="text-green-700 font-medium">Available for Appointments</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="mt-6">
                            <a href="book_appointment.php?doctor_id=<?php echo $doctor['user_id']; ?>" 
                               class="block w-full text-center bg-blue-900 hover:bg-blue-800 text-white font-bold py-2.5 rounded-lg text-sm transition shadow-sm">
                                Book Consultation
                            </a>
                        </div>
                    </div>

                <?php endwhile; ?>
            <?php else: ?>
                <!-- Empty Search Result Alert -->
                <div class="col-span-full bg-white rounded-xl shadow-md p-10 text-center border border-gray-200">
                    <i class="fa-solid fa-user-slash text-4xl text-gray-300 mb-3"></i>
                    <h3 class="text-lg font-bold text-gray-700">No Doctors Found</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        <?php echo !empty($search_query) ? "No doctor matches '" . htmlspecialchars($search_query) . "'" : "There are currently no doctor accounts registered."; ?>
                    </p>
                </div>
            <?php endif; ?>

        </div>

        <!-- Navigation Link -->
        <div class="text-center mt-10">
            <a href="home.php" class="text-sm text-blue-700 hover:text-blue-900 font-semibold">
                <i class="fa-solid fa-arrow-left mr-1"></i> Return to Main Homepage
            </a>
        </div>

    </div>

</body>
</html>