<?php
// Initialize the session
session_start();
// Include the connection file
require_once('../../database/connection.php');

// Initialize variables
$errors = array();

// Initialize an array to store student data
$studentData = array();

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
$prevStartMonth = ($selectedSemester == 1) ? 7 : 1;
$prevEndMonth = ($selectedSemester == 1) ? 12 : 6;
$prevYear = ($selectedSemester == 1) ? $selectedYear - 1 : $selectedYear;

// Fetch data for each student in the specified semester and year
$query = "SELECT
              Students.StudentID,
              Students.StudentNumber,
              Users.FullName AS StudentName,
              -- Data for the selected semester
              COUNT(DISTINCT CASE WHEN MasterViolations.ViolationID IS NOT NULL AND YEAR(StudentViolations.Date) = $selectedYear AND MONTH(StudentViolations.Date) BETWEEN $startMonth AND $endMonth THEN StudentViolations.ViolationID END) AS TotalViolations,
              COUNT(DISTINCT CASE WHEN MasterAchievements.AchievementID IS NOT NULL AND YEAR(StudentAchievements.Date) = $selectedYear AND MONTH(StudentAchievements.Date) BETWEEN $startMonth AND $endMonth THEN StudentAchievements.AchievementID END) AS TotalAchievements,
              COALESCE(SUM(DISTINCT CASE WHEN MasterViolations.ViolationID IS NOT NULL AND YEAR(StudentViolations.Date) = $selectedYear AND MONTH(StudentViolations.Date) BETWEEN $startMonth AND $endMonth THEN MasterViolations.Points END), 0) AS TotalPointViolations,
              COALESCE(SUM(DISTINCT CASE WHEN MasterAchievements.AchievementID IS NOT NULL AND YEAR(StudentAchievements.Date) = $selectedYear AND MONTH(StudentAchievements.Date) BETWEEN $startMonth AND $endMonth THEN MasterAchievements.Points END), 0) AS TotalPointAchievements,
              (COALESCE(SUM(DISTINCT CASE WHEN MasterViolations.ViolationID IS NOT NULL AND YEAR(StudentViolations.Date) = $selectedYear AND MONTH(StudentViolations.Date) BETWEEN $startMonth AND $endMonth THEN MasterViolations.Points END), 0) -
               COALESCE(SUM(DISTINCT CASE WHEN MasterAchievements.AchievementID IS NOT NULL AND YEAR(StudentAchievements.Date) = $selectedYear AND MONTH(StudentAchievements.Date) BETWEEN $startMonth AND $endMonth THEN MasterAchievements.Points END), 0)) AS TotalDifference,
              -- Data for the previous semester
              COUNT(DISTINCT CASE WHEN MasterViolations.ViolationID IS NOT NULL AND YEAR(StudentViolations.Date) = $prevYear AND MONTH(StudentViolations.Date) BETWEEN $prevStartMonth AND $prevEndMonth THEN StudentViolations.ViolationID END) AS PrevTotalViolations,
              COUNT(DISTINCT CASE WHEN MasterAchievements.AchievementID IS NOT NULL AND YEAR(StudentAchievements.Date) = $prevYear AND MONTH(StudentAchievements.Date) BETWEEN $prevStartMonth AND $prevEndMonth THEN StudentAchievements.AchievementID END) AS PrevTotalAchievements,
              COALESCE(SUM(DISTINCT CASE WHEN MasterViolations.ViolationID IS NOT NULL AND YEAR(StudentViolations.Date) = $prevYear AND MONTH(StudentViolations.Date) BETWEEN $prevStartMonth AND $prevEndMonth THEN MasterViolations.Points END), 0) AS PrevTotalPointViolations,
              COALESCE(SUM(DISTINCT CASE WHEN MasterAchievements.AchievementID IS NOT NULL AND YEAR(StudentAchievements.Date) = $prevYear AND MONTH(StudentAchievements.Date) BETWEEN $prevStartMonth AND $prevEndMonth THEN MasterAchievements.Points END), 0) AS PrevTotalPointAchievements,
              -- Data for all previous semesters
              COUNT(DISTINCT CASE WHEN MasterViolations.ViolationID IS NOT NULL AND YEAR(StudentViolations.Date) < $selectedYear THEN StudentViolations.ViolationID END) AS AllPrevTotalViolations,
              COUNT(DISTINCT CASE WHEN MasterAchievements.AchievementID IS NOT NULL AND YEAR(StudentAchievements.Date) < $selectedYear THEN StudentAchievements.AchievementID END) AS AllPrevTotalAchievements,
              COALESCE(SUM(DISTINCT CASE WHEN MasterViolations.ViolationID IS NOT NULL AND YEAR(StudentViolations.Date) < $selectedYear THEN MasterViolations.Points END), 0) AS AllPrevTotalPointViolations,
              COALESCE(SUM(DISTINCT CASE WHEN MasterAchievements.AchievementID IS NOT NULL AND YEAR(StudentAchievements.Date) < $selectedYear THEN MasterAchievements.Points END), 0) AS AllPrevTotalPointAchievements,
              -- Data for all previous semesters difference
              (COALESCE(SUM(DISTINCT CASE WHEN MasterViolations.ViolationID IS NOT NULL AND YEAR(StudentViolations.Date) < $selectedYear THEN MasterViolations.Points END), 0) -
               COALESCE(SUM(DISTINCT CASE WHEN MasterAchievements.AchievementID IS NOT NULL AND YEAR(StudentAchievements.Date) < $selectedYear THEN MasterAchievements.Points END), 0)) AS AllPrevTotalDifference
          FROM Students
          LEFT JOIN Users ON Students.UserID = Users.UserID
          LEFT JOIN StudentViolations ON Students.StudentID = StudentViolations.StudentID
          LEFT JOIN MasterViolations ON StudentViolations.ViolationID = MasterViolations.ViolationID
          LEFT JOIN StudentAchievements ON Students.StudentID = StudentAchievements.StudentID
          LEFT JOIN MasterAchievements ON StudentAchievements.AchievementID = MasterAchievements.AchievementID
          -- Conditions for the selected semester
          WHERE (YEAR(StudentViolations.Date) = $selectedYear AND MONTH(StudentViolations.Date) BETWEEN $startMonth AND $endMonth)
             OR (YEAR(StudentAchievements.Date) = $selectedYear AND MONTH(StudentAchievements.Date) BETWEEN $startMonth AND $endMonth)
          -- Conditions for the previous semester
             OR (YEAR(StudentViolations.Date) = $prevYear AND MONTH(StudentViolations.Date) BETWEEN $prevStartMonth AND $prevEndMonth)
             OR (YEAR(StudentAchievements.Date) = $prevYear AND MONTH(StudentAchievements.Date) BETWEEN $prevStartMonth AND $prevEndMonth)
          -- Conditions for all previous semesters
             OR (YEAR(StudentViolations.Date) < $selectedYear)
             OR (YEAR(StudentAchievements.Date) < $selectedYear)
          GROUP BY Students.StudentID";

$result = mysqli_query($conn, $query);

?>
<?php include_once('../components/header.php'); ?>

<body>
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
                        <!-- Add semester and year information -->
                        <p class="text-sm text-gray-600">
                            Current Semester:
                            <?php
                            $semesterText = ($selectedSemester == 1) ? 'Semester 1' : 'Semester 2';
                            echo "$semesterText, $selectedYear/" . ($selectedYear + 1);
                            ?>
                        </p>
                        <!-- Add previous semester information -->
                        <?php
                        $prevSemester = ($selectedSemester == 1) ? 2 : 1;
                        $prevYear = ($selectedSemester == 1) ? $selectedYear - 1 : $selectedYear;
                        $prevSemesterText = ($prevSemester == 1) ? 'Semester 1' : 'Semester 2';
                        $nextYear = ($prevYear == $selectedYear) ? $selectedYear + 1 : $selectedYear;
                        ?>
                        <p class="text-sm text-gray-600">
                            Previous Semester: <?php echo "$prevSemesterText, $prevYear/$nextYear"; ?>
                        </p>
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

                                echo "<button id='sem2Btn_$year' class='text-white font-bold py-2 px-4 rounded $sem2Class' onclick=\"$sem2OnClick\">Sem 2 TA $year/" . ($year + 1) . '</button>';
                                echo "<button id='sem1Btn_$year' class='text-white font-bold py-2 px-4 rounded $sem1Class' onclick=\"$sem1OnClick\">Sem 1 TA $year/" . ($year + 1) . '</button>';
                            }
                            ?>
                        </div>
                        <!-- End Filter Buttons -->


                        <!-- Display Total Points (Violations and Achievements) -->
                        <h2 class="text-xl font-semibold mb-2">Total Points for Students (Violations and Achievements)</h2>
                        <table class="min-w-full bg-white border border-gray-300">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b">Student Name</th>
                                    <th class="py-2 px-4 border-b"><i class="fa-solid fa-skull-crossbones"></i></th>
                                    <th class="py-2 px-4 border-b"><i class="fa-solid fa-trophy"></i></th>
                                    <th class="py-2 px-4 border-b"><i class="fa-solid fa-coins"></i></th>
                                    <th class="py-2 px-4 border-b"><?php echo $prevSemester ?>/<?php echo $prevYear ?> <i class="fa-solid fa-skull-crossbones"></i></th>
                                    <th class="py-2 px-4 border-b"><?php echo $prevSemester ?>/<?php echo $prevYear ?> <i class="fa-solid fa-trophy"></i></th>
                                    <th class="py-2 px-4 border-b"><?php echo $selectedSemester ?>/<?php echo $selectedYear ?><i class="fa-solid fa-skull-crossbones"></i></th>
                                    <th class="py-2 px-4 border-b"><?php echo $selectedSemester ?>/<?php echo $selectedYear ?><i class="fa-solid fa-trophy"></i></th>
                                    <th class="py-2 px-4 border-b"><?php echo $selectedSemester ?>/<?php echo $selectedYear ?><i class="fa-solid fa-coins"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                // Function to check if handling is needed
                                function checkHandlingNeeded($difference)
                                {
                                    // Perform a database query to check if handling is needed based on the difference
                                    // Replace 'your_db_connection' with your actual database connection variable
                                    global $conn;

                                    $query = "SELECT HandlingID FROM MasterViolationHandlings WHERE $difference BETWEEN ScoreRangeBottom AND ScoreRangeTop";
                                    $result = mysqli_query($conn, $query);

                                    // Check if there is a match in the MasterViolationHandlings table
                                    $handlingNeeded = mysqli_num_rows($result) > 0;

                                    // Free the result set
                                    mysqli_free_result($result);

                                    return $handlingNeeded;
                                }
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $studentInfo = array(
                                        'StudentID' => $row['StudentID'],
                                        'StudentName' => $row['StudentName'],
                                        'StudentNumber' => $row['StudentNumber'],
                                        'AllPrevTotalPointViolations' => $row['AllPrevTotalPointViolations'],
                                        'AllPrevTotalViolations' => $row['AllPrevTotalViolations'],
                                        'AllPrevTotalPointAchievements' => $row['AllPrevTotalPointAchievements'],
                                        'AllPrevTotalAchievements' => $row['AllPrevTotalAchievements'],
                                        'AllPrevTotalDifference' => $row['AllPrevTotalDifference'],
                                        'PrevTotalPointViolations' => $row['PrevTotalPointViolations'],
                                        'PrevTotalViolations' => $row['PrevTotalViolations'],
                                        'PrevTotalPointAchievements' => $row['PrevTotalPointAchievements'],
                                        'PrevTotalAchievements' => $row['PrevTotalAchievements'],
                                        'TotalPointViolations' => $row['TotalPointViolations'],
                                        'TotalViolations' => $row['TotalViolations'],
                                        'TotalPointAchievements' => $row['TotalPointAchievements'],
                                        'TotalAchievements' => $row['TotalAchievements'],
                                        'TotalDifference' => $row['TotalDifference'],
                                    );

                                    // Add the student information array to the main array
                                    $studentData[] = $studentInfo;

                                    echo '<tr>';
                                    echo '<td class="py-2 px-4 border-b"><a href="manage_student_violationhandlings_detail.php?student_id=' . $row['StudentID'] . '">' . $row['StudentName'] . ' (' . $row['StudentNumber'] . ')</a></td>';
                                    echo '<td class="py-2 px-4 border-b">' . $row['AllPrevTotalPointViolations'] . ' (' . $row['AllPrevTotalViolations'] . ')</td>';
                                    echo '<td class="py-2 px-4 border-b">' . $row['AllPrevTotalPointAchievements'] . ' (' . $row['AllPrevTotalAchievements'] . ')</td>';
                                    // Check if handling is needed
                                    $handlingNeeded = checkHandlingNeeded($row['AllPrevTotalDifference']);
                                    // Output the appropriate icon or cross mark
                                    if ($handlingNeeded) {
                                        echo '<td class="py-2 px-4 border-b"><a href="#" class="text-green-500">' . $row['AllPrevTotalDifference'] . ' <i class="fa-solid fa-check"></i></a></td>';
                                    } else {
                                        echo '<td class="py-2 px-4 border-b"><a href="#" class="text-red-500">' . $row['AllPrevTotalDifference'] . ' <i class="fa-solid fa-times"></i></a></td>';
                                    }
                                    echo '<td class="py-2 px-4 border-b">' . $row['PrevTotalPointViolations'] . ' (' . $row['PrevTotalViolations'] . ')</td>';
                                    echo '<td class="py-2 px-4 border-b">' . $row['PrevTotalPointAchievements'] . ' (' . $row['PrevTotalAchievements'] . ')</td>';
                                    echo '<td class="py-2 px-4 border-b">' . $row['TotalPointViolations'] . ' (' . $row['TotalViolations'] . ')</td>';
                                    echo '<td class="py-2 px-4 border-b">' . $row['TotalPointAchievements'] . ' (' . $row['TotalAchievements'] . ')</td>';
                                    echo '<td class="py-2 px-4 border-b">' . $row['TotalDifference'] . ' </td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                        <!-- End Display Total Points (Violations and Achievements) -->
                        <?php
                        // Query to get MasterViolationHandlings based on AllPrevTotalDifference
                        echo '<h2 class="text-xl font-semibold mb-2">Handling Information Based on AllPrevTotalDifference</h2>';
                        echo '<ul>';
                        foreach ($studentData as $studentInfo) {
                            // Perform a query to get the MasterViolationHandlings based on AllPrevTotalDifference
                            $difference = $studentInfo['AllPrevTotalDifference'];
                            $queryHandling = "SELECT * FROM MasterViolationHandlings WHERE $difference BETWEEN ScoreRangeBottom AND ScoreRangeTop";
                            $resultHandling = mysqli_query($conn, $queryHandling);

                            // Output the handling information
                            while ($handlingRow = mysqli_fetch_assoc($resultHandling)) {
                                echo '<li>';
                                echo "{$handlingRow['ViolationCategory']} ({$handlingRow['FollowUpAction']}) = {$handlingRow['HandlingID']}";
                                echo '</li>';
                            }

                            // Free the result set
                            mysqli_free_result($resultHandling);
                        }
                        echo '</ul>';

                        ?>
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
</body>

</html>

<?php
// Close the database connection
mysqli_close($conn);
?>