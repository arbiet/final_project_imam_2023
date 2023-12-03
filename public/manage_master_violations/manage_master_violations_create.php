<?php
session_start();

// Include the database connection
require_once('../../database/connection.php');
include_once('../components/header.php');

// Initialize variables
$violation_type = $violation_name = $points = '';
$errors = array();

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate the input data
    $violation_type = mysqli_real_escape_string($conn, $_POST['violation_type']);
    $violation_name = mysqli_real_escape_string($conn, $_POST['violation_name']);
    $points = mysqli_real_escape_string($conn, $_POST['points']);

    // Check for errors
    if (empty($violation_type)) {
        $errors['violation_type'] = "Violation Type is required.";
    }

    if (empty($violation_name)) {
        $errors['violation_name'] = "Violation Name is required.";
    }

    if (empty($points)) {
        $errors['points'] = "Points are required.";
    }

    // If there are no errors, insert the data into the database
    if (empty($errors)) {
        $query = "INSERT INTO MasterViolations (ViolationType, ViolationName, Points)
                  VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $violation_type, $violation_name, $points);

        if ($stmt->execute()) {
            // Violation creation successful
            // Log the activity for violation creation
            $activityDescription = "Violation created: $violation_name, Points: $points";
            $currentUserID = $_SESSION['UserID'];
            insertLogActivity($conn, $currentUserID, $activityDescription);

            // Display success message and redirect
            echo '<script>
        Swal.fire({
            icon: "success",
            title: "Success",
            text: "Violation created successfully.",
        }).then(function() {
            window.location.href = "manage_master_violations_list.php";
        });
    </script>';
            exit();
        } else {
            // Violation creation failed
            $errors['db_error'] = "Violation creation failed.";

            // Display error message
            echo '<script>
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Violation creation failed.",
        });
    </script>';
        }
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
                    <h1 class="text-3xl text-gray-800 font-semibold w-full">Create Violation</h1>
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
                            <p class="text-gray-600 text-sm">Violation creation form.</p>
                        </div>
                    </div>
                    <!-- End Navigation -->
                    <!-- Violation Creation Form -->
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
                        <input type="number" id="points" name="points" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Points" value="<?php echo $points; ?>">
                        <?php if (isset($errors['points'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['points']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Submit Button -->
                        <button type="submit" class="bg-green-500 hover-bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center mt-4 text-center">
                            <i class="fas fa-check mr-2"></i>
                            <span>Create Violation</span>
                        </button>
                    </form>
                    <!-- End Violation Creation Form -->
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