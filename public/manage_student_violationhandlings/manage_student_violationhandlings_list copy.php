<?php
// Initialize the session
session_start();
// Include the connection file
require_once('../../database/connection.php');

// Initialize variables
$errors = array();

// Initialize an array to store student data
$studentData = array();
// Function to fetch MasterViolationHandlings data
function fetchMasterViolationHandlings()
{
    global $conn;

    // Perform SQL query to fetch data
    $query = "SELECT * FROM MasterViolationHandlings";
    $result = mysqli_query($conn, $query);

    // Check if the query was successful
    if ($result) {
        // Fetch data as an associative array
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

        // Free result set
        mysqli_free_result($result);

        return $data;
    } else {
        // Handle the error
        echo "Error: " . $query . "<br>" . mysqli_error($conn);
        return [];
    }
}
function getFollowUpAction($masterViolationHandlings, $totalDifference)
{
    foreach ($masterViolationHandlings as $handling) {
        $bottomRange = $handling['ScoreRangeBottom'];
        $topRange = $handling['ScoreRangeTop'];

        // Check if TotalDifference falls within the specified range
        if ($totalDifference >= $bottomRange && $totalDifference <= $topRange) {
            return [
                'ViolationCategory' => $handling['ViolationCategory'],
                'FollowUpAction' => $handling['FollowUpAction'],
            ];
        }
    }

    // If no match is found, you can return a default or handle it accordingly
    return [
        'ViolationCategory' => 'Default Category',
        'FollowUpAction' => 'Default Action',
    ];
}
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
              GROUP_CONCAT(DISTINCT StudentViolations.ViolationID) AS ViolationIDs,
              GROUP_CONCAT(DISTINCT StudentAchievements.AchievementID) AS AchievementIDs,
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
        --   WHERE (YEAR(StudentViolations.Date) = $selectedYear AND MONTH(StudentViolations.Date) BETWEEN $startMonth AND $endMonth)
        --      OR (YEAR(StudentAchievements.Date) = $selectedYear AND MONTH(StudentAchievements.Date) BETWEEN $startMonth AND $endMonth)
        --   -- Conditions for the previous semester
        --      OR (YEAR(StudentViolations.Date) = $prevYear AND MONTH(StudentViolations.Date) BETWEEN $prevStartMonth AND $prevEndMonth)
        --      OR (YEAR(StudentAchievements.Date) = $prevYear AND MONTH(StudentAchievements.Date) BETWEEN $prevStartMonth AND $prevEndMonth)
        --   -- Conditions for all previous semesters
        --      OR (YEAR(StudentViolations.Date) < $selectedYear)
        --      OR (YEAR(StudentAchievements.Date) < $selectedYear)
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
                        <?php
                        // Initialize arrays
                        $DataTraining = array();
                        $DataTesting = array();
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
                        $no = 0;
                        while ($row = mysqli_fetch_assoc($result)) {
                            $totalDif = $row['AllPrevTotalDifference'] + $row['PrevTotalPointAchievements'] + $row['PrevTotalViolations'] + $row['TotalDifference'];
                            $no++;
                            $studentInfo = array(
                                'No' => $no,
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
                                'AchievementIDs' => $row['AchievementIDs'],
                                'ViolationIDs' => $row['ViolationIDs'],
                                'TotalDifference' => $totalDif,
                            );

                            // Add the student information array to the main array
                            $studentData[] = $studentInfo;
                            // For TrainingData table
                            $trainingSelectQuery = "SELECT * FROM TrainingData WHERE StudentID = '$row[StudentID]'";
                            $trainingResult = mysqli_query($conn, $trainingSelectQuery);

                            if (mysqli_num_rows($trainingResult) > 0) {
                                // Update the existing record
                                $trainingUpdateQuery = "UPDATE TrainingData SET
                                                        AllPrevTotalPointViolations = '$row[AllPrevTotalPointViolations]',
                                                        AllPrevTotalViolations = '$row[AllPrevTotalViolations]',
                                                        AllPrevTotalPointAchievements = '$row[AllPrevTotalPointAchievements]',
                                                        AllPrevTotalAchievements = '$row[AllPrevTotalAchievements]',
                                                        AllPrevTotalDifference = '$row[AllPrevTotalDifference]'
                                                        WHERE StudentID = '$row[StudentID]'";
                                mysqli_query($conn, $trainingUpdateQuery);
                            } elseif ($row['AllPrevTotalDifference'] > 0) {
                                // Insert the new record only if AllPrevTotalDifference is greater than or equal to 0
                                $trainingInsertQuery = "INSERT INTO TrainingData (StudentID, AllPrevTotalPointViolations, AllPrevTotalViolations, AllPrevTotalPointAchievements, AllPrevTotalAchievements, AllPrevTotalDifference)
                                                        VALUES ('$row[StudentID]', '$row[AllPrevTotalPointViolations]', '$row[AllPrevTotalViolations]', '$row[AllPrevTotalPointAchievements]', '$row[AllPrevTotalAchievements]', '$row[AllPrevTotalDifference]')";
                                mysqli_query($conn, $trainingInsertQuery);
                            }

                            // For TestingData table
                            $testingSelectQuery = "SELECT * FROM TestingData WHERE StudentID = '$row[StudentID]'";
                            $testingResult = mysqli_query($conn, $testingSelectQuery);

                            if (mysqli_num_rows($testingResult) > 0) {
                                // Update the existing record
                                $testingUpdateQuery = "UPDATE TestingData SET
                                                        PrevTotalPointViolations = '$row[PrevTotalPointViolations]',
                                                        PrevTotalViolations = '$row[PrevTotalViolations]',
                                                        PrevTotalPointAchievements = '$row[PrevTotalPointAchievements]',
                                                        PrevTotalAchievements = '$row[PrevTotalAchievements]',
                                                        TotalPointViolations = '$row[TotalPointViolations]',
                                                        TotalViolations = '$row[TotalViolations]',
                                                        TotalPointAchievements = '$row[TotalPointAchievements]',
                                                        TotalAchievements = '$row[TotalAchievements]',
                                                        TotalDifference = '$totalDif'
                                                        WHERE StudentID = '$row[StudentID]'";
                                mysqli_query($conn, $testingUpdateQuery);
                            } elseif ($row['TotalDifference'] > 0) {
                                // Insert the new record only if TotalDifference is greater than or equal to 0
                                $testingInsertQuery = "INSERT INTO TestingData (StudentID, PrevTotalPointViolations, PrevTotalViolations, PrevTotalPointAchievements, PrevTotalAchievements, TotalPointViolations, TotalViolations, TotalPointAchievements, TotalAchievements, TotalDifference)
                                                        VALUES ('$row[StudentID]', '$row[PrevTotalPointViolations]', '$row[PrevTotalViolations]', '$row[PrevTotalPointAchievements]', '$row[PrevTotalAchievements]', '$row[TotalPointViolations]', '$row[TotalViolations]', '$row[TotalPointAchievements]', '$row[TotalAchievements]', '$row[TotalDifference]')";
                                mysqli_query($conn, $testingInsertQuery);
                            }
                        }
                        // Function to generate HTML table from array
                        function arrayToTable($data)
                        {
                            echo '<table>';
                            foreach ($data as $row) {
                                echo '<tr>';
                                foreach ($row as $cell) {
                                    echo '<td>' . $cell . '</td>';
                                }
                                echo '</tr>';
                            }
                            echo '</table>';
                        }

                        // Call the function with your array
                        arrayToTable($studentData);
                        ?>
                        <!-- End Display Total Points (Violations and Achievements) -->
                        <table class="min-w-full bg-white border border-gray-300">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 border-b">No</th>
                                    <th class="py-2 px-4 border-b">Student Name</th>
                                    <th class="py-2 px-4 border-b"><i class="fa-solid fa-skull-crossbones"></i></th>
                                    <th class="py-2 px-4 border-b"><i class="fa-solid fa-trophy"></i></th>
                                    <th class="py-2 px-4 border-b"><i class="fa-solid fa-coins"></i></th>
                                    <th class="py-2 px-4 border-b"><?php echo $prevSemester ?>/<?php echo $prevYear ?> <i class="fa-solid fa-skull-crossbones"></i></th>
                                    <th class="py-2 px-4 border-b"><?php echo $prevSemester ?>/<?php echo $prevYear ?> <i class="fa-solid fa-trophy"></i></th>
                                    <th class="py-2 px-4 border-b"><?php echo $selectedSemester ?>/<?php echo $selectedYear ?><i class="fa-solid fa-skull-crossbones"></i></th>
                                    <th class="py-2 px-4 border-b"><?php echo $selectedSemester ?>/<?php echo $selectedYear ?><i class="fa-solid fa-trophy"></i></th>
                                    <th class="py-2 px-4 border-b"><?php echo $selectedSemester ?>/<?php echo $selectedYear ?><i class="fa-solid fa-coins"></i>(Last)</th>
                                    <th class="py-2 px-4 border-b">Follow Up Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 0;
                                // Fetch data from MasterViolationHandlings
                                $masterViolationHandlings = fetchMasterViolationHandlings();
                                foreach ($studentData as $studentInfo) {
                                    $no++;
                                    // Check if TotalDifference is greater than 0
                                    if ($studentInfo['TotalDifference'] > 0) {
                                        echo '<tr>';
                                        echo '<td>' . $no . '</td>';
                                        echo '<td class="py-2 px-4 border-b"><a href="manage_student_violationhandlings_detail.php?student_id=' . $studentInfo['StudentID'] . '">' . $studentInfo['StudentName'] . ' (' . $studentInfo['StudentNumber'] . ')</a></td>';
                                        echo '<td class="py-2 px-4 border-b">' . $studentInfo['AllPrevTotalPointViolations'] . ' (' . $studentInfo['AllPrevTotalViolations'] . ')</td>';
                                        echo '<td class="py-2 px-4 border-b">' . $studentInfo['AllPrevTotalPointAchievements'] . ' (' . $studentInfo['AllPrevTotalAchievements'] . ')</td>';

                                        // Check if handling is needed
                                        $handlingNeeded = checkHandlingNeeded($studentInfo['AllPrevTotalDifference']);

                                        // Output the appropriate icon or cross mark
                                        if ($handlingNeeded) {
                                            echo '<td class="py-2 px-4 border-b"><a href="#" class="text-green-500">' . $studentInfo['AllPrevTotalDifference'] . ' <i class="fa-solid fa-check"></i></a></td>';
                                        } else {
                                            echo '<td class="py-2 px-4 border-b"><a href="#" class="text-red-500">' . $studentInfo['AllPrevTotalDifference'] . ' <i class="fa-solid fa-times"></i></a></td>';
                                        }

                                        echo '<td class="py-2 px-4 border-b">' . $studentInfo['PrevTotalPointViolations'] . ' (' . $studentInfo['PrevTotalViolations'] . ')</td>';
                                        echo '<td class="py-2 px-4 border-b">' . $studentInfo['PrevTotalPointAchievements'] . ' (' . $studentInfo['PrevTotalAchievements'] . ')</td>';
                                        echo '<td class="py-2 px-4 border-b">' . $studentInfo['TotalPointViolations'] . ' (' . $studentInfo['TotalViolations'] . ')</td>';
                                        echo '<td class="py-2 px-4 border-b">' . $studentInfo['TotalPointAchievements'] . ' (' . $studentInfo['TotalAchievements'] . ')</td>';
                                        // Check if handling is needed
                                        $handlingNeeded = checkHandlingNeeded($studentInfo['TotalDifference']);

                                        // Output the appropriate icon or cross mark
                                        if ($handlingNeeded) {
                                            echo '<td class="py-2 px-4 border-b"><a href="#" class="text-green-500">' . $studentInfo['TotalDifference'] . ' <i class="fa-solid fa-check"></i></a></td>';
                                        } else {
                                            echo '<td class="py-2 px-4 border-b"><a href="#" class="text-red-500">' . $studentInfo['TotalDifference'] . ' <i class="fa-solid fa-times"></i></a></td>';
                                        }
                                        echo '<td class="py-2 px-4 border-b">';
                                        if ($handlingNeeded) {
                                            $followUpAction = getFollowUpAction($masterViolationHandlings, $studentInfo['TotalDifference']);
                                            // Set default classes
                                            $buttonClasses = 'sweet-alert-btn text-white text-xs font-bold py-2 px-4 rounded';
                                            $iconClasses = 'fas fa-exclamation-circle mr-2';

                                            // Set color based on ViolationCategory
                                            switch ($followUpAction['ViolationCategory']) {
                                                case 'Ringan':
                                                    $buttonClasses .= ' bg-green-500'; // Green color for Ringan
                                                    break;
                                                case 'Sedang':
                                                    $buttonClasses .= ' bg-yellow-500'; // Yellow color for Sedang
                                                    break;
                                                case 'Berat':
                                                    $buttonClasses .= ' bg-red-500'; // Red color for Berat
                                                    break;
                                                default:
                                                    $buttonClasses .= ' bg-blue-500'; // Default color for other categories
                                            }

                                            // Output the button
                                            echo '<button class="' . $buttonClasses . '" data-student-id="' . $studentInfo['StudentID'] . '" data-category="' . $followUpAction['ViolationCategory'] . '" data-follow-up="' . $followUpAction['FollowUpAction'] . '">
                                        <i class="' . $iconClasses . '"></i>' . $followUpAction['ViolationCategory'] . '
                                        </button>';
                                        } else {
                                            echo 'No Action';
                                        }
                                        echo '</td>';

                                        echo '</td>';
                                    }
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listener to all elements with class sweet-alert-btn
            var buttons = document.querySelectorAll('.sweet-alert-btn');
            buttons.forEach(function(button) {
                button.addEventListener('click', function() {
                    // Retrieve category from data-category attribute
                    var category = button.getAttribute('data-category');
                    // Retrieve student ID from data-student-id attribute
                    var studentID = button.getAttribute('data-student-id');
                    var followUPing = button.getAttribute('data-follow-up');

                    // Make an AJAX request to fetch student data
                    fetchStudentData(studentID, category, followUPing);
                });
            });

            function fetchStudentData(studentID, category, followUPing) {
                // You can use any AJAX library or the native fetch API for the request
                // Here, I'll use the fetch API for simplicity
                fetch('fetch_student_data.php?student_id=' + studentID)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Fetched student data:', data);

                        // Format student information into a table
                        const tableHtml = `
                    <table class="table-auto">
                        <tbody>
                            <tr>
                                <td class="border px-4 py-2">Violation Category</td>
                                <td class="border px-4 py-2">${category}</td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2">Student Name</td>
                                <td class="border px-4 py-2">${data.StudentName}</td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2">Student Number</td>
                                <td class="border px-4 py-2">${data.StudentNumber}</td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2">Religion</td>
                                <td class="border px-4 py-2">${data.Religion}</td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2">Parent/Guardian</td>
                                <td class="border px-4 py-2">${data.ParentGuardianFullName}</td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2">Contact</td>
                                <td class="border px-4 py-2">${data.ParentGuardianPhoneNumber}</td>
                            </tr>
                            <tr>
                                <td class="border px-4 py-2">Email</td>
                                <td class="border px-4 py-2">${data.ParentGuardianEmail}</td>
                            </tr>
                        </tbody>
                    </table>
                    `;

                        // Format Follow-Up Action
                        const followUpActionHtml = `
                        <div class="mt-4">
                            <strong>Follow-Up Action:</strong> ${followUPing}
                        </div>
                    `;

                        // Combine table and Follow-Up Action
                        const contentHtml = tableHtml + followUpActionHtml;

                        // Display Sweet Alert with the formatted table and Follow-Up Action
                        Swal.fire({
                            title: 'Follow-Up Action',
                            html: contentHtml,
                            icon: 'info'
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching student data:', error);
                        Swal.fire({
                            title: 'Error',
                            text: 'Failed to fetch student data. Please try again.',
                            icon: 'error'
                        });
                    });
            }
        });
    </script>
</body>

</html>

<?php
// Close the database connection
mysqli_close($conn);
?>