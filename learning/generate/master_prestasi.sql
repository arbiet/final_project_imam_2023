-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 20, 2023 at 07:50 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skripsi-imam`
--

-- --------------------------------------------------------

--
-- Table structure for table `master_prestasi`
--

CREATE TABLE `master_prestasi` (
  `id` int(11) NOT NULL,
  `jenis_prestasi` varchar(100) DEFAULT NULL,
  `nama_prestasi` text DEFAULT NULL,
  `poin` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_prestasi`
--

INSERT INTO `master_prestasi` (`id`, `jenis_prestasi`, `nama_prestasi`, `poin`, `created_at`, `updated_at`) VALUES
(2, 'BERPRESTASI AKADEMIK DAN NON AKADEMIK', 'tingkat nasional ', 100, '2023-08-15 15:50:00', '2023-08-15 15:50:00'),
(3, 'BERPRESTASI AKADEMIK DAN NON AKADEMIK', 'tingkat provinsi', 75, '2023-08-15 15:50:00', '2023-08-15 15:50:00'),
(4, 'BERPRESTASI AKADEMIK DAN NON AKADEMIK', 'tingkat kota/kabupaten', 50, '2023-08-15 15:50:00', '2023-08-15 15:50:00'),
(5, 'BERPRESTASI AKADEMIK DAN NON AKADEMIK', 'tingkat kecamatan', 25, '2023-08-15 15:50:00', '2023-08-15 15:50:00'),
(6, 'BERPRESTASI AKADEMIK DAN NON AKADEMIK', 'mengikuti lomba sebagai peserta didik(tidak juara)', 10, '2023-08-15 15:50:00', '2023-08-15 15:50:00'),
(7, 'BERPRESTASI AKADEMIK DAN NON AKADEMIK', 'mengikuti pelatihan LDKMS', 15, '2023-08-15 15:50:00', '2023-08-15 15:50:00'),
(8, 'BERPRESTASI AKADEMIK DAN NON AKADEMIK', 'Diangkat menjadi ketua OSIS', 25, '2023-08-15 15:50:00', '2023-08-15 15:50:00'),
(9, 'BERPRESTASI AKADEMIK DAN NON AKADEMIK', 'Diangkat Menjadi pengurus osis', 20, '2023-08-15 15:50:00', '2023-08-15 15:50:00'),
(10, 'TIDAK BERPRESTASI AKADEMIK DAN NON AKADEMIK', 'tidak pernah alpa(bagi peserta didik yang mempunyai catatan pelanggaran)', 25, '2023-08-15 15:50:00', '2023-08-15 15:50:00'),
(11, 'TIDAK BERPRESTASI AKADEMIK DAN NON AKADEMIK', 'tidak pernah terlambat selama 1 bulan berturut turut(bagi peserta didik yang mempunyai catatan pelanggaran)', 15, '2023-08-15 15:50:00', '2023-08-15 15:50:00'),
(12, 'TIDAK BERPRESTASI AKADEMIK DAN NON AKADEMIK', 'mampu menujukkan catatan pelajaran lengkap dalam waktu yang telah ditentukan', 30, '2023-08-15 15:50:00', '2023-08-15 15:50:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `master_prestasi`
--
ALTER TABLE `master_prestasi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `master_prestasi`
--
ALTER TABLE `master_prestasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
