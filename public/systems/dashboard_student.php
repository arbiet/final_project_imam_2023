<?php
// Initialize the session
session_start();
// Include the connection file
require_once('../../database/connection.php');

// Get StudentID using UserID
$userID = $_SESSION['UserID'];
$query = "SELECT StudentID FROM Students WHERE UserID = $userID";
$result = mysqli_query($conn, $query);
$student = mysqli_fetch_assoc($result);
$studentID = $student['StudentID'];

// Get the number of achievements for the student
$queryAchievementsCount = "SELECT COUNT(*) AS AchievementCount FROM StudentAchievements WHERE StudentID = $studentID";
$resultAchievementsCount = mysqli_query($conn, $queryAchievementsCount);
$achievementsCount = mysqli_fetch_assoc($resultAchievementsCount)['AchievementCount'];

// Get the number of violations for the student
$queryViolationsCount = "SELECT COUNT(*) AS ViolationCount FROM StudentViolations WHERE StudentID = $studentID";
$resultViolationsCount = mysqli_query($conn, $queryViolationsCount);
$violationsCount = mysqli_fetch_assoc($resultViolationsCount)['ViolationCount'];

// Get the list of achievements for the student
$queryAchievements = "SELECT sa.*, ma.* FROM StudentAchievements sa
                      JOIN MasterAchievements ma ON sa.AchievementID = ma.AchievementID
                      WHERE sa.StudentID = $studentID";
$resultAchievements = mysqli_query($conn, $queryAchievements);

// Get the list of violations for the student
$queryViolations = "SELECT sv.*, mv.* FROM StudentViolations sv
                    JOIN MasterViolations mv ON sv.ViolationID = mv.ViolationID
                    WHERE sv.StudentID = $studentID";
$resultViolations = mysqli_query($conn, $queryViolations);

?>

<?php include('../components/header.php'); ?>
<div class="h-screen flex flex-col">
    <?php include('../components/navbar.php'); ?>
    <div class="bg-gray-50 flex flex-row shadow-md">
        <?php include('../components/sidebar.php'); ?>
        <main class="bg-gray-50 flex flex-col flex-1 overflow-y-scroll h-screen flex-shrink-0 sc-hide pb-40">
            <div class="flex items-start justify-start p-6 shadow-md m-4 flex-1 flex-col">
                <h1 class="text-3xl text-gray-800 font-semibold border-b border-gray-200 w-full">Dashboard</h1>
                <h2 class="text-xl text-gray-800 font-semibold">
                    Welcome back, <?php echo $_SESSION['FullName']; ?>!
                    <?php
                    if ($_SESSION['RoleID'] === 'admin') {
                        echo " (Admin)";
                    } elseif ($_SESSION['RoleID'] === 'student') {
                        echo " (Student)";
                    }
                    ?>
                </h2>
                <p class="text-gray-600">Here's what's happening with your achievements and violations today.</p>

                <!-- Display table of achievements -->
                <div class="mt-4">
                    <h3 class="text-lg text-gray-800 font-semibold">Achievements</h3>
                    <p class="text-gray-600">Total Achievements: <?php echo $achievementsCount; ?></p>

                    <!-- Table for achievements -->
                    <table class="min-w-full divide-y divide-gray-200">
                        <!-- Table header -->
                        <thead class="bg-gray-50">
                            <tr>
                                <!-- Column headers -->
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Achievement Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Point</th>
                            </tr>
                        </thead>
                        <!-- Table body -->
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($achievement = mysqli_fetch_assoc($resultAchievements)) : ?>
                                <tr>
                                    <!-- Cells with achievement details -->
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $achievement['AchievementName']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $achievement['Points']; ?></td>
                                    <!-- Add more cells with additional achievement details as needed -->
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Display table of violations -->
                <div class="mt-4">
                    <h3 class="text-lg text-gray-800 font-semibold">Violations</h3>
                    <p class="text-gray-600">Total Violations: <?php echo $violationsCount; ?></p>

                    <!-- Table for violations -->
                    <table class="min-w-full divide-y divide-gray-200">
                        <!-- Table header -->
                        <thead class="bg-gray-50">
                            <tr>
                                <!-- Column headers -->
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Violation Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Point</th>
                                <!-- Add more columns as needed for additional violation details -->
                            </tr>
                        </thead>
                        <!-- Table body -->
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($violation = mysqli_fetch_assoc($resultViolations)) : ?>
                                <tr>
                                    <!-- Cells with violation details -->
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $violation['ViolationName']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $violation['Date']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $violation['Points']; ?></td>
                                    <!-- Add more cells with additional violation details as needed -->
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    <?php include('../components/footer.php'); ?>
</div>
</body>

</html>