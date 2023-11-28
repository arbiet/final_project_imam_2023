<?php
session_start();
require_once('../../database/connection.php');
include_once('../components/header.php');

if (!isset($_SESSION['UserID'])) {
  header('Location: login.php');
  exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header('Location: error.php');
  exit();
}

$id = $_GET['id'];

$success_message = '';
$error_message = '';

// Reset the user's password to "12345678"
$password = "12345678";
$new_password = hash('sha256', $password);

$query = "UPDATE Users SET Password = ? WHERE UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('si', $new_password, $id);

if ($stmt->execute()) {
  $stmt->close();
  $success_message = "Password reset successfully!";
} else {
  $stmt->close();
  $error_message = "Password reset failed.";
}

if (!empty($success_message)) {
  echo "<script>
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '$success_message',
        showConfirmButton: false,
        timer: 1500
    }).then(function() {
        window.location.href = 'manage_users_list.php'; // Redirect to the user management page
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
        window.location.href = 'manage_users_list.php'; // Redirect to the user management page
    });
    </script>";
}
