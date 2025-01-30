-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 30, 2025 at 03:02 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dpmd`
--

-- --------------------------------------------------------

--
-- Table structure for table `inventaris`
--

CREATE TABLE `inventaris` (
  `id_inventaris` int(11) NOT NULL,
  `kode_barang` varchar(225) NOT NULL,
  `waktu_pengadaan` date DEFAULT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `stok` int(11) NOT NULL,
  `lokasi_barang` enum('Bidang PPM','Bidang PEMDES','Bidang PKSB','') NOT NULL,
  `kondisi_barang` enum('Baik','Rusak','Hilang') DEFAULT NULL,
  `foto_barang` varchar(225) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventaris`
--

INSERT INTO `inventaris` (`id_inventaris`, `kode_barang`, `waktu_pengadaan`, `nama_barang`, `stok`, `lokasi_barang`, `kondisi_barang`, `foto_barang`) VALUES
(1, '', NULL, 'Komputer', 3, 'Bidang PPM', 'Baik', ''),
(2, '', NULL, 'Printer', 3, 'Bidang PPM', 'Baik', ''),
(3, '', NULL, 'Komputer', 3, 'Bidang PEMDES', 'Rusak', ''),
(10, '', NULL, 'Cermin', 1, 'Bidang PEMDES', 'Baik', ''),
(14, '', NULL, 'Cermin', 1, 'Bidang PPM', 'Baik', ''),
(16, '', NULL, 'Meja', 5, 'Bidang PKSB', 'Baik', ''),
(17, 'C03', '2025-01-03', 'saputangan', 30, 'Bidang PPM', 'Baik', '');

-- --------------------------------------------------------

--
-- Table structure for table `sifat_surat`
--

CREATE TABLE `sifat_surat` (
  `id_sifat` int(11) NOT NULL,
  `nama_sifat_surat` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sifat_surat`
--

INSERT INTO `sifat_surat` (`id_sifat`, `nama_sifat_surat`) VALUES
(1, 'biasa'),
(4, 'rahasia'),
(5, 'penting'),
(7, 'lumayan');

-- --------------------------------------------------------

--
-- Table structure for table `surat_keluar`
--

CREATE TABLE `surat_keluar` (
  `id_surat_keluar` int(11) NOT NULL,
  `no_surat` varchar(90) NOT NULL,
  `perihal_surat` varchar(90) NOT NULL,
  `tanggal_surat` date NOT NULL,
  `dokumen_surat` varchar(90) NOT NULL,
  `penerima` varchar(90) NOT NULL,
  `nama_sifat_surat` varchar(255) NOT NULL,
  `user_input_keluar` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surat_keluar`
--

INSERT INTO `surat_keluar` (`id_surat_keluar`, `no_surat`, `perihal_surat`, `tanggal_surat`, `dokumen_surat`, `penerima`, `nama_sifat_surat`, `user_input_keluar`) VALUES
(46, '400/01/432.312/2025', 'balasan', '2025-01-15', '22-028_Andre_PAW C (1) (2).pdf', 'dinas', 'biasa', NULL),
(48, '542/01/432.312/2026', 'pengajuan', '2026-02-15', 'BENDAHARA.jpg', 'bupati', 'Penting', NULL),
(49, '400/02/432.312/2026', 'pertemuan', '2026-06-15', 'Usecase.png', 'bupati', 'Penting', NULL),
(50, '400/03/432.312/2025', 'pertemuan', '2025-01-15', 'LAPORAN TUGAS PRAKTIKUM.docx', 'bupati', 'Rahasia', NULL),
(51, '501/04/432.312/2025', 'balasan', '2025-01-15', 'Screenshot (4).png', 'dinas', 'Biasa', NULL),
(52, '501/05/432.312/2025', 'pengajuan', '2025-01-15', 'Screenshot (4).png', 'dinas', 'lumayan', NULL),
(53, '501.3/06/432.312/2025', 'pertemuan', '2025-01-11', 'laporan_surat_2026.pdf', 'bupati', 'Penting', NULL),
(55, '400.23/03/432.312/2026', 'pertemuan', '2026-01-29', 'laporan_surat_2025 (4).pdf', 'bupati', 'Penting', NULL),
(56, '400/07/432.312/2025', 'pertemuan', '2025-01-23', 'Screenshot (5).png', 'bupati', 'Penting', NULL),
(57, '400/008/432.312/2025', 'pertemuan di balai desa larangan dalam', '2025-01-22', 'laporan_surat_2026.pdf', 'kepala desa larangan dalam', 'biasa', NULL),
(62, '400/012/432.312/2025', 'pertemuan', '2025-01-22', 'laporan_surat_2025 (6).pdf', 'bupati', 'penting', NULL),
(63, '456.76.2/013/432.312/2025', 'pertemuan', '2025-01-22', 'laporan_surat_2026 (1).pdf', 'dinas', 'biasa', NULL),
(64, '321/014/432.312/2025', 'pertemuan', '2025-01-22', 'laporan_surat_2025 (5).pdf', 'bupati', 'biasa', NULL),
(65, '501.0/015/432.312/2025', 'pertemuan bupati di kantor kepala desa', '2025-01-09', 'laporan_surat_2026 (1).pdf', 'bupati', 'rahasia', NULL),
(66, '400/016/432.312/2025', 'pertemuan cccccccc', '2025-01-25', 'laporan_surat_2025 (3).pdf', 'bupati', 'rahasia', NULL),
(67, '9087/017/432.312/2025', 'Pengajuan', '2025-01-24', 'laporan_surat_2026.pdf', 'camat', 'biasa', NULL),
(68, '90.088/018/432.312/2025', 'Pengajuan', '2025-01-18', '', 'camat', 'rahasia', NULL),
(69, '908.080/019/432.312/2025', 'Pengajuan', '2025-01-18', '', 'camat', 'rahasia', NULL),
(70, '987.088/020/432.312/2025', 'Pengajuan', '2025-01-17', '', 'camat', 'rahasia', 2),
(71, '9087.08/021/432.312/2025', 'Pengajuan', '2025-01-24', '', 'camat', 'rahasia', 2);

-- --------------------------------------------------------

--
-- Table structure for table `surat_keputusan`
--

CREATE TABLE `surat_keputusan` (
  `id_keputusan` int(11) NOT NULL,
  `no_keputusan` varchar(90) NOT NULL,
  `perihal_keputusan` varchar(90) NOT NULL,
  `tgl_keputusan` date NOT NULL,
  `agenda_keputusan` date NOT NULL,
  `dokumen_keputusan` varchar(255) NOT NULL,
  `keputusan_input` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surat_keputusan`
--

INSERT INTO `surat_keputusan` (`id_keputusan`, `no_keputusan`, `perihal_keputusan`, `tgl_keputusan`, `agenda_keputusan`, `dokumen_keputusan`, `keputusan_input`) VALUES
(3, '452.2/001/432.312/2025', 'Putus Cinta', '2025-01-16', '2025-01-29', 'IF2C_Tugas4_220411100028_Andre.pdf', 1),
(4, '452.2/002/432.312/2025', 'Putus Kerja', '2025-01-30', '2025-01-31', 'laporan_surat_2025 (6).pdf', 1);

-- --------------------------------------------------------

--
-- Table structure for table `surat_kontrak`
--

CREATE TABLE `surat_kontrak` (
  `id_kontrak` int(11) NOT NULL,
  `no_kontrak` varchar(50) NOT NULL,
  `perihal_kontrak` varchar(90) NOT NULL,
  `tgl_kontrak` date NOT NULL,
  `dokumen_kontrak` varchar(255) NOT NULL,
  `agenda_kontrak` date NOT NULL,
  `kontrak_input` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surat_kontrak`
--

INSERT INTO `surat_kontrak` (`id_kontrak`, `no_kontrak`, `perihal_kontrak`, `tgl_kontrak`, `dokumen_kontrak`, `agenda_kontrak`, `kontrak_input`) VALUES
(2, '099.8/hma/4.231/2025', 'kontrak kuliah biasa', '2025-01-25', 'laporan_surat_2025 (6).pdf', '2025-01-30', 1),
(3, '400.7/002/432.312/2025', 'kontrak kuliah', '2025-01-24', '22-028_Andre_PAW C (1).pdf', '2025-01-24', 1),
(5, '400.7/003/432.312/2025', 'Kontrakan', '2025-01-25', 'ANALISIS EFESIENSI USAHA TANI TEMBAKAU DI DESA KONANG KECAMATAN GALIS KABUPATEN PAMEKASAN.pdf', '2025-02-07', 1),
(6, '400.7/004/432.312/2025', 'Kontak', '2025-01-25', 'laporan_surat_2025 (5).pdf', '2025-01-30', 2),
(8, '400.7/009/432.312/2025', 'kontrak kuliah', '2025-01-29', 'laporan_surat_2025 (6) (2).pdf', '2025-01-29', 1);

-- --------------------------------------------------------

--
-- Table structure for table `surat_masuk`
--

CREATE TABLE `surat_masuk` (
  `id_surat` int(11) NOT NULL,
  `nomor_surat` varchar(90) NOT NULL,
  `perihal` varchar(90) NOT NULL,
  `tgl_surat` date NOT NULL,
  `terima_tanggal` date NOT NULL,
  `pengirim` varchar(90) NOT NULL,
  `nama_sifat_surat` varchar(90) NOT NULL,
  `dokumen` varchar(255) NOT NULL,
  `agenda` date DEFAULT NULL,
  `user_input` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surat_masuk`
--

INSERT INTO `surat_masuk` (`id_surat`, `nomor_surat`, `perihal`, `tgl_surat`, `terima_tanggal`, `pengirim`, `nama_sifat_surat`, `dokumen`, `agenda`, `user_input`) VALUES
(8, '003/hma/2025', 'pengajuan', '2025-01-10', '2025-01-10', 'camat', 'rahasia', 'data_inventaris (6).xls', NULL, NULL),
(9, '004/hma/2025', 'pemberitahuan', '2025-01-08', '2025-01-10', 'camat', 'Penting', 'data_inventaris (5).xls', NULL, NULL),
(10, '002/hma/2025', 'pengajuan', '2025-01-09', '2025-01-10', 'camat', 'Rahasia', '', NULL, NULL),
(11, '005/hma/2025', 'pengajuan', '2025-01-14', '2025-01-16', 'camat', 'Penting', '', NULL, NULL),
(12, '006/hma/2025', 'pertemuan', '2025-01-12', '2025-01-13', 'bupati', 'rahasia', 'data_inventaris (8).xls', NULL, NULL),
(13, '006/hma/2025', 'pengajuan', '2025-01-14', '2025-01-15', 'camat', 'Penting', '', NULL, NULL),
(15, '009/hma/2025', 'pertemuan', '2025-01-17', '2025-01-17', 'bupati', 'Penting', '', NULL, NULL),
(16, '0099/hma/2025', 'pertemuan', '2025-01-09', '2025-01-16', 'bupati', 'Rahasia', '', NULL, NULL),
(17, '006.92/hma/2025', 'pengajuan', '2025-02-17', '2025-02-20', 'camat', 'Penting', 'Arsip Surat Masuk (5).xls', NULL, NULL),
(18, '006.89/hma/2025', 'pertemuan', '2025-01-21', '2025-01-22', 'camat', 'Rahasia', 'Screenshot (1).png', NULL, NULL),
(19, '006/hma/2025', 'pengajuan', '2026-01-20', '2026-01-24', 'camat', 'Rahasia', 'Screenshot (5).png', NULL, NULL),
(20, '003/hma/2025', 'pertemuan', '2025-01-22', '2025-01-22', 'camat', 'biasa', 'Screenshot (4).png', NULL, NULL),
(21, '006/hma/2025', 'pengajuan', '2025-01-22', '2025-01-22', 'camat', 'rahasia', 'Screenshot (1).png', NULL, NULL),
(22, '006/hma/2025', 'pertemuan', '2025-01-23', '2025-01-23', 'camat', 'rahasia', 'laporan_surat_2025 (5).pdf', '2025-01-25', NULL),
(23, '003/hma/2025', 'pengajuan', '2025-01-01', '2025-01-22', 'bupati', 'rahasia', 'LAPORAN TUGAS PRAKTIKUM (6).docx', '0000-00-00', NULL),
(24, '009/hma/2025', 'pertemuan dengan bupati di kantor pusat', '2025-01-23', '2025-01-23', 'bupati', 'penting', 'LAPORAN TUGAS PRAKTIKUM (6).docx', '2025-01-30', NULL),
(25, '009,90/hma/2025', 'pengajuan', '2025-01-24', '2025-01-25', 'camat', 'biasa', 'laporan_surat_2026 (1).pdf', '0000-00-00', NULL),
(26, '0060/hma/2025', 'pengajuan', '2025-01-10', '2025-01-24', 'camat', 'rahasia', 'laporan_surat_2025 (1).pdf', '2025-01-25', NULL),
(29, '0879/jhs/2025', 'pengajuan', '2025-01-02', '2025-01-16', 'bupati', 'Rahasia', 'anjsbs', '2025-01-23', 2),
(36, '907/hj/hg/2025', 'bjfvujyiguhkl', '2025-01-24', '2025-01-25', 'byjbjn', 'biasa', 'laporan_surat_2025.pdf', '0000-00-00', 1),
(37, '907/hma/2025', 'pengajuan', '2025-01-24', '2025-01-25', 'sapek', 'biasa', 'laporan_surat_2026.pdf', '0000-00-00', 2),
(38, '00998/hma/2025', 'pemberitahuan', '2025-01-25', '2025-01-11', 'camat', 'rahasia', 'laporan_surat_2025 (6).pdf', '2025-01-25', 1);

-- --------------------------------------------------------

--
-- Table structure for table `surat_tugas`
--

CREATE TABLE `surat_tugas` (
  `id_tugas` int(11) NOT NULL,
  `no_tugas` varchar(90) NOT NULL,
  `perihal_tugas` varchar(90) NOT NULL,
  `tgl_tugas` date NOT NULL,
  `agenda_tugas` date NOT NULL,
  `dokumen_tugas` varchar(255) NOT NULL,
  `tugas_input` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surat_tugas`
--

INSERT INTO `surat_tugas` (`id_tugas`, `no_tugas`, `perihal_tugas`, `tgl_tugas`, `agenda_tugas`, `dokumen_tugas`, `tugas_input`) VALUES
(1, '309.2/001/432.312/2025', 'Tugas Kuliah', '2025-01-30', '2025-01-31', 'IF2C_Tugas4_220411100028_Andre.pdf', 1),
(2, '309.2/002/432.312/2025', 'Tugas Suami', '2025-01-24', '2025-01-30', '', 2),
(3, '309.2/003/432.312/2025', 'Tugas Istri', '2025-01-18', '0000-00-00', '', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','operator') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'andre', '12345', 'admin'),
(2, 'andredoank', '12345', 'operator'),
(3, 'user', '12345', 'operator'),
(4, 'andraw', '12345', 'admin'),
(5, 'andre1', '122', ''),
(6, 'admin', 'admin', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `inventaris`
--
ALTER TABLE `inventaris`
  ADD PRIMARY KEY (`id_inventaris`);

--
-- Indexes for table `sifat_surat`
--
ALTER TABLE `sifat_surat`
  ADD PRIMARY KEY (`id_sifat`);

--
-- Indexes for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  ADD PRIMARY KEY (`id_surat_keluar`),
  ADD KEY `user_input_keluar` (`user_input_keluar`);

--
-- Indexes for table `surat_keputusan`
--
ALTER TABLE `surat_keputusan`
  ADD PRIMARY KEY (`id_keputusan`),
  ADD KEY `fk_keputusan_input` (`keputusan_input`);

--
-- Indexes for table `surat_kontrak`
--
ALTER TABLE `surat_kontrak`
  ADD PRIMARY KEY (`id_kontrak`),
  ADD KEY `fk_kontrak_input` (`kontrak_input`);

--
-- Indexes for table `surat_masuk`
--
ALTER TABLE `surat_masuk`
  ADD PRIMARY KEY (`id_surat`),
  ADD KEY `fk_user_input` (`user_input`);

--
-- Indexes for table `surat_tugas`
--
ALTER TABLE `surat_tugas`
  ADD PRIMARY KEY (`id_tugas`),
  ADD KEY `fk_tugas_input` (`tugas_input`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `inventaris`
--
ALTER TABLE `inventaris`
  MODIFY `id_inventaris` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `sifat_surat`
--
ALTER TABLE `sifat_surat`
  MODIFY `id_sifat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  MODIFY `id_surat_keluar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `surat_keputusan`
--
ALTER TABLE `surat_keputusan`
  MODIFY `id_keputusan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `surat_kontrak`
--
ALTER TABLE `surat_kontrak`
  MODIFY `id_kontrak` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `surat_masuk`
--
ALTER TABLE `surat_masuk`
  MODIFY `id_surat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `surat_tugas`
--
ALTER TABLE `surat_tugas`
  MODIFY `id_tugas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  ADD CONSTRAINT `surat_keluar_ibfk_1` FOREIGN KEY (`user_input_keluar`) REFERENCES `users` (`id`);

--
-- Constraints for table `surat_keputusan`
--
ALTER TABLE `surat_keputusan`
  ADD CONSTRAINT `fk_keputusan_input` FOREIGN KEY (`keputusan_input`) REFERENCES `users` (`id`);

--
-- Constraints for table `surat_kontrak`
--
ALTER TABLE `surat_kontrak`
  ADD CONSTRAINT `fk_kontrak_input` FOREIGN KEY (`kontrak_input`) REFERENCES `users` (`id`);

--
-- Constraints for table `surat_masuk`
--
ALTER TABLE `surat_masuk`
  ADD CONSTRAINT `fk_user_input` FOREIGN KEY (`user_input`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `surat_tugas`
--
ALTER TABLE `surat_tugas`
  ADD CONSTRAINT `fk_tugas_input` FOREIGN KEY (`tugas_input`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
