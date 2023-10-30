<?php
// Ambil id data yang akan dihapus dari URL
$id = $_GET["id"];

// Konfigurasi koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$database = "skripsi-imam";

// Membuat koneksi
$conn = new mysqli($servername, $username, $password, $database);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk menghapus data berdasarkan id
$sql = "DELETE FROM data_siswa WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "Data berhasil dihapus.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Menutup koneksi
$conn->close();

// Mengalihkan kembali ke halaman tambah_siswa.php
header("Location: tambah_siswa.php");
exit();
