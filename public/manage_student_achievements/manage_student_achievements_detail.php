<?php
// Initialize the session
session_start();

// Include the connection file
require_once('../../database/connection.php');

// Initialize variables
$studentAchievementID = '';
$errors = array();
$studentAchievementData = array();

// Retrieve student achievement data
if (isset($_GET['id'])) {
    $studentAchievementID = $_GET['id'];
    $query = "SELECT sa.*, u.FullName AS StudentName, ma.AchievementType, ma.Points
              FROM StudentAchievements sa
              JOIN Students s ON sa.StudentID = s.StudentID
              JOIN Users u ON s.UserID = u.UserID
              JOIN MasterAchievements ma ON sa.AchievementID = ma.AchievementID
              WHERE StudentAchievementID = $studentAchievementID";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $studentAchievementData = $result->fetch_assoc();
    } else {
        $errors[] = "Student achievement not found.";
    }
}
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
        <main class="bg-gray-50 flex flex-col flex-1 overflow-y-scroll h-screen flex-shrink-0 sc-hide pb-40">
            <div class="flex items-start justify-start p-6 shadow-md m-4 flex-1 flex-col">
                <!-- Header Content -->
                <div class="flex flex-row justify-between items-center w-full border-b-2 border-gray-600 mb-2 pb-2">
                    <h1 class="text-3xl text-gray-800 font-semibold w-full">Student Achievement Details</h1>
                    <div class="flex flex-row justify-end items-center">
                        <a href="../manage_student_achievements/manage_student_achievements_list.php" class="bg-gray-800 hover-bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center space-x-2">
                            <i class="fas fa-arrow-left"></i>
                            <span>Back</span>
                        </a>
                    </div>
                </div>
                <!-- End Header Content -->
                <!-- Content -->
                <div class="flex flex-col w-full">
                    <!-- Navigation -->
                    <div class="flex flex-row justify-between items-center w-full mb-2 pb-2">
                        <div>
                            <h2 class="text-lg text-gray-800 font-semibold">Welcome back, <?php echo $_SESSION['FullName']; ?>!</h2>
                            <p class="text-gray-600 text-sm">Student achievement information.</p>
                        </div>
                    </div>
                    <!-- End Navigation -->
                    <!-- Student Achievement Details -->
                    <?php if (!empty($studentAchievementData)) : ?>
                        <div class="bg-white shadow-md p-4 rounded-md">
                            <h3 class="text-lg font-semibold text-gray-800">Student Achievement Information</h3>
                            <p><strong>Student Name:</strong> <?php echo $studentAchievementData['StudentName']; ?></p>
                            <p><strong>Achievement Type:</strong> <?php echo $studentAchievementData['AchievementType']; ?></p>
                            <p><strong>Achievement Name:</strong> <?php echo $studentAchievementData['AchievementName']; ?></p>
                            <p><strong>Points:</strong> <?php echo $studentAchievementData['Points']; ?></p>
                            <p><strong>Date:</strong> <?php echo $studentAchievementData['Date']; ?></p>
                            <p><strong>Time:</strong> <?php echo $studentAchievementData['Time']; ?></p>
                            <p><strong>Organizer:</strong> <?php echo $studentAchievementData['Organizer']; ?></p>
                            <p><strong>Rank:</strong> <?php echo $studentAchievementData['Rank']; ?></p>
                            <p><strong>Details:</strong> <?php echo $studentAchievementData['Details']; ?></p>
                        </div>
                    <?php else : ?>
                        <div class="bg-white shadow-md p-4 rounded-md">
                            <p>No student achievement data available.</p>
                        </div>
                    <?php endif; ?>
                    <!-- End Student Achievement Details -->
                </div>
                <!-- End Content -->
            </div>
        </main>
    </div>
    <!-- End Main Content -->
    <!-- Footer -->
    <?php include('../components/footer.php'); ?>
    <!-- End Footer -->
</div>
</body>

</html>