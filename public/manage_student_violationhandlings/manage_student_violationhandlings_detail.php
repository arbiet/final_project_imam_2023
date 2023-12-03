<?php
// Initialize the session
session_start();
// manage_student_violationhandlings_detail.php
// Include the connection file
require_once('../../database/connection.php');

// Get the student ID from the URL parameter
$studentID = isset($_GET['student_id']) ? intval($_GET['student_id']) : null;

// Validate the student ID
if (!$studentID) {
    // Handle invalid student ID (redirect or display an error message)
    header("Location: manage_student_violationhandlings_list.php");
    exit();
}

// Query to get details of the selected student
$query = "SELECT Students.StudentID,
                 Students.StudentNumber,
                 Users.FullName AS StudentName
          FROM Students
          JOIN Users ON Students.UserID = Users.UserID
          WHERE Students.StudentID = $studentID";

$result = mysqli_query($conn, $query);

// Fetch student details
$studentDetails = mysqli_fetch_assoc($result);

// Query to get violations for the selected student
$queryViolations = "SELECT StudentViolations.Date, MasterViolations.ViolationName, MasterViolations.Points
                    FROM StudentViolations
                    JOIN MasterViolations ON StudentViolations.ViolationID = MasterViolations.ViolationID
                    WHERE StudentViolations.StudentID = $studentID";

$resultViolations = mysqli_query($conn, $queryViolations);

// Query to get achievements for the selected student
$queryAchievements = "SELECT StudentAchievements.Date, MasterAchievements.AchievementName, MasterAchievements.Points
                      FROM StudentAchievements
                      JOIN MasterAchievements ON StudentAchievements.AchievementID = MasterAchievements.AchievementID
                      WHERE StudentAchievements.StudentID = $studentID";

$resultAchievements = mysqli_query($conn, $queryAchievements);
?>

<?php include_once('../components/header.php'); ?>

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
                    <h1 class="text-3xl text-gray-800 font-semibold w-full">Student Detail</h1>
                    <div class="flex flex-row justify-end items-center">
                        <a href="manage_student_violationhandlings_list.php" class="bg-gray-800 hover-bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
                            <i class="fas fa-arrow-left"></i>
                            <span>Back</span>
                        </a>
                    </div>
                </div>
                <!-- End Header Content -->
                <!-- Content -->
                <div class="flex flex-col w-full">
                    <!-- Display Student Details -->
                    <h2 class="text-xl font-semibold mb-2">Student Details</h2>
                    <p><strong>Student Number:</strong> <?php echo $studentDetails['StudentNumber']; ?></p>
                    <p><strong>Student Name:</strong> <?php echo $studentDetails['StudentName']; ?></p>
                    <!-- End Display Student Details -->

                    <!-- Display Violations -->
                    <h2 class="text-xl font-semibold mb-2">Violations</h2>
                    <table class="min-w-full bg-white border border-gray-300">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Date</th>
                                <th class="py-2 px-4 border-b">Violation Name</th>
                                <th class="py-2 px-4 border-b">Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = mysqli_fetch_assoc($resultViolations)) {
                                echo '<tr>';
                                echo '<td class="py-2 px-4 border-b">' . $row['Date'] . '</td>';
                                echo '<td class="py-2 px-4 border-b">' . $row['ViolationName'] . '</td>';
                                echo '<td class="py-2 px-4 border-b">' . $row['Points'] . '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    <!-- End Display Violations -->

                    <!-- Display Achievements -->
                    <h2 class="text-xl font-semibold mb-2">Achievements</h2>
                    <table class="min-w-full bg-white border border-gray-300">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Date</th>
                                <th class="py-2 px-4 border-b">Achievement Name</th>
                                <th class="py-2 px-4 border-b">Points</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = mysqli_fetch_assoc($resultAchievements)) {
                                echo '<tr>';
                                echo '<td class="py-2 px-4 border-b">' . $row['Date'] . '</td>';
                                echo '<td class="py-2 px-4 border-b">' . $row['AchievementName'] . '</td>';
                                echo '<td class="py-2 px-4 border-b">' . $row['Points'] . '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    <!-- End Display Achievements -->

                </div>
                <!-- End Content -->
            </div>
        </main>
        <!-- End Main Content -->
    </div>
</div>
<!-- End Main Content -->

</body>

<?php
// Close the database connection
mysqli_close($conn);
?>