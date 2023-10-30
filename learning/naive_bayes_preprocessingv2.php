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
    <h2>Data Siswa | Naive Bayes | Preprocessing</h2>
    <a href="naive_bayes_preprocessing.php">Preprocessing</a>
    <br>
    <br>
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
        echo "Total siswa: " . $result->num_rows;
        // get data penanganan pelanggaran
        $penanganan_query = "SELECT * FROM master_penanganan_pelanggaran";
        $penanganan_result = $conn->query($penanganan_query);
        $penanganans = array();
        // add column total_penanganan
        $data_pelanggaran = array();
        $master_pelanggaran = "SELECT * FROM master_pelanggaran";
        $master_pelanggaran_result = $conn->query($master_pelanggaran);
        foreach ($master_pelanggaran_result as $master_pelanggaran) {
            $master_pelanggaran['total_pelanggaran'] = 0;
            // add array master_pelanggaran to array penanganans
            $data_pelanggaran[] = $master_pelanggaran;
        }
        foreach ($penanganan_result as $penanganan_pelanggaran) {
            $penanganan_pelanggaran['total_penanganan'] = 0;
            // add array master_pelanggaran to array penanganans
            $penanganan_pelanggaran['master_pelanggaran'] = $data_pelanggaran;
            $penanganans[] = $penanganan_pelanggaran;
        }
        // echo "<pre>";
        // print_r($penanganans);
        // echo "</pre>";
        echo "<table>";
        echo "<tr><th>No</th><th>Nama</th><th>Jenis Kelamin</th><th>Jumlah pelanggaran</th><th>Poin pelanggaran</th><th>Jumlah Prestasi</th><th>Poin Prestasi</th><th>Total Poin</th><th>Penaganan</th><th>Aksi</th></tr>";

        $tahun_cari = date('Y');
        $bulan = date('m');
        if ($bulan >= 1 && $bulan <= 6) {
            $semester = 2;
        } else {
            $semester = 1;
        }
        $bulanStart = ($bulan == 1) ? 1 : 7;
        $bulanEnd = ($bulan == 1) ? 6 : 12;


        while ($row = $result->fetch_assoc()) {
            // Retrieve and display violations for the student
            $student_id = $row["id"];
            //join table data_pelanggaran dan master_pelanggaran
            $violations_query = "SELECT master_pelanggaran.poin AS poin_pelanggaran, master_pelanggaran.id AS pelanggaran_id
                FROM data_pelanggaran 
                LEFT JOIN master_pelanggaran ON data_pelanggaran.pelanggaran_id = master_pelanggaran.id
                WHERE siswa_id = $student_id AND YEAR(data_pelanggaran.tanggal) = $tahun_cari AND MONTH(data_pelanggaran.tanggal) BETWEEN $bulanStart AND $bulanEnd";
            $violations_result = $conn->query($violations_query);

            // join table data_prestasi dan master_prestasi
            $achievements_query = "SELECT master_prestasi.poin AS poin_prestasi
                FROM data_prestasi 
                LEFT JOIN master_prestasi ON data_prestasi.prestasi_id = master_prestasi.id
                WHERE siswa_id = $student_id";
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
                    foreach ($penanganans as $key => $penanganan_pelanggaran) {
                        foreach ($penanganan_pelanggaran['master_pelanggaran'] as $key2 => $master_pelanggaran) {
                            if ($master_pelanggaran['id'] == $violation['pelanggaran_id']) {
                                $penanganans[$key]['master_pelanggaran'][$key2]['total_pelanggaran'] += 1;
                            }
                        }
                    }
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
                        foreach ($penanganans as $key => $penanganan_pelanggaran) {
                            if ($penanganan_pelanggaran['kategori_pelanggaran'] == $penanganan['kategori_pelanggaran']) {
                                $penanganans[$key]['total_penanganan'] += 1;
                            }
                        }
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

        // Rumus Naive Bayes
        // Cari jumlah siswa (N)
        echo "<br>";
        echo "Jumlah siswa: " . $count;

        // Kelas Penanganan Pelanggaran (KPP)
        echo "<br>";
        echo "Kelas Penanganan Pelanggaran (KPP): ";
        echo "<br>";
        foreach ($penanganans as $key => $penanganan) {
            echo $penanganan['kategori_pelanggaran'] . " " . $penanganan['tindak_lanjut'] . " = " . $penanganan['total_penanganan'];
            echo "<br>";
        }

        // Probabilitas Kelas Penanganan Pelanggaran (P(KPP))
        echo "<br>";
        echo "Probabilitas Kelas Penanganan Pelanggaran (P(KPP)):  P(Ci)";
        echo "<br>";
        foreach ($penanganans as $key => $penanganan) {
            echo $penanganan['kategori_pelanggaran'] . " " . $penanganan['tindak_lanjut'] . " = " . $penanganan['total_penanganan'] . "/" . $count . " = " . $penanganan['total_penanganan'] / $count;
            echo "<br>";
            // add probabilitas to array penanganans
            $penanganans[$key]['probabilitas'] = $penanganan['total_penanganan'] / $count;
        }

        // Probabilitas Berdasarkan Kondisi (P(X|Ci))
        echo "<br>";
        echo "Probabilitas Berdasarkan Kondisi (P(X|Ci)):  P(X|Ci)";
        echo "<br>";
        foreach ($penanganans as $key => $penanganan) {
            echo $penanganan['kategori_pelanggaran'] . " " . $penanganan['tindak_lanjut'] . " =  P(" . $penanganan['kategori_pelanggaran'] . "|" . $penanganan['tindak_lanjut'] . ")";
            foreach ($penanganan['master_pelanggaran'] as $key2 => $master_pelanggaran) {
                echo "<br>";
                echo $master_pelanggaran['pelanggaran'] . " = " . $master_pelanggaran['total_pelanggaran'] . "/" . $penanganan['total_penanganan'] . " = " . $master_pelanggaran['total_pelanggaran'] / $penanganan['total_penanganan'];
                // add probabilitas to array master_pelanggaran
                $penanganans[$key]['master_pelanggaran'][$key2]['probabilitas'] = $master_pelanggaran['total_pelanggaran'] / $penanganan['total_penanganan'];
            }
            echo "<br>";
            echo "<br>";
        }

        // Get 1 random student for testing from data_pelanggaran
        $sql_random = "SELECT * FROM data_pelanggaran  WHERE YEAR(data_pelanggaran.tanggal) = $tahun_cari AND MONTH(data_pelanggaran.tanggal) BETWEEN $bulanStart AND $bulanEnd ORDER BY RAND() LIMIT 1";
        $result_random = $conn->query($sql_random);
        $random_id = $result_random->fetch_assoc()['siswa_id'];
        echo "Data Siswa For Testing: " . $random_id;
        echo "<br>";
        $sql = "SELECT * FROM data_siswa WHERE data_siswa.id = $random_id";
        $result = $conn->query($sql);
        echo "Total siswa: " . $result->num_rows;
        $count = 0;
        // hitung jumlah siswa
        if ($result->num_rows > 0) {
            echo "<br>";
            echo "Siswa: " . $result->fetch_assoc()['nama'];
            echo "<br>";
            // Retrieve and display violations for the student
            $student_id = $random_id;
            //join table data_pelanggaran dan master_pelanggaran
            $violations_query = "SELECT master_pelanggaran.poin AS poin_pelanggaran, master_pelanggaran.id AS pelanggaran_id, master_pelanggaran.pelanggaran AS pelanggaran
                FROM data_pelanggaran 
                LEFT JOIN master_pelanggaran ON data_pelanggaran.pelanggaran_id = master_pelanggaran.id
                WHERE siswa_id = $student_id AND YEAR(data_pelanggaran.tanggal) = $tahun_cari AND MONTH(data_pelanggaran.tanggal) BETWEEN $bulanStart AND $bulanEnd";
            $violations_result = $conn->query($violations_query);
            echo "<table>";
            echo "<tr><th>No</th><th>Pelanggaran</th><th>Poin</th></tr>";
            $count = 1;
            $total_poin_pelanggaran = 0;
            foreach ($violations_result as $violation) {
                echo "<tr>";
                echo "<td>" . $count++ . "</td>";
                echo "<td>" . $violation['pelanggaran'] . "</td>";
                echo "<td>" . $violation['poin_pelanggaran'] . "</td>";
                echo "</tr>";
                $total_poin_pelanggaran += $violation['poin_pelanggaran'];
            }
            echo "</table>";

            // join table data_prestasi dan master_prestasi
            $achievements_query = "SELECT master_prestasi.poin AS poin_prestasi, master_prestasi.nama_prestasi AS nama_prestasi, master_prestasi.id AS prestasi_id
                FROM data_prestasi 
                LEFT JOIN master_prestasi ON data_prestasi.prestasi_id = master_prestasi.id
                WHERE siswa_id = $student_id";
            $achievements_result = $conn->query($achievements_query);
            echo "<table>";
            echo "<tr><th>No</th><th>Prestasi</th><th>Poin</th></tr>";
            $count = 1;
            $total_poin_prestasi = 0;
            foreach ($achievements_result as $achievement) {
                echo "<tr>";
                echo "<td>" . $count++ . "</td>";
                echo "<td>" . $achievement['nama_prestasi'] . "</td>";
                echo "<td>" . $achievement['poin_prestasi'] . "</td>";
                echo "</tr>";
                $total_poin_prestasi += $achievement['poin_prestasi'];
            }
            echo "</table>";
            echo "<br>";
            echo "Total Poin Pelanggaran: " . $total_poin_pelanggaran;
            echo "<br>";
            echo "Total Poin Prestasi: " . $total_poin_prestasi;
            echo "<br>";
            echo "Total Poin: " . ($total_poin_pelanggaran - $total_poin_prestasi);
            // Probabilitas Berdasarkan Kondisi (P(X|Ci))
            echo "<br>";
            echo "Probabilitas Berdasarkan Kondisi (P(X|Ci)):  P(X|Ci)";
            echo "<br>";
            // save nilai probabilitas
            $nilai_probabilitas = array();
            if ($violations_result->num_rows > 0) {
                foreach ($penanganans as $key => $penanganan) {
                    echo $penanganan['kategori_pelanggaran'] . " " . $penanganan['tindak_lanjut'] . " =  P(" . $penanganan['kategori_pelanggaran'] . "|" . $penanganan['tindak_lanjut'] . ")";
                    foreach ($violations_result as $violation) {
                        foreach ($penanganan['master_pelanggaran'] as $key2 => $master_pelanggaran) {
                            if ($master_pelanggaran['id'] == $violation['pelanggaran_id']) {
                                echo "<br>";
                                echo $master_pelanggaran['pelanggaran'] . " = " . $master_pelanggaran['probabilitas'];
                                $penanganans[$key]['master_pelanggaran'][$key2]['probabilitas'] = $master_pelanggaran['probabilitas'];
                                $nilai_probabilitas[] = $master_pelanggaran['probabilitas'] / $penanganan['probabilitas'];
                            }
                        }
                    }
                    echo "<br>";
                    echo "<br>";
                }
            } else {
                echo "Tidak ada pelanggaran.";
            }

            // Probabilitas P(X|Ci) * P(Ci)
            echo "<br>";
            echo "Probabilitas P(X|Ci) * P(Ci):  P(X|Ci) * P(Ci)";
            echo "<br>";
            $probabilitas = array();
            foreach ($penanganans as $key => $penanganan) {
                echo $penanganan['kategori_pelanggaran'] . " " . $penanganan['tindak_lanjut'] . " =  P(" . $penanganan['kategori_pelanggaran'] . "|" . $penanganan['tindak_lanjut'] . ") * P(" . $penanganan['kategori_pelanggaran'] . ")";
                echo "<br>";
                echo $penanganan['probabilitas'] . " * " . $nilai_probabilitas[$key] . " = " . $penanganan['probabilitas'] * $nilai_probabilitas[$key];
                echo "<br>";
                $probabilitas[] = $penanganan['probabilitas'] * $nilai_probabilitas[$key];
            }

            // Tindak Lanjut
            echo "<br>";
            echo "Tindak Lanjut: ";
            echo "<br>";
            $max = max($probabilitas);
            $key = array_search($max, $probabilitas);
            echo $penanganans[$key]['kategori_pelanggaran'] . " " . $penanganans[$key]['tindak_lanjut'];
        }
    } else {
        echo "Tidak ada data.";
    }

    // Menutup koneksi
    $conn->close();
    ?>
</body>

</html>