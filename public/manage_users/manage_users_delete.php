<?php
session_start();
require_once('../../database/connection.php');
include_once('../components/header.php');
// Cek apakah user sudah login
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit();
}

// Cek apakah ID pengguna (user) disediakan dalam parameter query
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirect ke halaman error atau lokasi yang sesuai
    header('Location: error.php');
    exit();
}

$id = $_GET['id'];

// Inisialisasi pesan sukses dan pesan error
$success_message = '';
$error_message = '';

// Lakukan penghapusan catatan terkait dari tabel "logactivity"
$query = "DELETE FROM logactivity WHERE UserID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    // Penghapusan catatan terkait berhasil
    $stmt->close();

    // Sekarang, kita bisa melanjutkan dengan menghapus pengguna
    $query = "DELETE FROM users WHERE UserID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $id);

    if ($stmt->execute()) {
        // Deskripsi aktivitas
        $activityDescription = "User with UserID: $id has been deleted.";

        $currentUserID = $_SESSION['UserID'];
        insertLogActivity($conn, $currentUserID, $activityDescription);

        // Penghapusan pengguna berhasil
        $stmt->close();
        $success_message = "Pengguna berhasil dihapus!";
    } else {
        // Penghapusan pengguna gagal
        $stmt->close();
        $error_message = "Gagal menghapus pengguna.";
    }
} else {
    // Penghapusan catatan terkait gagal
    $stmt->close();
    $error_message = "Gagal menghapus catatan terkait dari logactivity.";
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
        window.location.href = 'manage_users_list.php'; // Redirect ke halaman daftar pengguna
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
        window.location.href = 'manage_users_list.php'; // Redirect ke halaman daftar pengguna
    });
    </script>";
}
?>

<div class="h-screen flex flex-col">
    <?php include_once('../components/navbar.php'); ?>
    <?php include('../components/footer.php'); ?>
</div>