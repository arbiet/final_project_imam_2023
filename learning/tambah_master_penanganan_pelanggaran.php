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
    <title>Form Tambah Master Penanganan Pelanggaran</title>
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
    <h2>Form Tambah Master Penanganan Pelanggaran</h2>

    <form action="proses_add_master_penanganan_pelanggaran.php" method="POST">
        <label for="kategori_pelanggaran">Kategori pelanggaran:</label>
        <input type="text" id="kategori_pelanggaran" name="kategori_pelanggaran" required><br><br>

        <label for="rentang_skor_bawah">Rentang Skor Bawah:</label>
        <input type="number" id="rentang_skor_bawah" name="rentang_skor_bawah" required><br><br>

        <label for="rentang_skor_atas">Rentang Skor Atas:</label>
        <input type="number" id="rentang_skor_atas" name="rentang_skor_atas" required><br><br>

        <label for="tindak_lanjut">Tindak Lanjut:</label>
        <textarea id="tindak_lanjut" name="tindak_lanjut" required></textarea><br><br>

        <input type="submit" value="Tambah">
    </form>

    <?php
    // Query untuk mengambil data dari database
    $sql = "SELECT * FROM master_penanganan_pelanggaran";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Kategori Pelanggaran</th><th>Rentang Skor Bawah</th><th>Rentang Skor Atas</th><th>Tindak Lanjut</th><th>Aksi</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["kategori_pelanggaran"] . "</td>";
            echo "<td>" . $row["rentang_skor_bawah"] . "</td>";
            echo "<td>" . $row["rentang_skor_atas"] . "</td>";
            echo "<td>" . $row["tindak_lanjut"] . "</td>";
            echo "<td><a href='form_edit_master_penanganan_pelanggaran.php?id=" . $row["id"] . "'>Edit</a> | <a href='proses_delete_master_penanganan_pelanggaran.php?id=" . $row["id"] . "'>Hapus</a></td>";
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