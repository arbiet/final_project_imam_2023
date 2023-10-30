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
    <title>Form Tambah Pelanggaran</title>
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
    <h2>Form Tambah Pelanggaran</h2>

    <form action="proses_add_pelanggaran.php" method="POST">
        <label for="siswa">Siswa:</label>
        <select id="siswa" name="siswa" required>
            <!-- Isi pilihan siswa dari database -->
            <?php
            $sql_siswa = "SELECT id, nama FROM data_siswa";
            $result_siswa = $conn->query($sql_siswa);

            if ($result_siswa->num_rows > 0) {
                while ($row_siswa = $result_siswa->fetch_assoc()) {
                    echo "<option value='" . $row_siswa["id"] . "'>" . $row_siswa["nama"] . "</option>";
                }
            }
            ?>
        </select><br><br>

        <label for="pelanggaran">Pelanggaran:</label>
        <select id="pelanggaran" name="pelanggaran" required>
            <!-- Isi pilihan jenis pelanggaran dari database -->
            <?php
            $sql_pelanggaran = "SELECT * FROM master_pelanggaran";
            $result_pelanggaran = $conn->query($sql_pelanggaran);

            if ($result_pelanggaran->num_rows > 0) {
                while ($row_pelanggaran = $result_pelanggaran->fetch_assoc()) {
                    echo "<option value='" . $row_pelanggaran["id"] . "'>" . $row_pelanggaran["jenis_pelanggaran"] . " - " . $row_pelanggaran["pelanggaran"] . " (" . $row_pelanggaran["poin"] . " poin)</option>";
                }
            }
            ?>
        </select><br><br>

        <label for="tanggal">Tanggal:</label>
        <input type="date" id="tanggal" name="tanggal" required><br><br>

        <label for="jam">Jam:</label>
        <input type="time" id="jam" name="jam" required><br><br>

        <input type="submit" value="Tambah">
    </form>

    <?php
    // Query untuk mengambil data dari tabel data_pelanggaran dan tabel terkait
    $sql_pelanggaran = "SELECT dp.id, ds.nama AS nama_siswa, mp.jenis_pelanggaran, mp.pelanggaran, mp.poin, dp.tanggal, dp.jam FROM data_pelanggaran dp
                       INNER JOIN data_siswa ds ON dp.siswa_id = ds.id
                       INNER JOIN master_pelanggaran mp ON dp.pelanggaran_id = mp.id";
    $result_pelanggaran = $conn->query($sql_pelanggaran);

    if ($result_pelanggaran->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Siswa</th><th>Jenis Pelanggaran</th><th>Pelanggaran</th><th>Poin</th><th>Tanggal</th><th>Jam</th><th>Aksi</th></tr>";

        while ($row_pelanggaran = $result_pelanggaran->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row_pelanggaran["id"] . "</td>";
            echo "<td>" . $row_pelanggaran["nama_siswa"] . "</td>";
            echo "<td>" . $row_pelanggaran["jenis_pelanggaran"] . "</td>";
            echo "<td>" . $row_pelanggaran["pelanggaran"] . "</td>";
            echo "<td>" . $row_pelanggaran["poin"] . "</td>";
            echo "<td>" . $row_pelanggaran["tanggal"] . "</td>";
            echo "<td>" . $row_pelanggaran["jam"] . "</td>";
            echo "<td><a href='edit_pelanggaran.php?id=" . $row_pelanggaran["id"] . "'>Edit</a> | <a href='proses_delete_pelanggaran.php?id=" . $row_pelanggaran["id"] . "'>Hapus</a></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Tidak ada data pelanggaran.";
    }

    // Menutup koneksi
    $conn->close();
    ?>

</html>