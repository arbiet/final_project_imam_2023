<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "skripsi-aziz";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Hash kata sandi default
$default_password = "12345678";
$hashed_default_password = hash('sha256', $default_password);

// Update semua kata sandi dalam tabel users
$sql = "UPDATE users SET password = '$hashed_default_password'";
if ($conn->query($sql) === TRUE) {
    echo "All passwords updated successfully.";
} else {
    echo "Error updating passwords: " . $conn->error;
}

$conn->close();
