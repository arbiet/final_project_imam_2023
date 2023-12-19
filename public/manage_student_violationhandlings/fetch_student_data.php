<?php
// Include the connection file
require_once('../../database/connection.php');

if (isset($_GET['student_id'])) {
    $studentID = $_GET['student_id'];

    // Perform SQL query to fetch student data
    $query = "SELECT
                Students.StudentID,
                Students.StudentNumber,
                Users.FullName AS StudentName,
                Students.Religion,
                Students.ParentGuardianFullName,
                Students.ParentGuardianAddress,
                Students.ParentGuardianPhoneNumber,
                Students.ParentGuardianEmail,
                Classes.ClassName,
                StudentViolations.Date AS ViolationDate,
                MasterViolations.ViolationType,
                MasterViolations.ViolationName AS ViolationName,
                MasterViolations.Points AS ViolationPoints,
                StudentAchievements.Date AS AchievementDate,
                MasterAchievements.AchievementType,
                MasterAchievements.AchievementName AS AchievementName,
                MasterAchievements.Points AS AchievementPoints
            FROM Students
            LEFT JOIN Users ON Students.UserID = Users.UserID
            LEFT JOIN Classes ON Students.ClassID = Classes.ClassID
            LEFT JOIN StudentViolations ON Students.StudentID = StudentViolations.StudentID
            LEFT JOIN MasterViolations ON StudentViolations.ViolationID = MasterViolations.ViolationID
            LEFT JOIN StudentAchievements ON Students.StudentID = StudentAchievements.StudentID
            LEFT JOIN MasterAchievements ON StudentAchievements.AchievementID = MasterAchievements.AchievementID
            WHERE Students.StudentID = '$studentID'";

    $result = mysqli_query($conn, $query);

    if ($result) {
        $studentData = mysqli_fetch_assoc($result);
        echo json_encode($studentData);
    } else {
        echo json_encode(['error' => 'Error fetching student data']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}

// Close the database connection
mysqli_close($conn);
?>