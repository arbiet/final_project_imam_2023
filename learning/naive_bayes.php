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
    <h2>Data Siswa | Naive Bayes</h2>
    <a href="naive_bayes_preprocessing.php">Preprocessing</a>
    <br>
    <br>
    <?php
    // Lihat Perhitungan Tahun Ajaran Sekarang hingga 3 tahun ke belakang
    $tahun_ajaran = date("Y");
    for ($i = 0; $i < 3; $i++) {
        // Semester
        for ($j = 1; $j <= 2; $j++) {
            echo "<a href='naive_bayes.php?tahun_ajaran=" . $tahun_ajaran . "/" . ($tahun_ajaran + 1) . "&semester=" . $j . "'>Semester " . $j . " Tahun Ajaran " . $tahun_ajaran . "/" . ($tahun_ajaran + 1) . "</a> | ";
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
        echo "<tr><th>No</th><th>Nama</th><th>Jenis Kelamin</th><th>Jumlah pelanggaran</th><th>Poin pelanggaran</th><th>Jumlah Prestasi</th><th>Poin Prestasi</th><th>Total Poin</th><th>Penaganan</th><th>Aksi</th></tr>";

        while ($row = $result->fetch_assoc()) {
            // Retrieve and display violations for the student
            $student_id = $row["id"];
            //join table data_pelanggaran dan master_pelanggaran
            $violations_query = "SELECT master_pelanggaran.poin AS poin_pelanggaran
                FROM data_pelanggaran 
                LEFT JOIN master_pelanggaran ON data_pelanggaran.pelanggaran_id = master_pelanggaran.id
                WHERE siswa_id = $student_id AND YEAR(data_pelanggaran.tanggal) = $tahun_cari AND MONTH(data_pelanggaran.tanggal) BETWEEN $bulanStart AND $bulanEnd";
            $violations_result = $conn->query($violations_query);

            // join table data_prestasi dan master_prestasi
            $achievements_query = "SELECT master_prestasi.poin AS poin_prestasi
                FROM data_prestasi 
                LEFT JOIN master_prestasi ON data_prestasi.prestasi_id = master_prestasi.id
                WHERE siswa_id = $student_id AND YEAR(data_prestasi.tanggal) = $tahun_cari AND MONTH(data_prestasi.tanggal) BETWEEN $bulanStart AND $bulanEnd";
            $achievements_result = $conn->query($achievements_query);
            $total_poin = 0;
            $total_poin_prestasi = 0;

            // jika ada data pelanggaran maka tampilkan
            if ($violations_result->num_rows > 0) {
                $count++;
                echo "<tr>";
                echo "<td>" . $count . "</td>";
                echo "<td>" . $row["nama"] . "</td>";
                echo "<td>" . $row["jenis_kelamin"] . "</td>";
                echo "<td>" . $violations_result->num_rows . "</td>";
                echo "<td>";
                foreach ($violations_result as $violation) {
                    // convert string to int
                    $total_poin += $violation['poin_pelanggaran'];
                    $total_poin = (int) $total_poin;
                }
                echo $total_poin;
                echo "</td>";
                if ($achievements_result->num_rows > 0) {
                    echo "<td>" . $achievements_result->num_rows . "</td>";
                    echo "<td>";
                    foreach ($achievements_result as $achievement) {
                        // convert string to int
                        $total_poin_prestasi += $achievement['poin_prestasi'];
                        $total_poin_prestasi = (int) $total_poin_prestasi;
                    }
                    echo $total_poin_prestasi;
                    echo "</td>";
                } else {
                    echo "<td>0</td>";
                    echo "<td>0</td>";
                }
                echo "<td>" . ($total_poin - $total_poin_prestasi) . "</td>";

                // get master_penanganan_pelanggaran
                $penanganan_query = "SELECT kategori_pelanggaran, tindak_lanjut FROM master_penanganan_pelanggaran WHERE rentang_skor_bawah <= " . ($total_poin - $total_poin_prestasi) . " AND rentang_skor_atas >= " . ($total_poin - $total_poin_prestasi);
                $penanganan_result = $conn->query($penanganan_query);
                if ($penanganan_result->num_rows > 0) {
                    foreach ($penanganan_result as $penanganan) {
                        echo "<td>" . $penanganan['kategori_pelanggaran'] . "</td>";
                    }
                } else {
                    echo "<td>Belum ada penanganan</td>";
                }

                echo "<td>
                        <a href='lihat_pelanggaran.php?id=" . $row["id"] . "'>Lihat</a> |
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

    // Menutup koneksi
    $conn->close();
    ?>
</body>

</html>