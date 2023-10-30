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
?>

<!DOCTYPE html>
<html>

<head>
    <title>Form Tambah Master Pelanggaran</title>
</head>

<body>
    <!-- navigasi -->
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="tambah_siswa.php">Form Data Siswa</a></li>
        <li><a href="tambah_pelanggaran.php">Form Data Pelanggaran</a></li>
        <li><a href="tambah_prestasi.php">Form Data Prestasi</a></li>
        <li><a href="tambah_master_pelanggaran.php">Form Data Master Pelanggaran</a></li>
        <li><a href="tambah_master_prestasi.php">Form Data Master Prestasi</a></li>
        <li><a href="tambah_master_penanganan_pelanggaran.php">Form Data Master Penanganan Pelanggaran</a></li>
        <li><a href="perhitungan.php">Perhitungan Persemester</a></li>
        <li><a href="naive_bayes.php">Perhitungan Naive Bayes</a></li>
    </ul>
    <h2>Form Tambah Master Pelanggaran</h2>

    <form action="proses_add_master_pelanggaran.php" method="POST">
        <label for="jenis_pelanggaran">Jenis Pelanggaran:</label>

        <input type="text" id="jenis_pelanggaran" name="jenis_pelanggaran" required><br><br>

        <label for="pelanggaran">Pelanggaran:</label>
        <textarea id="pelanggaran" name="pelanggaran" required></textarea><br><br>

        <label for="poin">Poin:</label>
        <input type="number" id="poin" name="poin" required><br><br>

        <input type="submit" value="Tambah">
    </form>

    <?php


    // Query untuk mengambil data dari database
    $sql = "SELECT * FROM master_pelanggaran";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Jenis Pelanggaran</th><th>Pelanggaran</th><th>Poin</th><th>Aksi</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["jenis_pelanggaran"] . "</td>";
            echo "<td>" . $row["pelanggaran"] . "</td>";
            echo "<td>" . $row["poin"] . "</td>";
            echo "<td><a href='form_edit_master_pelanggaran.php?id=" . $row["id"] . "'>Edit</a> | <a href='proses_delete_master_pelanggaran.php?id=" . $row["id"] . "'>Hapus</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Tidak ada data.";
    }

    // Menutup koneksi
    $conn->close();
    ?>
</body>

</html>