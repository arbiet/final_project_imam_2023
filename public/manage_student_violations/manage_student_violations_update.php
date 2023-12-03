<?php
// Start a session to manage user data across pages
session_start();

// Include the database connection file
require_once('../../database/connection.php');

// Include the header component
include_once('../components/header.php');

// Initialize variables
$student_violation_id = $violation_type = $violation_name = $points = $date = $time = '';
$errors = array();

// Retrieve the student violation data to be updated (you might need to pass the student violation ID to this page)
if (isset($_GET['id'])) {
    $student_violation_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Query to fetch the existing student violation data
    $query = "SELECT sv.*, mv.ViolationType, mv.ViolationName, mv.Points
          FROM StudentViolations sv
          JOIN MasterViolations mv ON sv.ViolationID = mv.ViolationID
          WHERE sv.StudentViolationID = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $student_violation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student_violation = $result->fetch_assoc();

    // Check if the student violation exists
    if (!$student_violation) {
        // Student violation not found, handle accordingly (e.g., redirect to an error page)
    } else {
        // Populate variables with existing student violation data
        $violation_type = $student_violation['ViolationType'];
        $violation_name = $student_violation['ViolationName'];
        $points = $student_violation['Points'];
        $date = $student_violation['Date'];
        $time = $student_violation['Time'];
        // You can also retrieve other fields as needed
    }
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate the input data (similar to create student violation form)
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    // You should validate the fields and handle errors as needed

    // Update student violation data in the database
    $query = "UPDATE StudentViolations 
          SET Date = ?, Time = ?
          WHERE StudentViolationID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "sss",
        $date,
        $time,
        $student_violation_id
    );

    if ($stmt->execute()) {
        // Student violation update successful
        // Log the activity for student violation update
        $activityDescription = "Student Violation updated: $student_violation_id";
        $currentUserID = $_SESSION['UserID'];
        insertLogActivity($conn, $currentUserID, $activityDescription);
        // Display success message or redirect to student violations list
        echo '<script>
        Swal.fire({
            icon: "success",
            title: "Success",
            text: "Student Violation update successfully.",
        }).then(function() {
            window.location.href = "manage_student_violations_list.php";
        });
    </script>';
        exit();
    } else {
        // Student violation update failed
        $errors['db_error'] = "Student Violation update failed.";

        // Display error message or handle accordingly
        echo '<script>
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Student Violation update failed.",
        });
    </script>';
    }
}

// Close the database connection
?>

<!-- Your HTML and form elements go here, similar to the manage_student_achievements_update.php file -->

<body class="bg-gray-50">
    <div class="h-screen flex flex-col">
        <!-- Top Navbar -->
        <?php include('../components/navbar.php'); ?>
        <!-- End Top Navbar -->

        <!-- Main Content -->
        <div class="flex-grow bg-gray-50 flex flex-row shadow-md">
            <!-- Sidebar -->
            <?php include('../components/sidebar.php'); ?>
            <!-- End Sidebar -->

            <!-- Main Content -->
            <main class="bg-gray-50 flex flex-col flex-1 overflow-y-scroll h-screen flex-shrink-0 sc-hide pb-40">
                <div class="flex items-start justify-start p-6 shadow-md m-4 flex-1 flex-col">
                    <!-- Header Content -->
                    <div class="flex flex-row justify-between items-center w-full border-b-2 border-gray-600 mb-2 pb-2">
                        <h1 class="text-3xl text-gray-800 font-semibold w-full">Update Student Violation</h1>
                        <div class="flex flex-row justify-end items-center">
                            <a href="manage_student_violations_list.php" class="bg-gray-800 hover-bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
                                <i class="fas fa-arrow-left"></i>
                                <span>Back</span>
                            </a>
                        </div>
                    </div>
                    <!-- End Header Content -->

                    <!-- Content -->
                    <div class="flex flex-col w-full">
                        <!-- Navigation -->
                        <div class="flex flex-row justify-between items-center w-full pb-2">
                            <div>
                                <h2 class="text-lg text-gray-800 font-semibold">Welcome back, <?php echo $_SESSION['FullName']; ?>!</h2>
                                <p class="text-gray-600 text-sm">Update student violation information form.</p>
                            </div>
                        </div>
                        <!-- End Navigation -->

                        <!-- Student Violation Update Form -->
                        <form action="" method="POST" class="flex flex-col w-full space-x-2">

                            <!-- Violation Type -->
                            <label for="violation_type" class="block font-semibold text-gray-800 mt-2 mb-2">Violation Type
                                <span class="text-red-500">*</span></label>
                            <input type="text" id="violation_type" name="violation_type" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Violation Type" value="<?php echo $violation_type; ?>" disabled>
                            <?php if (isset($errors['violation_type'])) : ?>
                                <p class="text-red-500 text-sm">
                                    <?php echo $errors['violation_type']; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Violation Name -->
                            <label for="violation_name" class="block font-semibold text-gray-800 mt-2 mb-2">Violation Name
                                <span class="text-red-500">*</span></label>
                            <input type="text" id="violation_name" name="violation_name" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Violation Name" value="<?php echo $violation_name; ?>" disabled>
                            <?php if (isset($errors['violation_name'])) : ?>
                                <p class="text-red-500 text-sm">
                                    <?php echo $errors['violation_name']; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Points -->
                            <label for="points" class="block font-semibold text-gray-800 mt-2 mb-2">Points
                                <span class="text-red-500">*</span></label>
                            <input type="text" id="points" name="points" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Points" value="<?php echo $points; ?>" disabled>
                            <?php if (isset($errors['points'])) : ?>
                                <p class="text-red-500 text-sm">
                                    <?php echo $errors['points']; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Date -->
                            <label for="date" class="block font-semibold text-gray-800 mt-2 mb-2">Date
                                <span class="text-red-500">*</span></label>
                            <input type="date" id="date" name="date" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" value="<?php echo $date; ?>">
                            <?php if (isset($errors['date'])) : ?>
                                <p class="text-red-500 text-sm">
                                    <?php echo $errors['date']; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Time -->
                            <label for="time" class="block font-semibold text-gray-800 mt-2 mb-2">Time
                                <span class="text-red-500">*</span></label>
                            <input type="time" id="time" name="time" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" value="<?php echo $time; ?>">
                            <?php if (isset($errors['time'])) : ?>
                                <p class="text-red-500 text-sm">
                                    <?php echo $errors['time']; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Submit Button -->
                            <button type="submit" class="bg-green-500 hover-bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center mt-4 text-center">
                                <i class="fas fa-check mr-2"></i>
                                <span>Update Student Violation</span>
                            </button>
                        </form>
                        <!-- End Student Violation Update Form -->
                    </div>
                    <!-- End Content -->
                </div>
            </main>
            <!-- End Main Content -->
        </div>
        <!-- End Main Content -->

        <!-- Footer -->
        <?php include('../components/footer.php'); ?>
        <!-- End Footer -->
    </div>
    <!-- End Main Content -->
</body>

</html>