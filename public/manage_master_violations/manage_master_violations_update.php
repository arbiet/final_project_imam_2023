<?php
session_start();

// Include the database connection
require_once('../../database/connection.php');
include_once('../components/header.php');

// Initialize variables
$violation_id = $violation_type = $violation_name = $points = '';
$errors = array();

// Retrieve the violation data to be updated (you might need to pass the violation ID to this page)
if (isset($_GET['id'])) {
    $violation_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Query to fetch the existing violation data
    $query = "SELECT * FROM MasterViolations WHERE ViolationID = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $violation_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $violation = $result->fetch_assoc();

    // Check if the violation exists
    if (!$violation) {
        // Violation not found, handle accordingly (e.g., redirect to an error page)
    } else {
        // Populate variables with existing violation data
        $violation_type = $violation['ViolationType'];
        $violation_name = $violation['ViolationName'];
        $points = $violation['Points'];
        // You can also retrieve other fields as needed
    }
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate the input data (similar to create violation form)
    $violation_type = mysqli_real_escape_string($conn, $_POST['violation_type']);
    $violation_name = mysqli_real_escape_string($conn, $_POST['violation_name']);
    $points = mysqli_real_escape_string($conn, $_POST['points']);
    // You should validate the fields and handle errors as needed

    // Update violation data in the database
    $query = "UPDATE MasterViolations 
              SET ViolationType = ?, ViolationName = ?, Points = ? 
              WHERE ViolationID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $violation_type, $violation_name, $points, $violation_id);

    if ($stmt->execute()) {
        // Violation update successful
        // Log the activity for violation update
        $activityDescription = "Violation updated: $violation_name";
        $currentUserID = $_SESSION['UserID'];
        insertLogActivity($conn, $currentUserID, $activityDescription);
        // Display success message or redirect to violations list
        echo '<script>
        Swal.fire({
            icon: "success",
            title: "Success",
            text: "Violation update successfully.",
        }).then(function() {
            window.location.href = "manage_master_violations_list.php";
        });
    </script>';
        exit();
    } else {
        // Violation update failed
        $errors['db_error'] = "Violation update failed.";

        // Display error message or handle accordingly
        echo '<script>
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Violation update failed.",
        });
    </script>';
    }
}

// Close the database connection
?>

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
                    <h1 class="text-3xl text-gray-800 font-semibold w-full">Update Violation</h1>
                    <div class="flex flex-row justify-end items-center">
                        <a href="manage_master_violations_list.php" class="bg-gray-800 hover-bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
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
                            <p class="text-gray-600 text-sm">Update violation information form.</p>
                        </div>
                    </div>
                    <!-- End Navigation -->
                    <!-- Violation Update Form -->
                    <form action="" method="POST" class="flex flex-col w-full space-x-2">
                        <!-- Violation Type -->
                        <label for="violation_type" class="block font-semibold text-gray-800 mt-2 mb-2">Violation Type <span class="text-red-500">*</span></label>
                        <input type="text" id="violation_type" name="violation_type" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Violation Type" value="<?php echo $violation_type; ?>">
                        <?php if (isset($errors['violation_type'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['violation_type']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Violation Name -->
                        <label for="violation_name" class="block font-semibold text-gray-800 mt-2 mb-2">Violation Name <span class="text-red-500">*</span></label>
                        <input type="text" id="violation_name" name="violation_name" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Violation Name" value="<?php echo $violation_name; ?>">
                        <?php if (isset($errors['violation_name'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['violation_name']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Points -->
                        <label for="points" class="block font-semibold text-gray-800 mt-2 mb-2">Points <span class="text-red-500">*</span></label>
                        <input type="text" id="points" name="points" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Points" value="<?php echo $points; ?>">
                        <?php if (isset($errors['points'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['points']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Submit Button -->
                        <button type="submit" class="bg-green-500 hover-bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center mt-4 text-center">
                            <i class="fas fa-check mr-2"></i>
                            <span>Update Violation</span>
                        </button>
                    </form>
                    <!-- End Violation Update Form -->
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