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
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  // Redirect ke halaman error atau lokasi yang sesuai
  header('Location: error.php');
  exit();
}

$id = $_GET['id'];

// Inisialisasi pesan sukses dan pesan error
$success_message = '';
$error_message = '';

// Lakukan penghapusan kelas
$query = "DELETE FROM Classes WHERE ClassID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
  // Deskripsi aktivitas
  $activityDescription = "Class with ClassID: $id has been deleted.";

  $currentUserID = $_SESSION['UserID'];
  insertLogActivity($conn, $currentUserID, $activityDescription);

  // Penghapusan kelas berhasil
  $stmt->close();
  $success_message = "Kelas berhasil dihapus!";
} else {
  // Penghapusan kelas gagal
  $stmt->close();
  $error_message = "Gagal menghapus kelas.";
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
        window.location.href = 'manage_classes_list.php'; // Redirect ke halaman daftar kelas
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
        window.location.href = 'manage_classes_list.php'; // Redirect ke halaman daftar kelas
    });
    </script>";
}
?>

<div class="h-screen flex flex-col">
</div>