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
    <h2>Data Siswa | <?php echo (isset($_GET['prestasi']) && $_GET['prestasi'] == 'true') ? 'Prestasi' : 'Pelanggaran'; ?></h2>
    <a href="perhitungan.php?pelanggaran=true">Lihat Perhitungan Pelanggaran</a> |
    <a href="perhitungan.php?prestasi=true">Lihat Perhitungan Prestasi</a>
    <br>
    <?php
    // Lihat Perhitungan Tahun Ajaran Sekarang hingga 3 tahun ke belakang
    $tahun_ajaran = date("Y");
    $cari = 'pelanggaran';
    if (isset($_GET['pelanggaran']) && $_GET['pelanggaran'] == 'true') {
        $cari = 'pelanggaran';
    } else if (isset($_GET['prestasi']) && $_GET['prestasi'] == 'true') {
        $cari = 'prestasi';
    }

    for ($i = 0; $i < 3; $i++) {
        // Semester
        for ($j = 1; $j <= 2; $j++) {
            echo "<a href='perhitungan.php?tahun_ajaran=" . $tahun_ajaran . "/" . ($tahun_ajaran + 1) . "&semester=" . $j . "&" . $cari . "=true'>Semester " . $j . " Tahun Ajaran " . $tahun_ajaran . "/" . ($tahun_ajaran + 1) . "</a> | ";
        }
        $tahun_ajaran--;
    }


    ?>
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

    // Check if the 'prestasi' query parameter is set to true
    if (isset($_GET['prestasi']) && $_GET['prestasi'] == 'true') {
        $sql = "SELECT * FROM data_siswa";
        $result = $conn->query($sql);
        $count = 0;
        if ($result->num_rows > 0) {
            $semester = (isset($_GET['semester'])) ? intval($_GET['semester']) : null;
            $tahun_ajaran = (isset($_GET['tahun_ajaran'])) ? $_GET['tahun_ajaran'] : null;

            $semester_condition = $semester;

            $bulanStart = ($semester_condition == 1) ? 1 : 7;
            $bulanEnd = ($semester_condition == 1) ? 6 : 12;

            // Use provided semester and tahun_ajaran parameters if available, otherwise use current semester and year
            $selected_semester = ($semester !== null) ? $semester : $semester_condition;
            $selected_tahun_ajaran = ($tahun_ajaran !== null) ? $tahun_ajaran : date('Y') . '/' . (date('Y') + 1);

            $tahun_ajaran = explode('/', $selected_tahun_ajaran);
            $tahun_cari = $tahun_ajaran[0];

            echo "<h3>Semester " . $selected_semester . " Tahun Ajaran " . $selected_tahun_ajaran . "</h3>";
            echo "<h4>Periode: " . $bulanStart . " - " . $bulanEnd . "</h4>";
            echo "<h4>Tahun Cari: " . $tahun_cari . "</h4>";

            echo "<table>";
            echo "<tr><th>No</th><th>Nama</th><th>NIPD</th><th>Jenis Kelamin</th><th>NISN</th><th>Tempat Lahir</th><th>Tanggal Lahir</th><th>NIK</th><th>Agama</th><th>Jumlah prestasi</th><th>Total poin</th><th>Aksi</th></tr>";

            while ($row = $result->fetch_assoc()) {
                // Retrieve and display violations for the student
                $student_id = $row["id"];
                $violations_query = "SELECT poin
                FROM data_prestasi LEFT JOIN master_prestasi ON data_prestasi.prestasi_id = master_prestasi.id WHERE siswa_id = $student_id AND YEAR(data_prestasi.tanggal) = $tahun_cari AND MONTH(data_prestasi.tanggal) BETWEEN $bulanStart AND $bulanEnd";
                $violations_result = $conn->query($violations_query);

                // jika ada data prestasi maka tampilkan
                if ($violations_result->num_rows > 0) {
                    $count++;
                    echo "<tr>";
                    echo "<td>" . $count . "</td>";
                    echo "<td>" . $row["nama"] . "</td>";
                    echo "<td>" . $row["nipd"] . "</td>";
                    echo "<td>" . $row["jenis_kelamin"] . "</td>";
                    echo "<td>" . $row["nisn"] . "</td>";
                    echo "<td>" . $row["tempat_lahir"] . "</td>";
                    echo "<td>" . $row["tanggal_lahir"] . "</td>";
                    echo "<td>" . $row["nik"] . "</td>";
                    echo "<td>" . $row["agama"] . "</td>";
                    echo "<td>" . $violations_result->num_rows . "</td>";
                    $total_poin = 0;
                    foreach ($violations_result as $violation) {
                        $total_poin += $violation['poin'];
                    }
                    echo "<td>" . $total_poin . "</td>";
                    echo "<td>
                        <a href='lihat_prestasi.php?id=" . $row["id"] . "'>Lihat prestasi</a> |
                        <a href='detail_siswa.php?id=" . $row["id"] . "'>Detail</a> |
                        <a href='edit_data.php?id=" . $row["id"] . "'>Edit</a> |
                        <a href='proses_delete_siswa.php?id=" . $row["id"] . "' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>Delete</a>
                    </td>";
                    echo "</tr>";
                }
            }
            if ($count == 0) {
                echo "<tr><td colspan='11'>Tidak ada siswa yang memiliki prestasi.</td></tr>";
            }
            echo "</table>";
        } else {
            echo "Tidak ada data.";
        }
    } else {
        $sql = "SELECT * FROM data_siswa";
        $result = $conn->query($sql);
        $count = 0;
        if ($result->num_rows > 0) {
            $semester = (isset($_GET['semester'])) ? intval($_GET['semester']) : null;
            $tahun_ajaran = (isset($_GET['tahun_ajaran'])) ? $_GET['tahun_ajaran'] : null;

            $semester_condition = $semester;

            $bulanStart = ($semester_condition == 1) ? 1 : 7;
            $bulanEnd = ($semester_condition == 1) ? 6 : 12;

            // Use provided semester and tahun_ajaran parameters if available, otherwise use current semester and year
            $selected_semester = ($semester !== null) ? $semester : $semester_condition;
            $selected_tahun_ajaran = ($tahun_ajaran !== null) ? $tahun_ajaran : date('Y') . '/' . (date('Y') + 1);

            $tahun_ajaran = explode('/', $selected_tahun_ajaran);
            $tahun_cari = $tahun_ajaran[0];

            echo "<h3>Semester " . $selected_semester . " Tahun Ajaran " . $selected_tahun_ajaran . "</h3>";
            echo "<h4>Periode: " . $bulanStart . " - " . $bulanEnd . "</h4>";
            echo "<h4>Tahun Cari: " . $tahun_cari . "</h4>";

            echo "<table>";
            echo "<tr><th>No</th><th>Nama</th><th>NIPD</th><th>Jenis Kelamin</th><th>NISN</th><th>Tempat Lahir</th><th>Tanggal Lahir</th><th>NIK</th><th>Agama</th><th>Jumlah pelanggaran</th><th>Total poin</th><th>Aksi</th></tr>";

            while ($row = $result->fetch_assoc()) {
                // Retrieve and display violations for the student
                $student_id = $row["id"];
                //join table data_pelanggaran dan master_pelanggaran
                $violations_query = "SELECT poin
                FROM data_pelanggaran LEFT JOIN master_pelanggaran ON data_pelanggaran.pelanggaran_id = master_pelanggaran.id
                WHERE siswa_id = $student_id AND YEAR(data_pelanggaran.tanggal) = $tahun_cari AND MONTH(data_pelanggaran.tanggal) BETWEEN $bulanStart AND $bulanEnd";
                $violations_result = $conn->query($violations_query);

                // jika ada data pelanggaran maka tampilkan
                if ($violations_result->num_rows > 0) {
                    $count++;
                    echo "<tr>";
                    echo "<td>" . $count . "</td>";
                    echo "<td>" . $row["nama"] . "</td>";
                    echo "<td>" . $row["nipd"] . "</td>";
                    echo "<td>" . $row["jenis_kelamin"] . "</td>";
                    echo "<td>" . $row["nisn"] . "</td>";
                    echo "<td>" . $row["tempat_lahir"] . "</td>";
                    echo "<td>" . $row["tanggal_lahir"] . "</td>";
                    echo "<td>" . $row["nik"] . "</td>";
                    echo "<td>" . $row["agama"] . "</td>";
                    echo "<td>" . $violations_result->num_rows . "</td>";
                    echo "<td>";
                    $total_poin = 0;
                    foreach ($violations_result as $violation) {
                        // convert string to int
                        $total_poin += $violation['poin'];
                        $total_poin = (int) $total_poin;
                    }
                    echo $total_poin;
                    echo "<td>
                        <a href='lihat_pelanggaran.php?id=" . $row["id"] . "'>Lihat pelanggaran</a> |
                        <a href='detail_siswa.php?id=" . $row["id"] . "'>Detail</a> |
                        <a href='edit_data.php?id=" . $row["id"] . "'>Edit</a> |
                        <a href='proses_delete_siswa.php?id=" . $row["id"] . "' onclick='return confirm(\"Apakah Anda yakin ingin menghapus data ini?\")'>Delete</a>
                    </td>";
                    echo "</tr>";
                }
            }
            if ($count == 0) {
                echo "<tr><td colspan='11'>Tidak ada siswa yang memiliki pelanggaran.</td></tr>";
            }
            echo "</table>";
        } else {
            echo "Tidak ada data.";
        }
    }
    // Menutup koneksi
    $conn->close();
    ?>
</body>

</html>