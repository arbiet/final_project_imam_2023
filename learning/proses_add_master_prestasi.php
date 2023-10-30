<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $jenis_prestasi = $_POST["jenis_prestasi"];
    $prestasi = $_POST["prestasi"];
    $poin = $_POST["poin"];

    // timestamp
    // created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    // updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

    $created_at = date("Y-m-d H:i:s");
    $updated_at = date("Y-m-d H:i:s");


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

    // Query untuk menyimpan data ke database
    $sql = "INSERT INTO master_prestasi (jenis_prestasi, nama_prestasi, poin, created_at, updated_at)
            VALUES ('$jenis_prestasi', '$prestasi', '$poin', '$created_at', '$updated_at')";

    if ($conn->query($sql) === TRUE) {
        echo "Data berhasil disimpan ke database.";
        header("Location: tambah_master_prestasi.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Menutup koneksi
    $conn->close();
}
