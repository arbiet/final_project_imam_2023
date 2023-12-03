<?php
session_start();
require_once('../../database/connection.php');
include_once('../components/header.php');

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit();
}

// Check if the achievement ID is provided in the query parameter
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirect to an error page or appropriate location
    header('Location: error.php');
    exit();
}

$id = $_GET['id'];

// Initialize success and error messages
$success_message = '';
$error_message = '';

// Perform the deletion of the achievement
$query = "DELETE FROM MasterAchievements WHERE AchievementID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    // Log activity description
    $activityDescription = "Achievement with AchievementID: $id has been deleted.";

    $currentUserID = $_SESSION['UserID'];
    insertLogActivity($conn, $currentUserID, $activityDescription);

    // Deletion successful
    $stmt->close();
    $success_message = "Achievement deleted successfully!";
} else {
    // Deletion failed
    $stmt->close();
    $error_message = "Failed to delete achievement.";
}

// Update related tables to handle foreign key constraints
// Note: You may need to customize this based on your specific relationships
$queryUpdateRelatedTables = "UPDATE StudentAchievements SET AchievementID = NULL WHERE AchievementID = ?";
$stmtUpdateRelatedTables = $conn->prepare($queryUpdateRelatedTables);
$stmtUpdateRelatedTables->bind_param('i', $id);
$stmtUpdateRelatedTables->execute();
$stmtUpdateRelatedTables->close();

// Display success or error message using SweetAlert2
if (!empty($success_message)) {
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '$success_message',
            showConfirmButton: false,
            timer: 1500
        }).then(function() {
            window.location.href = 'manage_master_achievements_list.php'; // Redirect to the achievements list page
        });
        </script>";
} elseif (!empty($error_message)) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '$error_message',
            showConfirmButton: false,
            timer: 1500
        }).then(function() {
            window.location.href = 'manage_master_achievements_list.php'; // Redirect to the achievements list page
        });
        </script>";
}
?>

<div class="h-screen flex flex-col">
    <!-- Add any additional content or structure as needed -->
</div>