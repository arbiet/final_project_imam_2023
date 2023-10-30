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
$pelanggaran_id = $_POST['pelanggaran'];
$tanggal = $_POST['tanggal'];
$jam = $_POST['jam'];

// timestamp
// created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
// updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

$created_at = date("Y-m-d H:i:s");
$updated_at = date("Y-m-d H:i:s");


echo "Siswa ID: " . $siswa_id . "<br>";
echo "Pelanggaran ID: " . $pelanggaran_id . "<br>";
echo "Tanggal: " . $tanggal . "<br>";
echo "Jam: " . $jam . "<br>";

// Query untuk menyimpan data ke tabel data_pelanggaran
$sql_insert = "INSERT INTO data_pelanggaran (siswa_id, pelanggaran_id, tanggal, jam, created_at, updated_at)
              VALUES ('$siswa_id', '$pelanggaran_id', '$tanggal', '$jam', '$created_at', '$updated_at')";

if ($conn->query($sql_insert) === TRUE) {
    echo "Data pelanggaran berhasil ditambahkan.";
    header("Location: tambah_master_pelanggaran.php");
} else {
    echo "Error: " . $sql_insert . "<br>" . $conn->error;
}

// Menutup koneksi
$conn->close();
