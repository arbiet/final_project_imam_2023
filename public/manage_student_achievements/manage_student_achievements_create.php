<?php
session_start();

// Include the database connection
require_once('../../database/connection.php');
include_once('../components/header.php');

// Initialize variables
$achievement_type = $achievement_name = $points = $date = $time = $organizer = $rank = $details = '';
$errors = array();

// Fetch student data for the dropdown
$queryStudents = "SELECT s.StudentID, u.FullName
                  FROM Students s
                  JOIN Users u ON s.UserID = u.UserID";
$resultStudents = $conn->query($queryStudents);

// Fetch master achievements data for the dropdown
$queryMasterAchievements = "SELECT AchievementID, CONCAT(AchievementType, ' - ', AchievementName) AS AchievementInfo
                            FROM MasterAchievements";
$resultMasterAchievements = $conn->query($queryMasterAchievements);

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate the input data
    $achievement_name = mysqli_real_escape_string($conn, $_POST['achievement_name']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);
    $organizer = mysqli_real_escape_string($conn, $_POST['organizer']);
    $rank = mysqli_real_escape_string($conn, $_POST['rank']);
    $details = mysqli_real_escape_string($conn, $_POST['details']);
    $student_id = isset($_POST['student_id']) ? (int)$_POST['student_id'] : 0;
    $achievement_id = isset($_POST['achievement_id']) ? (int)$_POST['achievement_id'] : 0;

    // Check for required fields
    if (
        empty($achievement_name) || empty($points) || empty($date) || empty($time) || empty($student_id) || empty($achievement_id)
    ) {
        $errors['required_fields'] = "All fields are required.";
    }


    // Additional validations for date and time
    if (!validateDate($date)) {
        $errors['date'] = "Invalid date format.";
    }

    if (!validateTime($time)) {
        $errors['time'] = "Invalid time format.";
    }

    // If there are no errors, insert the data into the database
    if (empty($errors)) {
        $query = "INSERT INTO StudentAchievements (StudentID, AchievementID, Date, Time, AchievementName, Organizer, Rank, Details)
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iissssss", $student_id, $achievement_id, $date, $time, $achievement_name, $organizer, $rank, $details);


        if ($stmt->execute()) {
            // Achievement creation successful
            // Log the activity for achievement creation
            $activityDescription = "Student Achievement created: $achievement_name";
            $currentUserID = $_SESSION['UserID'];
            insertLogActivity($conn, $currentUserID, $activityDescription);

            // Display success message and redirect
            echo '<script>
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: "Student Achievement created successfully.",
                }).then(function() {
                    window.location.href = "manage_student_achievements_list.php";
                });
            </script>';
            exit();
        } else {
            // Achievement creation failed
            $errors['db_error'] = "Student Achievement creation failed.";

            // Display error message
            echo '<script>
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: "Student Achievement creation failed.",
                });
            </script>';
        }
    }
}

// Function to validate date format
function validateDate($date)
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

// Function to validate time format
function validateTime($time)
{
    $t = DateTime::createFromFormat('H:i', $time);
    return $t && $t->format('H:i') === $time;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Include your head content here -->
</head>

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
                        <h1 class="text-3xl text-gray-800 font-semibold w-full">Create Student Achievement</h1>
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
                                <p class="text-gray-600 text-sm">Student Achievement creation form.</p>
                            </div>
                        </div>
                        <!-- End Navigation -->

                        <!-- Student Achievement Creation Form -->
                        <form action="" method="POST" class="flex flex-col w-full space-x-2">

                            <!-- Student Selector -->
                            <label for="student_id" class="block font-semibold text-gray-800 mt-2 mb-2">Select Student <span class="text-red-500">*</span></label>
                            <select id="student_id" name="student_id" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600">
                                <?php while ($rowStudent = $resultStudents->fetch_assoc()) : ?>
                                    <option value="<?php echo $rowStudent['StudentID']; ?>"><?php echo $rowStudent['FullName']; ?></option>
                                <?php endwhile; ?>
                            </select>
                            <?php if (isset($errors['student_id'])) : ?>
                                <p class="text-red-500 text-sm">
                                    <?php echo $errors['student_id']; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Master Achievement Selector -->
                            <label for="achievement_id" class="block font-semibold text-gray-800 mt-2 mb-2">Select Master Achievement <span class="text-red-500">*</span></label>
                            <select id="achievement_id" name="achievement_id" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600">
                                <?php while ($rowAchievement = $resultMasterAchievements->fetch_assoc()) : ?>
                                    <option value="<?php echo $rowAchievement['AchievementID']; ?>"><?php echo $rowAchievement['AchievementInfo']; ?></option>
                                <?php endwhile; ?>
                            </select>
                            <?php if (isset($errors['achievement_id'])) : ?>
                                <p class="text-red-500 text-sm">
                                    <?php echo $errors['achievement_id']; ?>
                                </p>
                            <?php endif; ?>

                            <!-- Achievement Name -->
                            <label for="achievement_name" class="block font-semibold text-gray-800 mt-2 mb-2">Achievement Name
                                <span class="text-red-500">*</span></label>
                            <input type="text" id="achievement_name" name="achievement_name" class="w-full rounded-md border-gray-300 px-2 py-2 border text-gray-600" placeholder="Achievement Name" value="<?php echo $achievement_name; ?>">
                            <?php if (isset($errors['achievement_name'])) : ?>
                                <p class="text-red-500 text-sm">
                                    <?php echo $errors['achievement_name']; ?>
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
                            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded inline-flex items-center mt-4 text-center">
                                <i class="fas fa-check mr-2"></i>
                                <span>Create Student Achievement</span>
                            </button>
                        </form>
                        <!-- End Student Achievement Creation Form -->
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