<?php
// Initialize the session
session_start();
// Include the connection file
require_once('../../database/connection.php');

// Initialize variables
$errors = array();

// Get the selected year and semester from the URL parameters
$selectedYear = isset($_GET['year']) ? intval($_GET['year']) : null;
$selectedSemester = isset($_GET['semester']) ? intval($_GET['semester']) : null;

// Validate the selected semester
if ($selectedSemester != 1 && $selectedSemester != 2) {
    // Handle invalid semester parameter (redirect or display an error message)
    header("Location: manage_student_violationhandlings_list.php?year=2023&semester=1");
    exit();
}

// Calculate the start and end months for the selected semester
$startMonth = ($selectedSemester == 1) ? 1 : 7;
$endMonth = ($selectedSemester == 1) ? 6 : 12;

// Calculate the start and end months for the previous semester
$prevSemester = ($selectedSemester == 1) ? 2 : 1;
$prevStartMonth = ($prevSemester == 1) ? 1 : 7;
$prevEndMonth = ($prevSemester == 1) ? 6 : 12;

// Construct the SQL query to get the total points for each student (Violations and Achievements)
$query = "SELECT Students.StudentID,
                 Students.StudentNumber,
                 Users.FullName AS StudentName,
                 SUM(CASE WHEN YEAR(StudentViolations.Date) = $selectedYear
                           AND MONTH(StudentViolations.Date) BETWEEN $startMonth AND $endMonth
                           THEN MasterViolations.Points ELSE 0 END) AS TotalPointsViolations,
                 SUM(CASE WHEN YEAR(StudentAchievements.Date) = $selectedYear
                           AND MONTH(StudentAchievements.Date) BETWEEN $startMonth AND $endMonth
                           THEN MasterAchievements.Points ELSE 0 END) AS TotalPointsAchievements,
                 SUM(CASE WHEN YEAR(StudentViolations.Date) = $selectedYear - 1
                           AND MONTH(StudentViolations.Date) BETWEEN $prevStartMonth AND $prevEndMonth
                           THEN MasterViolations.Points ELSE 0 END) AS PrevTotalPointsViolations,
                 SUM(CASE WHEN YEAR(StudentAchievements.Date) = $selectedYear - 1
                           AND MONTH(StudentAchievements.Date) BETWEEN $prevStartMonth AND $prevEndMonth
                           THEN MasterAchievements.Points ELSE 0 END) AS PrevTotalPointsAchievements
          FROM Students
          JOIN Users ON Students.UserID = Users.UserID
          LEFT JOIN StudentViolations ON Students.StudentID = StudentViolations.StudentID
          LEFT JOIN MasterViolations ON StudentViolations.ViolationID = MasterViolations.ViolationID
          LEFT JOIN StudentAchievements ON Students.StudentID = StudentAchievements.StudentID
          LEFT JOIN MasterAchievements ON StudentAchievements.AchievementID = MasterAchievements.AchievementID
          WHERE (YEAR(StudentViolations.Date) = $selectedYear
                 AND MONTH(StudentViolations.Date) BETWEEN $startMonth AND $endMonth)
             OR (YEAR(StudentAchievements.Date) = $selectedYear
                 AND MONTH(StudentAchievements.Date) BETWEEN $startMonth AND $endMonth)
             OR (YEAR(StudentViolations.Date) = $selectedYear - 1
                 AND MONTH(StudentViolations.Date) BETWEEN $prevStartMonth AND $prevEndMonth)
             OR (YEAR(StudentAchievements.Date) = $selectedYear - 1
                 AND MONTH(StudentAchievements.Date) BETWEEN $prevStartMonth AND $prevEndMonth)
          GROUP BY Students.StudentID
          HAVING (TotalPointsViolations > 0 OR TotalPointsAchievements > 0
                  OR PrevTotalPointsViolations > 0 OR PrevTotalPointsAchievements > 0)"; // Exclude students with no points

$result = mysqli_query($conn, $query);

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
                    <h1 class="text-3xl text-gray-800 font-semibold w-full">Student Violation Handlings</h1>
                </div>
                <!-- End Header Content -->
                <!-- Content -->
                <div class="flex flex-col w-full">
                    <!-- Filter Buttons -->
                    <div class="flex flex-wrap gap-2 mb-4 justify-between" id="filterButtons">
                        <?php
                        // Get current year
                        $currentYear = date('Y');

                        // Loop through the last two years
                        for ($year = $currentYear; $year >= $currentYear - 2; $year--) {
                            $sem1Class = ($selectedYear == $year && $selectedSemester == 1) ? 'bg-green-500 hover:bg-green-700' : 'bg-blue-500 hover:bg-blue-700';
                            $sem2Class = ($selectedYear == $year && $selectedSemester == 2) ? 'bg-green-500 hover:bg-green-700' : 'bg-blue-500 hover:bg-blue-700';

                            $sem1OnClick = "filterSemester($year, 1)";
                            $sem2OnClick = "filterSemester($year, 2)";

                            echo "<button id='sem1Btn_$year' class='text-white font-bold py-2 px-4 rounded $sem1Class' onclick=\"$sem1OnClick\">Sem 1 TA $year/" . ($year + 1) . '</button>';
                            echo "<button id='sem2Btn_$year' class='text-white font-bold py-2 px-4 rounded $sem2Class' onclick=\"$sem2OnClick\">Sem 2 TA $year/" . ($year + 1) . '</button>';
                        }
                        ?>
                    </div>
                    <!-- End Filter Buttons -->

                    <!-- Display Total Points (Violations and Achievements) -->
                    <h2 class="text-xl font-semibold mb-2">Total Points for Students (Violations and Achievements)</h2>
                    <table class="min-w-full bg-white border border-gray-300">
                        <thead>
                            <tr>
                                <th class="py-2 px-4 border-b">Student Number</th>
                                <th class="py-2 px-4 border-b">Student Name</th>
                                <th class="py-2 px-4 border-b">Total Points (Violations)</th>
                                <th class="py-2 px-4 border-b">Total Points (Achievements)</th>
                                <th class="py-2 px-4 border-b">Prev Total Points (Violations)</th>
                                <th class="py-2 px-4 border-b">Prev Total Points (Achievements)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo '<tr>';
                                echo '<td class="py-2 px-4 border-b">' . $row['StudentNumber'] . '</td>';
                                echo '<td class="py-2 px-4 border-b"><a href="manage_student_violationhandlings_detail.php?student_id=' . $row['StudentID'] . '">' . $row['StudentName'] . '</a></td>';
                                echo '<td class="py-2 px-4 border-b">' . $row['TotalPointsViolations'] . '</td>';
                                echo '<td class="py-2 px-4 border-b">' . $row['TotalPointsAchievements'] . '</td>';
                                echo '<td class="py-2 px-4 border-b">' . $row['PrevTotalPointsViolations'] . '</td>';
                                echo '<td class="py-2 px-4 border-b">' . $row['PrevTotalPointsAchievements'] . '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    <!-- End Display Total Points (Violations and Achievements) -->

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
<!-- JavaScript for Filtering -->
<script>
    function filterSemester(year, semester) {
        // Construct the URL based on the selected semester and year
        var url = "manage_student_violationhandlings_list.php";
        url += "?year=" + year + "&semester=" + semester;

        // Redirect to the constructed URL
        window.location.href = url;
    }
</script>

</html>

<?php
// Close the database connection
mysqli_close($conn);
?>