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
$siswa_id = $_POST['siswa'];
$prestasi_id = $_POST['prestasi'];
$tanggal = $_POST['tanggal'];
$jam = $_POST['jam'];
$nama_prestasi = $_POST['nama_prestasi'];
$penyelengara = $_POST['penyelengara'];
$juara = $_POST['juara'];
$detail = $_POST['detail'];

// timestamp
// created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
// updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

$created_at = date("Y-m-d H:i:s");
$updated_at = date("Y-m-d H:i:s");


// Query untuk menyimpan data ke tabel data_prestasi
$sql_insert = "INSERT INTO data_prestasi (siswa_id, prestasi_id, tanggal, jam, nama_prestasi, penyelengara, juara, detail, created_at, updated_at) VALUES ('$siswa_id', '$prestasi_id', '$tanggal', '$jam', '$nama_prestasi', '$penyelengara', '$juara', '$detail', '$created_at', '$updated_at')";

if ($conn->query($sql_insert) === TRUE) {
    echo "Data prestasi berhasil ditambahkan.";
} else {
    echo "Error: " . $sql_insert . "<br>" . $conn->error;
}

// Menutup koneksi
$conn->close();
