<?php
session_start();

// Include the database connection
require_once('../../database/connection.php');
include_once('../components/header.php');

// Initialize variables
$violation_category = $score_range_bottom = $score_range_top = $follow_up_action = '';
$errors = array();

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate the input data
    $violation_category = mysqli_real_escape_string($conn, $_POST['violation_category']);
    $score_range_bottom = mysqli_real_escape_string($conn, $_POST['score_range_bottom']);
    $score_range_top = mysqli_real_escape_string($conn, $_POST['score_range_top']);
    $follow_up_action = mysqli_real_escape_string($conn, $_POST['follow_up_action']);

    // Check for errors
    if (empty($violation_category)) {
        $errors['violation_category'] = "Violation Category is required.";
    }

    if (empty($score_range_bottom)) {
        $errors['score_range_bottom'] = "Score Range Bottom is required.";
    }

    if (empty($score_range_top)) {
        $errors['score_range_top'] = "Score Range Top is required.";
    }

    if (empty($follow_up_action)) {
        $errors['follow_up_action'] = "Follow-Up Action is required.";
    }

    // If there are no errors, insert the data into the database
    if (empty($errors)) {
        $query = "INSERT INTO MasterViolationHandlings (ViolationCategory, ScoreRangeBottom, ScoreRangeTop, FollowUpAction)
                  VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("siss", $violation_category, $score_range_bottom, $score_range_top, $follow_up_action);

        if ($stmt->execute()) {
            // Violation handling creation successful
            // Log the activity for violation handling creation
            $activityDescription = "Violation handling created: $violation_category, Score Range: $score_range_bottom-$score_range_top, Follow-Up Action: $follow_up_action";
            $currentUserID = $_SESSION['UserID'];
            insertLogActivity($conn, $currentUserID, $activityDescription);

            // Display success message and redirect
            echo '<script>
        Swal.fire({
            icon: "success",
            title: "Success",
            text: "Violation handling created successfully.",
        }).then(function() {
            window.location.href = "manage_master_violationhandlings_list.php";
        });
    </script>';
            exit();
        } else {
            // Violation handling creation failed
            $errors['db_error'] = "Violation handling creation failed.";

            // Display error message
            echo '<script>
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Violation handling creation failed.",
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
                    <h1 class="text-3xl text-gray-800 font-semibold w-full">Create Violation Handling</h1>
                    <div class="flex flex-row justify-end items-center">
                        <a href="manage_master_violationhandlings_list.php" class="bg-gray-800 hover-bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
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
                            <p class="text-gray-600 text-sm">Violation handling creation form.</p>
                        </div>
                    </div>
                    <!-- End Navigation -->
                    <!-- Violation Handling Creation Form -->
                    <form action="" method="POST" class="flex flex-col w-full space-x-2">
                        <!-- Violation Category -->
                        <label for="violation_category" class="block font-semibold text-gray-800 mt-2 mb-2">Violation Category <span class="text-red-500">*</span></label>
                        <input type="text" id="violation_category" name="violation_category" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Violation Category" value="<?php echo $violation_category; ?>">
                        <?php if (isset($errors['violation_category'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['violation_category']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Score Range Bottom -->
                        <label for="score_range_bottom" class="block font-semibold text-gray-800 mt-2 mb-2">Score Range Bottom <span class="text-red-500">*</span></label>
                        <input type="number" id="score_range_bottom" name="score_range_bottom" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Score Range Bottom" value="<?php echo $score_range_bottom; ?>">
                        <?php if (isset($errors['score_range_bottom'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['score_range_bottom']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Score Range Top -->
                        <label for="score_range_top" class="block font-semibold text-gray-800 mt-2 mb-2">Score Range Top <span class="text-red-500">*</span></label>
                        <input type="number" id="score_range_top" name="score_range_top" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Score Range Top" value="<?php echo $score_range_top; ?>">
                        <?php if (isset($errors['score_range_top'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['score_range_top']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Follow-Up Action -->
                        <label for="follow_up_action" class="block font-semibold text-gray-800 mt-2 mb-2">Follow-Up Action <span class="text-red-500">*</span></label>
                        <input type="text" id="follow_up_action" name="follow_up_action" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Follow-Up Action" value="<?php echo $follow_up_action; ?>">
                        <?php if (isset($errors['follow_up_action'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['follow_up_action']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Submit Button -->
                        <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center mt-4 text-center">
                            <i class="fas fa-check mr-2"></i>
                            <span>Create Violation Handling</span>
                        </button>
                    </form>
                    <!-- End Violation Handling Creation Form -->
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