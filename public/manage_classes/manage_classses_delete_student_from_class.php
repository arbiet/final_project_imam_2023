<?php
session_start();
require_once('../../database/connection.php');
include_once('../components/header.php');

// Cek apakah user sudah login
if (!isset($_SESSION['UserID'])) {
  header('Location: login.php');
  exit();
}

// Cek apakah ID kelas (class) disediakan dalam parameter query
if (!isset($_GET['class_id']) || !is_numeric($_GET['class_id'])) {
  // Redirect ke halaman error atau lokasi yang sesuai
  header('Location: error.php');
  exit();
}

// Cek apakah ID siswa (student) disediakan dalam parameter query
if (!isset($_GET['student_id']) || !is_numeric($_GET['student_id'])) {
  // Redirect ke halaman error atau lokasi yang sesuai
  header('Location: error.php');
  exit();
}

$classID = $_GET['class_id'];
$studentID = $_GET['student_id'];

// Inisialisasi pesan sukses dan pesan error
$success_message = '';
$error_message = '';

// Hapus hubungan siswa dengan kelas
$query = "UPDATE Students SET ClassID = NULL WHERE StudentID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $studentID);

if ($stmt->execute()) {
  // Deskripsi aktivitas
  $activityDescription = "Student with StudentID: $studentID has been removed from ClassID: $classID.";

  $currentUserID = $_SESSION['UserID'];
  insertLogActivity($conn, $currentUserID, $activityDescription);

  // Penghapusan hubungan siswa dengan kelas berhasil
  $stmt->close();
  $success_message = "Siswa berhasil dihapus dari kelas!";
} else {
  // Penghapusan hubungan siswa dengan kelas gagal
  $stmt->close();
  $error_message = "Gagal menghapus siswa dari kelas.";
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
        window.location.href = 'manage_classes_details.php?id=$classID'; // Redirect kembali ke halaman detail kelas
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
        window.location.href = 'manage_classes_detail.php?id=$classID'; // Redirect kembali ke halaman detail kelas
    });
    </script>";
}
