<?php
// Include the connection file
require_once('../../database/connection.php');
require_once('getData.php');

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
$prevStartMonth = ($selectedSemester == 1) ? 7 : 1;
$prevEndMonth = ($selectedSemester == 1) ? 12 : 6;
$prevYear = ($selectedSemester == 1) ? $selectedYear - 1 : $selectedYear;
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
                            $currentYear = date('Y') - 1;

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
                        <h2 class="text-xl font-semibold mb-2">Total Points for Students (Violations and Achievements) <?php echo $semesterText . " " . $selectedYear . "/ " . ($selectedYear + 1); ?></h2>
                        <!-- End Display Total Points (Violations and Achievements) -->
                        <!-- Display SemesterResultsTraining Table -->
                        <div class="mt-4">
                            <h2 class="text-xl font-semibold mb-2">SemesterResultsTraining Data</h2>

                            <?php
                            // Fetch data from SemesterResultsTraining table
                            $query = "SELECT `DataID`, `Semester`, `Year`, `PrevYear`, `StartMonth`, `EndMonth`, `PrevStartMonth`, `PrevEndMonth`, `PreSemester`, `StudentID`, `StudentNumber`, `StudentName`, `ViolationIDs`, `AchievementIDs`, `TotalViolations`, `TotalAchievements`, `TotalPointViolations`, `TotalPointAchievements`, `TotalDifference`, `PrevTotalViolations`, `PrevTotalAchievements`, `PrevTotalPointViolations`, `PrevTotalPointAchievements`, `AllPrevTotalViolations`, `AllPrevTotalAchievements`, `AllPrevTotalPointViolations`, `AllPrevTotalPointAchievements`, `AllPrevTotalDifference`, `HandlingID` FROM `SemesterResultsTraining`";

                            $result = mysqli_query($conn, $query);

                            // Fetch data from SemesterResultsTraining table
                            $query = "SELECT `DataID`, `Semester`, `Year`, `PrevYear`, `StartMonth`, `EndMonth`, `PrevStartMonth`, `PrevEndMonth`, `PreSemester`, `StudentID`, `StudentNumber`, `StudentName`, `ViolationIDs`, `AchievementIDs`, `TotalViolations`, `TotalAchievements`, `TotalPointViolations`, `TotalPointAchievements`, `TotalDifference`, `PrevTotalViolations`, `PrevTotalAchievements`, `PrevTotalPointViolations`, `PrevTotalPointAchievements`, `AllPrevTotalViolations`, `AllPrevTotalAchievements`, `AllPrevTotalPointViolations`, `AllPrevTotalPointAchievements`, `AllPrevTotalDifference`, `HandlingID` FROM `SemesterResultsTraining` 
                            WHERE `Year` = '$selectedYear' AND `Semester` = '$selectedSemester'";

                            // Initialize an array to store data
                            $dataArray = array();
                            $dataArrayTest = array();

                            $resultTest = mysqli_query($conn, $query);
                            if (!$resultTest) {
                                echo "Error fetching data: " . mysqli_error($conn);
                            } else {
                                // Initialize an array to store data
                                $dataArrayTest = array();

                                // Display table data
                                while ($row = mysqli_fetch_assoc($resultTest)) {
                                    // Append only the displayed data to the array
                                    $rowData = array(
                                        'DataID' => $row['DataID'],
                                        'StudentID' => $row['StudentID'],
                                        'StudentNumber' => $row['StudentNumber'],
                                        'StudentName' => $row['StudentName'],
                                        'Semester' => $row['Semester'],
                                        'Year' => $row['Year'],
                                        'ViolationIDs' => ($row['ViolationIDs'] ? $row['ViolationIDs'] : '0'),
                                        'AchievementIDs' => ($row['AchievementIDs'] ? $row['AchievementIDs'] : '0'),
                                        'TotalViolations' => $row['TotalViolations'],
                                        'TotalAchievements' => $row['TotalAchievements'],
                                        'TotalPointViolations' => $row['TotalPointViolations'],
                                        'TotalPointAchievements' => $row['TotalPointAchievements'],
                                        'TotalDifference' => $row['TotalDifference'],
                                        'HandlingID' => $row['HandlingID']
                                    );

                                    $dataArrayTest[] = $rowData;
                                }
                            }

                            if (!$result) {
                                echo "Error fetching data: " . mysqli_error($conn);
                            } else {

                                // Display table data
                                while ($row = mysqli_fetch_assoc($result)) {

                                    // Append only the displayed data to the array
                                    $rowData = array(
                                        'DataID' => $row['DataID'],
                                        'StudentID' => $row['StudentID'],
                                        'StudentNumber' => $row['StudentNumber'],
                                        'StudentName' => $row['StudentName'],
                                        'Semester' => $row['Semester'],
                                        'Year' => $row['Year'],
                                        'ViolationIDs' => ($row['ViolationIDs'] ? $row['ViolationIDs'] : '0'),
                                        'AchievementIDs' => ($row['AchievementIDs'] ? $row['AchievementIDs'] : '0'),
                                        'TotalViolations' => $row['TotalViolations'],
                                        'TotalAchievements' => $row['TotalAchievements'],
                                        'TotalPointViolations' => $row['TotalPointViolations'],
                                        'TotalPointAchievements' => $row['TotalPointAchievements'],
                                        'TotalDifference' => $row['TotalDifference'],
                                        'HandlingID' => $row['HandlingID']
                                    );

                                    $dataArray[] = $rowData;
                                }

                                echo '<div class="flex flex-col w-full">';

                                // Step 1: Count Total Data (n)
                                $totalData = count($dataArray);

                                // Step 2: Count Handling Data
                                $handlingCounts = array_count_values(array_column($dataArray, 'HandlingID'));

                                // Step 3: Calculate Prior Probabilities
                                $priorProbabilities = array();
                                foreach ($handlingCounts as $handlingID => $count) {
                                    $priorProbabilities[$handlingID] = $count / $totalData;
                                }

                                // Step 4: Calculate Conditional Probabilities
                                $conditionalProbabilities = array();
                                $features = ['ViolationIDs', 'AchievementIDs', 'TotalViolations', 'TotalAchievements', 'TotalPointViolations', 'TotalPointAchievements', 'TotalDifference'];

                                foreach ($features as $feature) {
                                    foreach ($handlingCounts as $handlingID => $count) {
                                        $featureCounts = array_count_values(array_column($dataArray, $feature, 'HandlingID'));
                                        $conditionalProbabilities[$feature][$handlingID] = array();

                                        foreach (array_unique(array_column($dataArray, $feature)) as $value) {
                                            // Check if the key exists before accessing it
                                            if (isset($featureCounts[$handlingID][$value])) {
                                                $conditionalProbabilities[$feature][$handlingID][$value] = ($featureCounts[$handlingID][$value] + 1) / ($count + count(array_unique(array_column(
                                                    $dataArray,
                                                    $feature
                                                ))));
                                            } else {
                                                $conditionalProbabilities[$feature][$handlingID][$value] = 1 / ($count + count(array_unique(array_column($dataArray, $feature))));
                                            }
                                        }
                                    }
                                }

                                echo '<div class="flex flex-col w-full">';

                                // Testing with $dataArrayTest
                                // echo '<h2 class="text-xl font-semibold mb-2 mt-4">Testing with $dataArrayTest</h2>';
                                echo '<table class="table-auto">';
                                echo '<thead>';
                                echo '<tr>';
                                echo '<th class="border px-4 py-2">Student Name</th>';
                                echo '<th class="border px-4 py-2">Semester</th>';
                                echo '<th class="border px-4 py-2">Year</th>';
                                echo '<th class="border px-4 py-2">Violation IDs</th>';
                                echo '<th class="border px-4 py-2">Achievement IDs</th>';
                                echo '<th class="border px-4 py-2">Total Violations</th>';
                                echo '<th class="border px-4 py-2">Total Achievements</th>';
                                echo '<th class="border px-4 py-2">Total Point Violations</th>';
                                echo '<th class="border px-4 py-2">Total Point Achievements</th>';
                                echo '<th class="border px-4 py-2">Total Difference</th>';
                                // echo '<th class="border px-4 py-2">Actual HandlingID</th>';
                                echo '<th class="border px-4 py-2">Predicted HandlingID</th>';
                                echo '<th class="border px-4 py-2">Handling Category</th>';
                                echo '</tr>';
                                echo '</thead>';
                                echo '<tbody>';

                                // Fetch ViolationHandling data and store it in an associative array
                                $sql = "SELECT `HandlingID`, `ViolationCategory`, `ScoreRangeBottom`, `ScoreRangeTop`, `FollowUpAction` FROM `MasterViolationHandlings`";
                                $resultViolationHandling = mysqli_query($conn, $sql);

                                $violationHandlingData = array();

                                while ($row = mysqli_fetch_assoc($resultViolationHandling)) {
                                    $violationHandlingData[] = $row;
                                }

                                // Bagian Prediksinya

                                foreach ($dataArrayTest as $testData) {
                                    // Initialize probabilities
                                    $predictedProbabilities = array();
                                    $predictedHandlingID_ = $testData['HandlingID'];

                                    // Calculate posterior probabilities for each HandlingID
                                    foreach ($handlingCounts as $handlingID => $count) {
                                        $predictedProbabilities[$handlingID] = $priorProbabilities[$handlingID];

                                        foreach ($features as $feature) {
                                            // Check if the key exists before accessing it
                                            if (isset($conditionalProbabilities[$feature][$handlingID][$testData[$feature]])) {
                                                $predictedProbabilities[$handlingID] *= $conditionalProbabilities[$feature][$handlingID][$testData[$feature]];
                                            } else {
                                                $predictedProbabilities[$handlingID] *= 1 / ($count + count(array_unique(array_column($dataArray, $feature))));
                                            }
                                        }
                                    }

                                    // Get the predicted HandlingID with the maximum probability
                                    $predictedHandlingID = array_keys($predictedProbabilities, max($predictedProbabilities))[0];

                                    // Display results
                                    echo '<tr>';
                                    echo '<td class="py-2 px-4 border-b"><a href="manage_student_violationhandlings_detail.php?student_id=' . $testData['StudentID'] . '">' . $testData['StudentName'] . ' (' . $testData['StudentNumber'] . ')</a></td>';
                                    echo '<td class="border px-4 py-2">' . $testData['Semester'] . '</td>';
                                    echo '<td class="border px-4 py-2">' . $testData['Year'] . '</td>';
                                    echo '<td class="border px-4 py-2">' . ($testData['ViolationIDs'] ? $testData['ViolationIDs'] : '0') . '</td>';
                                    echo '<td class="border px-4 py-2">' . ($testData['AchievementIDs'] ? $testData['AchievementIDs'] : '0') . '</td>';
                                    echo '<td class="border px-4 py-2">' . $testData['TotalViolations'] . '</td>';
                                    echo '<td class="border px-4 py-2">' . $testData['TotalAchievements'] . '</td>';
                                    echo '<td class="border px-4 py-2">' . $testData['TotalPointViolations'] . '</td>';
                                    echo '<td class="border px-4 py-2">' . $testData['TotalPointAchievements'] . '</td>';
                                    echo '<td class="border px-4 py-2">' . $testData['TotalDifference'] . '</td>';
                                    // echo '<td class="border px-4 py-2">' . $testData['HandlingID'] . '</td>';
                                    echo '<td class="border px-4 py-2">' . $predictedHandlingID_ . '</td>';
                                    echo '<td class="border px-4 py-2">';
                                    // Display buttons for each ViolationCategory
                                    $buttonClasses = 'sweet-alert-btn text-white font-bold py-2 px-4 rounded';
                                    $iconClasses = 'fas fa-exclamation-circle mr-2';
                                    $foundMatchingHandling = false;
                                    foreach ($violationHandlingData as $row) {
                                        $violationCategory = $row['ViolationCategory'];
                                        $FollowUpAction = $row['FollowUpAction'];
                                        $handlingID = $row['HandlingID'];

                                        // Set color based on ViolationCategory
                                        switch ($violationCategory) {
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

                                        // Use the predictedHandlingID_ to find the corresponding HandlingID
                                        if ($predictedHandlingID_ == $handlingID) {
                                            echo '<button class="' . $buttonClasses . '" data-student-id="' . $testData['StudentID'] . '" data-category="' . $violationCategory . '" data-follow-up="' . $FollowUpAction . '"><i class="' . $iconClasses . '"></i>' . $violationCategory . '
                                        </button>';
                                            $foundMatchingHandling = true;
                                            break; // Exit the loop once a match is found
                                        }
                                    }
                                    // If no matching HandlingID is found, display "No Action"
                                    if (!$foundMatchingHandling) {
                                        echo 'No Action';
                                    }


                                    echo '</td>';
                                    echo '</tr>';
                                }

                                echo '</tbody>';
                                echo '</table>';
                                echo '</div>';

                                echo '</div>';

                                // Display results of Step 1: Count Total Data (n)
                                echo '<h2 class="text-xl font-semibold mb-2">Step 1: Count Total Data (n)</h2>';
                                echo '<table class="table-auto">';
                                echo '<thead>';
                                echo '<tr>';
                                echo '<th class="border px-4 py-2">Total Data (n)</th>';
                                echo '</tr>';
                                echo '</thead>';
                                echo '<tbody>';
                                echo '<tr>';
                                echo '<td class="border px-4 py-2">' . $totalData . '</td>';
                                echo '</tr>';
                                echo '</tbody>';
                                echo '</table>';

                                echo '<h2 class="text-xl font-semibold mb-2 mt-4">Step 2: Count Handling Data</h2>';
                                echo '<table class="table-auto">';
                                echo '<thead>';
                                echo '<tr>';
                                echo '<th class="border px-4 py-2">HandlingID</th>';
                                echo '<th class="border px-4 py-2">Count</th>';
                                echo '<th class="border px-4 py-2">ViolationCategory</th>';
                                echo '<th class="border px-4 py-2">ScoreRangeBottom</th>';
                                echo '<th class="border px-4 py-2">ScoreRangeTop</th>';
                                echo '<th class="border px-4 py-2">FollowUpAction</th>';
                                echo '</tr>';
                                echo '</thead>';
                                echo '<tbody>';

                                foreach ($handlingCounts as $handlingID => $count) {
                                    echo '<tr>';
                                    echo '<td class="border px-4 py-2">' . $handlingID . '</td>';
                                    echo '<td class="border px-4 py-2">' . $count . '</td>';

                                    // Find matching data from MasterViolationHandlings based on HandlingID
                                    $matchingHandlingData = array_filter($violationHandlingData, function ($data) use ($handlingID) {
                                        return $data['HandlingID'] == $handlingID;
                                    });

                                    if (!empty($matchingHandlingData)) {
                                        $matchingData = reset($matchingHandlingData); // Get the first matching data

                                        // Display additional information
                                        echo '<td class="border px-4 py-2">' . $matchingData['ViolationCategory'] . '</td>';
                                        echo '<td class="border px-4 py-2">' . $matchingData['ScoreRangeBottom'] . '</td>';
                                        echo '<td class="border px-4 py-2">' . $matchingData['ScoreRangeTop'] . '</td>';
                                        echo '<td class="border px-4 py-2">' . $matchingData['FollowUpAction'] . '</td>';
                                    } else {
                                        // If no matching data found, display placeholders
                                        echo '<td class="border px-4 py-2">N/A</td>';
                                        echo '<td class="border px-4 py-2">N/A</td>';
                                        echo '<td class="border px-4 py-2">N/A</td>';
                                        echo '<td class="border px-4 py-2">N/A</td>';
                                    }

                                    echo '</tr>';
                                }

                                echo '</tbody>';
                                echo '</table>';

                                echo '<h2 class="text-xl font-semibold mb-2 mt-4">Step 3: Calculate Prior Probabilities</h2>';
                                echo '<table class="table-auto">';
                                echo '<thead>';
                                echo '<tr>';
                                echo '<th class="border px-4 py-2">HandlingID</th>';
                                echo '<th class="border px-4 py-2">Prior Probability</th>';
                                echo '<th class="border px-4 py-2">ViolationCategory</th>'; // New column for ViolationCategory
                                echo '</tr>';
                                echo '</thead>';
                                echo '<tbody>';

                                foreach ($priorProbabilities as $handlingID => $probability) {
                                    echo '<tr>';
                                    echo '<td class="border px-4 py-2">' . $handlingID . '</td>';
                                    echo '<td class="border px-4 py-2">' . $probability . '</td>';

                                    // Find matching data from MasterViolationHandlings based on HandlingID
                                    $matchingHandlingData = array_filter($violationHandlingData, function ($data) use ($handlingID) {
                                        return $data['HandlingID'] == $handlingID;
                                    });

                                    if (!empty($matchingHandlingData)) {
                                        $matchingData = reset($matchingHandlingData); // Get the first matching data

                                        // Display ViolationCategory
                                        echo '<td class="border px-4 py-2">' . $matchingData['ViolationCategory'] . '</td>';
                                    } else {
                                        // If no matching data found, display "N/A" as a placeholder
                                        echo '<td class="border px-4 py-2">N/A</td>';
                                    }

                                    echo '</tr>';
                                }

                                echo '</tbody>';
                                echo '</table>';


                                echo '<h2 class="text-xl font-semibold mb-2 mt-4">Step 4: Calculate Conditional Probabilities</h2>';
                                foreach ($features as $feature) {
                                    echo '<h3 class="text-lg font-semibold mb-2">' . $feature . '</h3>';
                                    echo '<table class="table-auto">';
                                    echo '<thead>';
                                    echo '<tr>';
                                    echo '<th class="border px-4 py-2">HandlingID</th>';
                                    echo '<th class="border px-4 py-2">Feature Value</th>';
                                    echo '<th class="border px-4 py-2">Conditional Probability</th>';
                                    echo '</tr>';
                                    echo '</thead>';
                                    echo '<tbody>';

                                    foreach ($handlingCounts as $handlingID => $count) {
                                        foreach (array_unique(array_column($dataArray, $feature)) as $value) {
                                            echo '<tr>';
                                            echo '<td class="border px-4 py-2">' . $handlingID . '</td>';
                                            echo '<td class="border px-4 py-2">' . $value . '</td>';
                                            echo '<td class="border px-4 py-2">' . $conditionalProbabilities[$feature][$handlingID][$value] . '</td>';
                                            echo '</tr>';
                                        }
                                    }

                                    echo '</tbody>';
                                    echo '</table>';
                                }
                            }
                            ?>
                        </div>
                        <!-- End Display SemesterResultsTraining Table -->
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