<?php
session_start();

// Include the database connection
require_once('../../database/connection.php');
include_once('../components/header.php');

// Initialize variables
$student_achievement_id = $achievement_type = $achievement_name = $points = $date = $time = $organizer = $rank = $details = '';
$errors = array();

// Retrieve the student achievement data to be updated (you might need to pass the student achievement ID to this page)
if (isset($_GET['id'])) {
    $student_achievement_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Query to fetch the existing student achievement data
    $query = "SELECT sa.*, ma.AchievementType, ma.AchievementName, ma.Points, sa.AchievementName AS EditableAchievementName
              FROM StudentAchievements sa
              JOIN MasterAchievements ma ON sa.AchievementID = ma.AchievementID
              WHERE sa.StudentAchievementID = ? LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $student_achievement_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student_achievement = $result->fetch_assoc();

    // Check if the student achievement exists
    if (!$student_achievement) {
        // Student achievement not found, handle accordingly (e.g., redirect to an error page)
    } else {
        // Populate variables with existing student achievement data
        $achievement_type = $student_achievement['AchievementType'];
        $achievement_name = $student_achievement['AchievementName'];
        $editable_achievement_name = $student_achievement['EditableAchievementName'];
        $points = $student_achievement['Points'];
        $date = $student_achievement['Date'];
        $time = $student_achievement['Time'];
        $organizer = $student_achievement['Organizer'];
        $rank = $student_achievement['Rank'];
        $details = $student_achievement['Details'];
        // You can also retrieve other fields as needed
    }
}

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate the input data (similar to create student achievement form)
    $editable_achievement_name = mysqli_real_escape_string($conn, $_POST['editable_achievement_name']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $organizer = mysqli_real_escape_string($conn, $_POST['organizer']);
    $rank = mysqli_real_escape_string($conn, $_POST['rank']);
    $details = mysqli_real_escape_string($conn, $_POST['details']);
    // You should validate the fields and handle errors as needed

    // Update student achievement data in the database
    $query = "UPDATE StudentAchievements 
          SET Date = ?, Time = ?, Organizer = ?, Rank = ?, Details = ?, AchievementName = ? 
          WHERE StudentAchievementID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "sssssss",
        $date,
        $time,
        $organizer,
        $rank,
        $details,
        $editable_achievement_name,
        $student_achievement_id
    );


    if ($stmt->execute()) {
        // Student achievement update successful
        // Log the activity for student achievement update
        $activityDescription = "Student Achievement updated: $achievement_name";
        $currentUserID = $_SESSION['UserID'];
        insertLogActivity($conn, $currentUserID, $activityDescription);
        // Display success message or redirect to student achievements list
        echo '<script>
        Swal.fire({
            icon: "success",
            title: "Success",
            text: "Student Achievement update successfully.",
        }).then(function() {
            window.location.href = "manage_student_achievements_list.php";
        });
    </script>';
        exit();
    } else {
        // Student achievement update failed
        $errors['db_error'] = "Student Achievement update failed.";

        // Display error message or handle accordingly
        echo '<script>
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Student Achievement update failed.",
        });
    </script>';
    }
}

// Close the database connection
?>

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
                        <h1 class="text-3xl text-gray-800 font-semibold w-full">Update Student Achievement</h1>
                        <div class="flex flex-row justify-end items-center">
                            <a href="manage_student_achievements_list.php" class="bg-gray-800 hover-bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
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
                                <p class="text-gray-600 text-sm">Update student achievement information form.</p>
                            </div>
                        </div>
                        <!-- End Navigation -->
                        <!-- Student Achievement Update Form -->
                        <form action="" method="POST" class="flex flex-col w-full space-x-2">

                            <!-- Achievement Type -->
                            <label for="achievement_type" class="block font-semibold text-gray-800 mt-2 mb-2">Achievement Type
                                <span class="text-red-500">*</span></label>
                            <input type="text" id="achievement_type" name="achievement_type" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Achievement Type" value="<?php echo $achievement_type; ?>" disabled>
                            <?php if (isset($errors['achievement_type'])) : ?>
                                <p class="text-red-500 text-sm">
                                    <?php echo $errors['achievement_type']; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Achievement Name -->
                            <label for="achievement_name" class="block font-semibold text-gray-800 mt-2 mb-2">Achievement Name
                                <span class="text-red-500">*</span></label>
                            <input type="text" id="achievement_name" name="achievement_name" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Achievement Name" value="<?php echo $achievement_name; ?>" disabled>
                            <?php if (isset($errors['achievement_name'])) : ?>
                                <p class="text-red-500 text-sm">
                                    <?php echo $errors['achievement_name']; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Editable Achievement Name for StudentAchievement -->
                            <label for="editable_achievement_name" class="block font-semibold text-gray-800 mt-2 mb-2">Edit Achievement Name for Student Achievement
                                <span class="text-red-500">*</span></label>
                            <input type="text" id="editable_achievement_name" name="editable_achievement_name" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Achievement Name" value="<?php echo $editable_achievement_name; ?>">
                            <?php if (isset($errors['editable_achievement_name'])) : ?>
                                <p class="text-red-500 text-sm">
                                    <?php echo $errors['editable_achievement_name']; ?>
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

                            <!-- Organizer -->
                            <label for="organizer" class="block font-semibold text-gray-800 mt-2 mb-2">Organizer</label>
                            <input type="text" id="organizer" name="organizer" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Organizer" value="<?php echo $organizer; ?>">
                            <?php if (isset($errors['organizer'])) : ?>
                                <p class="text-red-500 text-sm">
                                    <?php echo $errors['organizer']; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Rank -->
                            <label for="rank" class="block font-semibold text-gray-800 mt-2 mb-2">Rank</label>
                            <input type="text" id="rank" name="rank" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Rank" value="<?php echo $rank; ?>">
                            <?php if (isset($errors['rank'])) : ?>
                                <p class="text-red-500 text-sm">
                                    <?php echo $errors['rank']; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Details -->
                            <label for="details" class="block font-semibold text-gray-800 mt-2 mb-2">Details</label>
                            <textarea id="details" name="details" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Details"><?php echo $details; ?></textarea>
                            <?php if (isset($errors['details'])) : ?>
                                <p class="text-red-500 text-sm">
                                    <?php echo $errors['details']; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Submit Button -->
                            <button type="submit" class="bg-green-500 hover-bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center mt-4 text-center">
                                <i class="fas fa-check mr-2"></i>
                                <span>Update Student Achievement</span>
                            </button>
                        </form>
                        <!-- End Student Achievement Update Form -->
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