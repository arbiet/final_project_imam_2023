<?php
session_start();
require_once('../../database/connection.php');
include_once('../components/header.php');

// Check if the user is logged in
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit();
}

// Check if the user's ID is provided in the query string
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: error.php'); // Redirect to an error page or an appropriate location
    exit();
}

$id = $_GET['id'];

// Initialize success and error messages
$success_message = '';
$error_message = '';

// Get the user's current activation status from the database
$query = "SELECT ActivationStatus FROM Users WHERE UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($currentStatus);
$stmt->fetch();
$stmt->close();

// Determine the new status based on the current status
if ($currentStatus === 'pending' || $currentStatus === NULL) {
    $newStatus = 'active';
} elseif ($currentStatus === 'active') {
    $newStatus = 'disabled';
} else {
    $newStatus = 'active'; // You can set the default status here
}

// Determine the new status based on the current status
if ($currentStatus === 'pending' || $currentStatus === NULL) {
    $newStatus = 'active';
} elseif ($currentStatus === 'active') {
    $newStatus = 'disabled';
} else {
    $newStatus = 'active'; // You can set the default status here
}

// Update the user's activation status
$query = "UPDATE Users SET ActivationStatus = ? WHERE UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('si', $newStatus, $id);

if ($stmt->execute()) {
    $success_message = "User's activation status has been updated to $newStatus.";

    // Log the activity for the user deletion
    $activityDescription = "User with UserID: $id has been deleted.";
    $currentUserID = $_SESSION['UserID'];

    // Call the insertLogActivity function to log the activity
    insertLogActivity($conn, $currentUserID, $activityDescription);
    $stmt->close();
    $success_message = "Users activation status has been updated to $newStatus.";
} else {
    // Update failed
    $stmt->close();
    $error_message = "Failed to update user's activation status.";
}

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
        window.location.href = 'manage_users_list.php'; // Redirect to the user list page
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
        window.location.href = 'manage_users_list.php'; // Redirect to the user list page
    });
    </script>";
}
?>
<div class="h-screen flex flex-col">
</div>