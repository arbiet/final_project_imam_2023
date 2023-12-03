<?php
session_start();

// Include the database connection
require_once('../../database/connection.php');
include_once('../components/header.php');

// Initialize variables
$achievement_id = $achievement_type = $achievement_name = $points = '';
$errors = array();

// Retrieve the achievement data to be updated (you might need to pass the achievement ID to this page)
if (isset($_GET['id'])) {
    $achievement_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Query to fetch the existing achievement data
    $query = "SELECT * FROM MasterAchievements WHERE AchievementID = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $achievement_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $achievement = $result->fetch_assoc();

    // Check if the achievement exists
    if (!$achievement) {
        // Achievement not found, handle accordingly (e.g., redirect to an error page)
    } else {
        // Populate variables with existing achievement data
        $achievement_type = $achievement['AchievementType'];
        $achievement_name = $achievement['AchievementName'];
        $points = $achievement['Points'];
        // You can also retrieve other fields as needed
    }
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate the input data (similar to create achievement form)
    $achievement_type = mysqli_real_escape_string($conn, $_POST['achievement_type']);
    $achievement_name = mysqli_real_escape_string($conn, $_POST['achievement_name']);
    $points = mysqli_real_escape_string($conn, $_POST['points']);
    // You should validate the fields and handle errors as needed

    // Update achievement data in the database
    $query = "UPDATE MasterAchievements 
              SET AchievementType = ?, AchievementName = ?, Points = ? 
              WHERE AchievementID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $achievement_type, $achievement_name, $points, $achievement_id);

    if ($stmt->execute()) {
        // Achievement update successful
        // Log the activity for achievement update
        $activityDescription = "Achievement updated: $achievement_name";
        $currentUserID = $_SESSION['UserID'];
        insertLogActivity($conn, $currentUserID, $activityDescription);
        // Display success message or redirect to achievements list
        echo '<script>
        Swal.fire({
            icon: "success",
            title: "Success",
            text: "Achievement update successfully.",
        }).then(function() {
            window.location.href = "manage_master_achievements_list.php";
        });
    </script>';
        exit();
    } else {
        // Achievement update failed
        $errors['db_error'] = "Achievement update failed.";

        // Display error message or handle accordingly
        echo '<script>
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Achievement update failed.",
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
                    <h1 class="text-3xl text-gray-800 font-semibold w-full">Update Achievement</h1>
                    <div class="flex flex-row justify-end items-center">
                        <a href="manage_master_achievements_list.php" class="bg-gray-800 hover-bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
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
                            <p class="text-gray-600 text-sm">Update achievement information form.</p>
                        </div>
                    </div>
                    <!-- End Navigation -->
                    <!-- Achievement Update Form -->
                    <form action="" method="POST" class="flex flex-col w-full space-x-2">
                        <!-- Achievement Type -->
                        <label for="achievement_type" class="block font-semibold text-gray-800 mt-2 mb-2">Achievement Type <span class="text-red-500">*</span></label>
                        <input type="text" id="achievement_type" name="achievement_type" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Achievement Type" value="<?php echo $achievement_type; ?>">
                        <?php if (isset($errors['achievement_type'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['achievement_type']; ?>
                            </p>
                        <?php endif; ?>

                        <!-- Achievement Name -->
                        <label for="achievement_name" class="block font-semibold text-gray-800 mt-2 mb-2">Achievement Name <span class="text-red-500">*</span></label>
                        <input type="text" id="achievement_name" name="achievement_name" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Achievement Name" value="<?php echo $achievement_name; ?>">
                        <?php if (isset($errors['achievement_name'])) : ?>
                            <p class="text-red-500 text-sm">
                                <?php echo $errors['achievement_name']; ?>
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
                            <span>Update Achievement</span>
                        </button>
                    </form>
                    <!-- End Achievement Update Form -->
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