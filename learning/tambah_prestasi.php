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
    <h2>Form Tambah Prestasi</h2>

    <form action="proses_add_prestasi.php" method="POST">
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

        <label for="prestasi">Prestasi:</label>
        <select id="prestasi" name="prestasi" required>
            <!-- Isi pilihan jenis prestasi dari database -->
            <?php
            $sql_prestasi = "SELECT * FROM master_prestasi";
            $result_prestasi = $conn->query($sql_prestasi);

            if ($result_prestasi->num_rows > 0) {
                while ($row_prestasi = $result_prestasi->fetch_assoc()) {
                    echo "<option value='" . $row_prestasi["id"] . "'>" . $row_prestasi["jenis_prestasi"] . " - " . $row_prestasi["prestasi"] . " (" . $row_prestasi["poin"] . " poin)</option>";
                }
            }
            ?>
        </select><br><br>

        <label for="tanggal">Tanggal:</label>
        <input type="date" id="tanggal" name="tanggal" required><br><br>

        <label for="jam">Jam:</label>
        <input type="time" id="jam" name="jam" required><br><br>

        <label for="nama_prestasi">Nama Prestasi:</label>
        <input type="text" id="nama_prestasi" name="nama_prestasi" required><br><br>

        <label for="penyelengara">Penyelengara:</label>
        <input type="text" id="penyelengara" name="penyelengara" required><br><br>

        <label for="juara">Juara:</label>
        <input type="number" id="juara" name="juara" required><br><br>

        <label for="detail">Detail Prestasi:</label>
        <textarea id="detail" name="detail" required></textarea><br><br>

        <input type="submit" value="Tambah">
    </form>

    <?php
    // Query untuk mengambil data dari tabel data_prestasi dan tabel terkait
    $sql_prestasi = "SELECT dp.id, ds.nama AS nama_siswa, mp.jenis_prestasi, mp.nama_prestasi, mp.poin, dp.tanggal, dp.jam FROM data_prestasi dp
                       INNER JOIN data_siswa ds ON dp.siswa_id = ds.id
                       INNER JOIN master_prestasi mp ON dp.prestasi_id = mp.id";
    $result_prestasi = $conn->query($sql_prestasi);

    if ($result_prestasi->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>ID</th><th>Siswa</th><th>Jenis prestasi</th><th>prestasi</th><th>Poin</th><th>Tanggal</th><th>Jam</th><th>Aksi</th></tr>";

        while ($row_prestasi = $result_prestasi->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row_prestasi["id"] . "</td>";
            echo "<td>" . $row_prestasi["nama_siswa"] . "</td>";
            echo "<td>" . $row_prestasi["jenis_prestasi"] . "</td>";
            echo "<td>" . $row_prestasi["nama_prestasi"] . "</td>";
            echo "<td>" . $row_prestasi["poin"] . "</td>";
            echo "<td>" . $row_prestasi["tanggal"] . "</td>";
            echo "<td>" . $row_prestasi["jam"] . "</td>";
            echo "<td><a href='edit_prestasi.php?id=" . $row_prestasi["id"] . "'>Edit</a> | <a href='proses_delete_prestasi.php?id=" . $row_prestasi["id"] . "'>Hapus</a></td>";
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