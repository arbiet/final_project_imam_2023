<?php
// Initialize the session
session_start();

// Include the connection file
require_once('../../database/connection.php');

// Set the initial month and year to 6 semesters ago
$selectedMonth = date('n');
$selectedYear = date('Y');
$semestersAgo = 6;

// Calculate the starting semester and year
$selectedMonth -= ($semestersAgo * 6); // Subtracting months for the past 6 semesters
while ($selectedMonth <= 0) {
    $selectedMonth += 12;
    $selectedYear--;
}

// Determine the current semester based on the month
$currentSemester = ($selectedMonth >= 1 && $selectedMonth <= 6) ? 2 : 1;

// Initialize arrays to store semester data and results
$semesterResults = [];
$semesterResultsClean = [];
$semesterResultsTraining = [];
$semesterResultsTesting = [];

// Loop to generate data for the current semester and the past 6 semesters
for ($i = 0; $i < 6; $i++) {
    // Calculate the semester and year for each iteration
    $semester = ($currentSemester - $i) % 2 == 0 ? 2 : 1;
    $preSemester = ($semester == 1) ? 1 : 2;
    $year = $selectedYear - floor(($currentSemester - $i - 1) / 2);

    // Calculate start and end months based on the semester
    $startMonth = ($semester == 2) ? 7 : 1;
    $prevStartMonth = ($semester == 1) ? 1 : 7;
    $endMonth = ($semester == 2) ? 12 : 6;
    $prevEndMonth = ($semester == 1) ? 6 : 12;
    $prevYear = $year - 1;

    // Store the semester information
    $semesterInfo = [
        'semester' => $semester,
        'preSemester' => $preSemester,
        'year' => $year,
        'prevYear' => $prevYear,
        'startMonth' => $startMonth,
        'endMonth' => $endMonth,
        'prevStartMonth' => $prevStartMonth,
        'prevEndMonth' => $prevEndMonth,
    ];

    // Fetch data for each student in the specified semester and year
    $query = "SELECT
              Students.StudentID,
              Students.StudentNumber,
              Users.FullName AS StudentName,
              GROUP_CONCAT(DISTINCT CASE WHEN MasterViolations.ViolationID IS NOT NULL AND YEAR(StudentViolations.Date) = $year AND MONTH(StudentViolations.Date) BETWEEN $startMonth AND $endMonth THEN StudentViolations.ViolationID END) AS ViolationIDs,
              GROUP_CONCAT(DISTINCT CASE WHEN MasterAchievements.AchievementID IS NOT NULL AND YEAR(StudentAchievements.Date) = $year AND MONTH(StudentAchievements.Date) BETWEEN $startMonth AND $endMonth THEN StudentAchievements.AchievementID END) AS AchievementIDs,
              -- Data for the selected semester
              COUNT(DISTINCT CASE WHEN MasterViolations.ViolationID IS NOT NULL AND YEAR(StudentViolations.Date) = $year AND MONTH(StudentViolations.Date) BETWEEN $startMonth AND $endMonth THEN StudentViolations.ViolationID END) AS TotalViolations,
              COUNT(DISTINCT CASE WHEN MasterAchievements.AchievementID IS NOT NULL AND YEAR(StudentAchievements.Date) = $year AND MONTH(StudentAchievements.Date) BETWEEN $startMonth AND $endMonth THEN StudentAchievements.AchievementID END) AS TotalAchievements,
              COALESCE(SUM(DISTINCT CASE WHEN MasterViolations.ViolationID IS NOT NULL AND YEAR(StudentViolations.Date) = $year AND MONTH(StudentViolations.Date) BETWEEN $startMonth AND $endMonth THEN MasterViolations.Points END), 0) AS TotalPointViolations,
              COALESCE(SUM(DISTINCT CASE WHEN MasterAchievements.AchievementID IS NOT NULL AND YEAR(StudentAchievements.Date) = $year AND MONTH(StudentAchievements.Date) BETWEEN $startMonth AND $endMonth THEN MasterAchievements.Points END), 0) AS TotalPointAchievements,
              (COALESCE(SUM(DISTINCT CASE WHEN MasterViolations.ViolationID IS NOT NULL AND YEAR(StudentViolations.Date) = $year AND MONTH(StudentViolations.Date) BETWEEN $startMonth AND $endMonth THEN MasterViolations.Points END), 0) -
              COALESCE(SUM(DISTINCT CASE WHEN MasterAchievements.AchievementID IS NOT NULL AND YEAR(StudentAchievements.Date) = $year AND MONTH(StudentAchievements.Date) BETWEEN $startMonth AND $endMonth THEN MasterAchievements.Points END), 0)) AS TotalDifference,
              -- Data for the previous semester
              COUNT(DISTINCT CASE WHEN MasterViolations.ViolationID IS NOT NULL AND YEAR(StudentViolations.Date) = $prevYear AND MONTH(StudentViolations.Date) BETWEEN $prevStartMonth AND $prevEndMonth THEN StudentViolations.ViolationID END) AS PrevTotalViolations,
              COUNT(DISTINCT CASE WHEN MasterAchievements.AchievementID IS NOT NULL AND YEAR(StudentAchievements.Date) = $prevYear AND MONTH(StudentAchievements.Date) BETWEEN $prevStartMonth AND $prevEndMonth THEN StudentAchievements.AchievementID END) AS PrevTotalAchievements,
              COALESCE(SUM(DISTINCT CASE WHEN MasterViolations.ViolationID IS NOT NULL AND YEAR(StudentViolations.Date) = $prevYear AND MONTH(StudentViolations.Date) BETWEEN $prevStartMonth AND $prevEndMonth THEN MasterViolations.Points END), 0) AS PrevTotalPointViolations,
              COALESCE(SUM(DISTINCT CASE WHEN MasterAchievements.AchievementID IS NOT NULL AND YEAR(StudentAchievements.Date) = $prevYear AND MONTH(StudentAchievements.Date) BETWEEN $prevStartMonth AND $prevEndMonth THEN MasterAchievements.Points END), 0) AS PrevTotalPointAchievements,
              -- Data for all previous semesters
              COUNT(DISTINCT CASE WHEN MasterViolations.ViolationID IS NOT NULL AND YEAR(StudentViolations.Date) < $year THEN StudentViolations.ViolationID END) AS AllPrevTotalViolations,
              COUNT(DISTINCT CASE WHEN MasterAchievements.AchievementID IS NOT NULL AND YEAR(StudentAchievements.Date) < $year THEN StudentAchievements.AchievementID END) AS AllPrevTotalAchievements,
              COALESCE(SUM(DISTINCT CASE WHEN MasterViolations.ViolationID IS NOT NULL AND YEAR(StudentViolations.Date) < $year THEN MasterViolations.Points END), 0) AS AllPrevTotalPointViolations,
              COALESCE(SUM(DISTINCT CASE WHEN MasterAchievements.AchievementID IS NOT NULL AND YEAR(StudentAchievements.Date) < $year THEN MasterAchievements.Points END), 0) AS AllPrevTotalPointAchievements,
              -- Data for all previous semesters difference
              (COALESCE(SUM(DISTINCT CASE WHEN MasterViolations.ViolationID IS NOT NULL AND YEAR(StudentViolations.Date) < $year THEN MasterViolations.Points END), 0) -
               COALESCE(SUM(DISTINCT CASE WHEN MasterAchievements.AchievementID IS NOT NULL AND YEAR(StudentAchievements.Date) < $year THEN MasterAchievements.Points END), 0)) AS AllPrevTotalDifference
          FROM Students
          LEFT JOIN Users ON Students.UserID = Users.UserID
          LEFT JOIN StudentViolations ON Students.StudentID = StudentViolations.StudentID
          LEFT JOIN MasterViolations ON StudentViolations.ViolationID = MasterViolations.ViolationID
          LEFT JOIN StudentAchievements ON Students.StudentID = StudentAchievements.StudentID
          LEFT JOIN MasterAchievements ON StudentAchievements.AchievementID = MasterAchievements.AchievementID
        --   Conditions for the selected semester
          WHERE (YEAR(StudentViolations.Date) = $year AND MONTH(StudentViolations.Date) BETWEEN $startMonth AND $endMonth)
             OR (YEAR(StudentAchievements.Date) = $year AND MONTH(StudentAchievements.Date) BETWEEN $startMonth AND $endMonth)
        --   -- Conditions for the previous semester
        --      OR (YEAR(StudentViolations.Date) = $prevYear AND MONTH(StudentViolations.Date) BETWEEN $prevStartMonth AND $prevEndMonth)
        --      OR (YEAR(StudentAchievements.Date) = $prevYear AND MONTH(StudentAchievements.Date) BETWEEN $prevStartMonth AND $prevEndMonth)
        --   -- Conditions for all previous semesters
        --      OR (YEAR(StudentViolations.Date) < $year)
        --      OR (YEAR(StudentAchievements.Date) < $year)
          GROUP BY Students.StudentID";
    $result = mysqli_query($conn, $query);

    // Fetch results and add them to the semesterResults array
    $semesterResults[] = [
        'semesterInfo' => $semesterInfo,
        'results' => mysqli_fetch_all($result, MYSQLI_ASSOC),
    ];
}

// Sort the semesterResults array based on semester and year
usort($semesterResults, function ($a, $b) {
    // Compare semesters first
    $semesterComparison = $a['semesterInfo']['semester'] - $b['semesterInfo']['semester'];

    // If semesters are equal, compare years
    return ($semesterComparison == 0) ? ($a['semesterInfo']['year'] - $b['semesterInfo']['year']) : $semesterComparison;
});

/// Populate $semesterResultsClean with entries where TotalDifference is greater than or equal to 0
foreach ($semesterResults as $semesterResult) {
    $semesterInfo = $semesterResult['semesterInfo'];
    $results = $semesterResult['results'];

    // Temporary array to store cleaned student data for the current semester
    $cleanedResults = [];

    foreach ($results as $result) {
        $totalDifference = $result['TotalDifference'];

        // Check if TotalDifference is greater than or equal to 0
        if ($totalDifference >= 0) {
            // Fetch 'HandlingID' based on 'TotalDifference' within the score range
            $handlingQuery = "SELECT HandlingID
                      FROM MasterViolationHandlings
                      WHERE '{$result['TotalDifference']}' BETWEEN ScoreRangeBottom AND ScoreRangeTop";

            $handlingResult = mysqli_query($conn, $handlingQuery);

            if ($handlingResult) {
                $handlingData = mysqli_fetch_assoc($handlingResult);
                if (isset($handlingData['HandlingID'])) {
                    $result['HandlingID'] = $handlingData['HandlingID'];
                } else {
                    $result['HandlingID'] = NULL;
                }
                $cleanedResults[] = $result;
            }
        }
    }


    // Add the cleaned data to $semesterResultsClean only if there are valid entries
    if (!empty($cleanedResults)) {
        $semesterResultsClean[] = [
            'semesterInfo' => $semesterInfo,
            'results' => $cleanedResults,
        ];
    }
}

// Check if $semesterResultsClean is not empty
if (!empty($semesterResultsClean)) {
    $semesterResultsTraining = $semesterResultsClean;
    // Separate the last entry from $semesterResultsClean
    $lastEntry = array_pop($semesterResultsClean);

    // Add the last entry to $semesterResultsTest
    $semesterResultsTesting[] = $lastEntry;

    $semesterResultsClean = $semesterResultsTraining;
}

// Function to check if data exists in a table based on specified parameters
function isDataExists($conn, $tableName, $semesterInfo, $studentID)
{
    $query = "SELECT DataID FROM $tableName 
              WHERE Semester = {$semesterInfo['semester']} 
              AND Year = {$semesterInfo['year']} 
              AND StartMonth = {$semesterInfo['startMonth']} 
              AND EndMonth = {$semesterInfo['endMonth']} 
              AND StudentID = $studentID";
    $result = mysqli_query($conn, $query);

    return mysqli_fetch_assoc($result);
}

// Function to insert or update data in a table
function insertOrUpdateData($conn, $tableName, $semesterInfo, $studentID, $data)
{
    $existingData = isDataExists($conn, $tableName, $semesterInfo, $studentID);

    if ($existingData) {
        // Data exists, perform an update
        $dataID = $existingData['DataID'];
        $updateQuery = "UPDATE $tableName SET ";

        foreach ($data as $key => $value) {
            $updateQuery .= "$key = '$value', ";
        }

        $updateQuery = rtrim($updateQuery, ", ");
        $updateQuery .= " WHERE DataID = $dataID";

        mysqli_query($conn, $updateQuery);
    } else {
        // Data doesn't exist, perform an insert
        $insertColumns = implode(', ', array_keys($data));
        $insertValues = "'" . implode("', '", array_values($data)) . "'";

        $insertQuery = "INSERT INTO $tableName ($insertColumns) VALUES ($insertValues)";
        mysqli_query($conn, $insertQuery);
    }
}

// Loop through the results for SemesterResultsTraining
foreach ($semesterResultsTraining as $semesterResult) {
    $semesterInfo = $semesterResult['semesterInfo'];
    $results = $semesterResult['results'];

    foreach ($results as $result) {
        $data = array_merge($semesterInfo, $result);
        insertOrUpdateData($conn, 'SemesterResultsTraining', $semesterInfo, $result['StudentID'], $data);
    }
}

// Loop through the results for SemesterResultsTesting
foreach ($semesterResultsTesting as $semesterResult) {
    $semesterInfo = $semesterResult['semesterInfo'];
    $results = $semesterResult['results'];

    foreach ($results as &$result) {
        // Calculate NaiveBayes from HandlingID (modify this part based on your logic)
        $handlingID = $result['HandlingID'];

        $naiveBayes = $handlingID;

        // Add NaiveBayes to the result array
        $result['NaiveBayes'] = $naiveBayes;

        // Merge semesterInfo and result
        $data = array_merge($semesterInfo, $result);

        // Insert or update data
        insertOrUpdateData($conn, 'SemesterResultsTesting', $semesterInfo, $result['StudentID'], $data);
    }
}
