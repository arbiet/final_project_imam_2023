<?php
session_start();
require_once('../../database/connection.php');
include_once('../components/header.php');

// Cek apakah user sudah login
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit();
}

// Cek apakah ID kelas (class) dan ID subjek (subject) disediakan dalam parameter query
if (!isset($_GET['class_id']) || !is_numeric($_GET['class_id']) || !isset($_GET['subject_id']) || !is_numeric($_GET['subject_id'])) {
    // Redirect ke halaman error atau lokasi yang sesuai
    header('Location: error.php');
    exit();
}

$classID = $_GET['class_id'];
$subjectID = $_GET['subject_id'];

// Inisialisasi pesan sukses dan pesan error
$success_message = '';
$error_message = '';

// Lakukan penghapusan subjek dari kelas
$query = "DELETE FROM ClassSubjects WHERE ClassID = ? AND SubjectID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ii', $classID, $subjectID);

if ($stmt->execute()) {
    // Deskripsi aktivitas
    $activityDescription = "Subject with SubjectID: $subjectID has been removed from ClassID: $classID.";

    $currentUserID = $_SESSION['UserID'];
    insertLogActivity($conn, $currentUserID, $activityDescription);

    // Penghapusan subjek dari kelas berhasil
    $stmt->close();
    $success_message = "Subjek berhasil dihapus dari kelas!";
} else {
    // Penghapusan subjek dari kelas gagal
    $stmt->close();
    $error_message = "Gagal menghapus subjek dari kelas.";
}

// Tampilkan pesan sukses atau pesan error dengan SweetAlert2
if (!empty($success_message)) {
    echo "<script>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '$success_message',
        showConfirmButton: false,
        timer: 1500
    }).then(function() {
        window.location.href = 'manage_classes_detail.php?id=$classID'; // Redirect ke halaman detail kelas
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
        window.location.href = 'manage_classes_detail.php?id=$classID'; // Redirect ke halaman detail kelas
    });
    </script>";
}
?>
<div class="h-screen flex flex-col">
</div>