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

// Query to fetch data from the database
$sql_export = "SELECT dp.id, ds.nama AS nama_siswa, mp.jenis_pelanggaran, mp.pelanggaran, mp.poin, dp.tanggal, dp.jam FROM data_pelanggaran dp
               INNER JOIN data_siswa ds ON dp.siswa_id = ds.id
               INNER JOIN master_pelanggaran mp ON dp.pelanggaran_id = mp.id";
$result_export = $conn->query($sql_export);

// Check if there is data to export
if ($result_export->num_rows > 0) {
    // Set the headers for CSV file download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="pelanggaran_data.csv"');

    // Create a file pointer connected to the output stream
    $output = fopen('php://output', 'w');

    // Output the CSV column headers
    fputcsv($output, array('ID', 'Siswa', 'Jenis Pelanggaran', 'Pelanggaran', 'Poin', 'Tanggal', 'Jam'));

    // Output each row of the data
    while ($row_export = $result_export->fetch_assoc()) {
        fputcsv($output, $row_export);
    }

    // Close the file pointer
    fclose($output);
} else {
    echo "Tidak ada data pelanggaran.";
}

// Close the database connection
$conn->close();
