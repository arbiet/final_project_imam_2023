<!DOCTYPE html>
<html>

<head>
    <title>Form Data Siswa</title>
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
    <h2>Form Data Siswa</h2>
    <p>Silahkan isi form di bawah ini untuk menambahkan data siswa.</p>
    <form action="proses_add_siswa.php" method="POST">
        <table>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIPD</th>
                <th>Jenis Kelamin</th>
                <th>NISN</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
                <th>NIK</th>
                <th>Agama</th>
            </tr>
            <tr>
                <td><input type="text" name="no"></td>
                <td><input type="text" name="nama"></td>
                <td><input type="text" name="nipd"></td>
                <td>
                    <input type="radio" name="jenis_kelamin" value="Laki-laki"> Laki-laki
                    <input type="radio" name="jenis_kelamin" value="Perempuan"> Perempuan
                </td>
                <td><input type="text" name="nisn"></td>
                <td><input type="text" name="tempat_lahir"></td>
                <td><input type="date" name="tanggal_lahir"></td>
                <td><input type="text" name="nik"></td>
                <td><input type="text" name="agama"></td>
            </tr>
        </table>
        <br>
        <input type="submit" value="Submit">
    </form>
    <h2>Data Siswa</h2>

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

    // Query untuk mengambil data dari database
    $sql = "SELECT * FROM data_siswa";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>No</th><th>Nama</th><th>NIPD</th><th>Jenis Kelamin</th><th>NISN</th><th>Tempat Lahir</th><th>Tanggal Lahir</th><th>NIK</th><th>Agama</th><th>Aksi</th></tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["no"] . "</td>";
            echo "<td>" . $row["nama"] . "</td>";
            echo "<td>" . $row["nipd"] . "</td>";
            echo "<td>" . $row["jenis_kelamin"] . "</td>";
            echo "<td>" . $row["nisn"] . "</td>";
            echo "<td>" . $row["tempat_lahir"] . "</td>";
            echo "<td>" . $row["tanggal_lahir"] . "</td>";
            echo "<td>" . $row["nik"] . "</td>";
            echo "<td>" . $row["agama"] . "</td>";
            echo "<td>
                <a href='edit_data.php?id=" . $row["id"] . "'>Edit</a>
                <a href='proses_delete_siswa.php?id=" . $row["id"] . "' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>Delete</a>
              </td>";
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