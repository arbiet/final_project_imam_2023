<?php
session_start();
require_once('../../database/connection.php');
include_once('../components/header.php');

// Cek apakah user sudah login
if (!isset($_SESSION['UserID'])) {
    header('Location: login.php');
    exit();
}

// Cek apakah ID guru (TeacherID) disediakan dalam parameter query
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Redirect ke halaman error atau lokasi yang sesuai
    header('Location: error.php');
    exit();
}

$id = $_GET['id'];

// Inisialisasi pesan sukses dan pesan error
$success_message = '';
$error_message = '';

// Mulai transaksi
$conn->begin_transaction();

try {
    // Hapus catatan aktivitas terkait dari tabel "logactivity"
    $queryDeleteLogActivity = "DELETE FROM logactivity WHERE UserID = ?";
    $stmtDeleteLogActivity = $conn->prepare($queryDeleteLogActivity);
    $stmtDeleteLogActivity->bind_param('i', $id);

    if (!$stmtDeleteLogActivity->execute()) {
        throw new Exception("Gagal menghapus catatan aktivitas log.");
    }

    // Hapus guru (Teachers) terkait
    $queryDeleteTeacher = "DELETE FROM Teachers WHERE TeacherID = ?";
    $stmtDeleteTeacher = $conn->prepare($queryDeleteTeacher);
    $stmtDeleteTeacher->bind_param('i', $id);

    if (!$stmtDeleteTeacher->execute()) {
        throw new Exception("Gagal menghapus data guru.");
    }

    // Hapus pengguna (Users) terkait
    $queryDeleteUser = "DELETE FROM Users WHERE UserID = ?";
    $stmtDeleteUser = $conn->prepare($queryDeleteUser);
    $stmtDeleteUser->bind_param('i', $id);

    if (!$stmtDeleteUser->execute()) {
        throw new Exception("Gagal menghapus data pengguna.");
    }

    // Commit transaksi jika semua operasi berhasil
    $conn->commit();

    $success_message = "Data guru dan pengguna berhasil dihapus!";
} catch (Exception $e) {
    // Rollback transaksi jika terjadi kesalahan
    $conn->rollback();
    $error_message = $e->getMessage();
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
        window.location.href = 'manage_teachers_list.php'; // Redirect ke halaman daftar guru
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
        window location.href = 'manage_teachers_list.php'; // Redirect ke halaman daftar guru
    });
    </script>";
}

// Tutup koneksi database
$conn->close();
