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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $no = $_POST["no"];
    $nama = $_POST["nama"];
    $nipd = $_POST["nipd"];
    $jenis_kelamin = $_POST["jenis_kelamin"];
    $nisn = $_POST["nisn"];
    $tempat_lahir = $_POST["tempat_lahir"];
    $tanggal_lahir = $_POST["tanggal_lahir"];
    $nik = $_POST["nik"];
    $agama = $_POST["agama"];
    // timestamp
    // created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    // updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

    $created_at = date("Y-m-d H:i:s");
    $updated_at = date("Y-m-d H:i:s");


    // Query untuk menyimpan data ke database
    $sql = "INSERT INTO data_siswa (no, nama, nipd, jenis_kelamin, nisn, tempat_lahir, tanggal_lahir, nik, agama, created_at, updated_at)
     VALUES ('$no', '$nama', '$nipd', '$jenis_kelamin', '$nisn', '$tempat_lahir', '$tanggal_lahir', '$nik', '$agama', '$created_at', '$updated_at')";

    if ($conn->query($sql) === TRUE) {
        echo "Data berhasil disimpan ke database.";
        header("Location: tambah_siswa.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Menutup koneksi
$conn->close();
