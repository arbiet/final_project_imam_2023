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
-- Table structure for table `master_pelanggaran`
--

CREATE TABLE `master_pelanggaran` (
  `id` int(11) NOT NULL,
  `jenis_pelanggaran` varchar(100) DEFAULT NULL,
  `pelanggaran` text DEFAULT NULL,
  `poin` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_pelanggaran`
--

INSERT INTO `master_pelanggaran` (`id`, `jenis_pelanggaran`, `pelanggaran`, `poin`, `created_at`, `updated_at`) VALUES
(4, ' SIKAP PERILAKU', 'Mencoret-coret atau mengotori dinding, pintu, meja, kursi, pagar sekolah\r\n', 10, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(5, 'SIKAP PERILAKU', 'Berperilaku jorok atau asusila baik didalam maupun diluar sekolah', 10, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(6, 'SIKAP PERILAKU', 'Menggunakan fasilitas toilet sesuai gender', 30, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(7, 'SIKAP PERILAKU', 'Menggunakan fasilitas toilet sesuai gender', 40, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(8, 'SIKAP PERILAKU', 'Merusak sarana dan prasarana sekolah', 40, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(9, 'SIKAP PERILAKU', 'Mengancam / mengintimidasi teman sekelas / teman sekolah', 75, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(10, 'SIKAP PERILAKU', 'Membawa / merokok saat masih mengenakan seragam sekolah.', 100, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(11, 'SIKAP PERILAKU', 'Menyalahgunakan media sosial yang merugikan pihak lain yang berhubungan dengan \r\nsekolah', 100, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(12, 'SIKAP PERILAKU', 'Membawa senjata tajam, senjata api dsb. Di sekolah', 150, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(13, 'SIKAP PERILAKU', 'Terlibat langsung maupun tidak langsung perkelahian/tawuran di sekolah,di luar \r\nsekolah atau antar sekolah.', 150, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(14, 'SIKAP PERILAKU', ' Mengikuti aliran/perkumpulan/geng terlarang/komunitas LGBT dan radikalisme.\r\n', 150, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(15, 'SIKAP PERILAKU', '. Membawa, menggunakan atau mengedarkan miras dan narkoba', 250, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(16, 'SIKAP PERILAKU', 'Membawa dan/atau membuat VCD Porno, buku porno, majalah porno atau sesuatu \r\nyang berbau pornografi dan pornoaksi.', 200, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(17, 'SIKAP PERILAKU', 'Mencuri di sekolah dan diluar sekolah', 200, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(18, 'SIKAP PERILAKU', 'Terlibat tindakan kriminal, mencemarkan nama baik sekolah.\r\n', 250, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(19, 'SIKAP PERILAKU', 'Terbukti hamil atau menghamili.', 250, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(20, 'SIKAP PERILAKU', 'Terbukti menikah', 250, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(21, 'KERAJINAN', 'Datang terlambat', 10, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(22, 'KERAJINAN', 'Dikantin saat jam pelajaran', 10, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(23, 'KERAJINAN', 'Tidak mengikuti pelajaran/meninggalkan kelas tanpa izin', 20, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(24, 'KERAJINAN', 'Tidak mengikuti kegiatan sekolah', 20, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(25, 'KERAPIAN', 'Tidak berseragam sesuai dengan ketentuan', 10, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(26, 'KERAPIAN', 'Tidak memasukan baju seragam sekolah', 10, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(27, 'KERAPIAN', 'Melipat lengan baju', 10, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(28, 'KERAPIAN', 'Seragam yang dicoret-coret', 10, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(29, 'KERAPIAN', 'Celana atau rok bawah tidak dikelim', 10, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(30, 'KERAPIAN', 'Tidak memakai ikat pinggang', 10, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(31, 'KERAPIAN', 'Seragam atribut tidak lengkap', 10, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(32, 'KERAPIAN', 'Setiap senin dan selasa wajib bersepatu hitam kaos kaki putih', 10, '2023-08-15 15:49:53', '2023-08-15 15:49:53'),
(33, 'KERAPIAN', 'Rambut tidak boleh panjang (peserta didik putra)', 10, '2023-08-15 15:49:53', '2023-08-15 15:49:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `master_pelanggaran`
--
ALTER TABLE `master_pelanggaran`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `master_pelanggaran`
--
ALTER TABLE `master_pelanggaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
