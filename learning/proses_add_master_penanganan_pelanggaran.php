<?php
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

// Mendapatkan data dari form
$kategori_pelanggaran = $_POST['kategori_pelanggaran'];
$rentang_skor_bawah = $_POST['rentang_skor_bawah'];
$rentang_skor_atas = $_POST['rentang_skor_atas'];
$tindak_lanjut = $_POST['tindak_lanjut'];

// timestamp
$created_at = date("Y-m-d H:i:s");
$updated_at = date("Y-m-d H:i:s");

// Query untuk menyimpan data ke tabel master_penanganan_pelanggaran
$sql_insert = "INSERT INTO master_penanganan_pelanggaran (kategori_pelanggaran, rentang_skor_bawah, rentang_skor_atas, tindak_lanjut, created_at, updated_at) VALUES ('$kategori_pelanggaran', '$rentang_skor_bawah', '$rentang_skor_atas', '$tindak_lanjut', '$created_at', '$updated_at')";

if ($conn->query($sql_insert) === TRUE) {
    echo "Data penanganan pelanggaran berhasil ditambahkan.";
    \header("Location: tambah_master_penanganan_pelanggaran.php");
} else {
    echo "Error: " . $sql_insert . "<br>" . $conn->error;
}

// Menutup koneksi
$conn->close();
