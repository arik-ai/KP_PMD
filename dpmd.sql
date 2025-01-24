-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 22 Jan 2025 pada 07.54
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

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
-- Struktur dari tabel `inventaris`
--

CREATE TABLE `inventaris` (
  `id_inventaris` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `stok` int(11) NOT NULL,
  `lokasi_barang` enum('Bidang PPM','Bidang PEMDES','Bidang PKSB','') NOT NULL,
  `kondisi_barang` enum('Baik','Rusak','Hilang') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `inventaris`
--

INSERT INTO `inventaris` (`id_inventaris`, `nama_barang`, `stok`, `lokasi_barang`, `kondisi_barang`) VALUES
(1, 'Komputer', 3, 'Bidang PPM', 'Baik'),
(2, 'Printer', 3, 'Bidang PPM', 'Baik'),
(3, 'Komputer', 3, 'Bidang PEMDES', 'Rusak'),
(10, 'Cermin', 1, 'Bidang PEMDES', 'Baik'),
(14, 'Cermin', 1, 'Bidang PPM', 'Baik'),
(16, 'Meja', 5, 'Bidang PKSB', 'Baik'),
(17, 'saputangan', 30, 'Bidang PPM', 'Baik'),
(18, 'Nasi Rames', 500, 'Bidang PPM', 'Baik'),
(19, 'Kursi', 100, 'Bidang PPM', 'Baik');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sifat_surat`
--

CREATE TABLE `sifat_surat` (
  `id_sifat` int(11) NOT NULL,
  `nama_sifat_surat` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `sifat_surat`
--

INSERT INTO `sifat_surat` (`id_sifat`, `nama_sifat_surat`) VALUES
(1, 'biasa'),
(4, 'rahasia'),
(5, 'penting'),
(7, 'lumayan'),
(8, 'a'),
(9, 'lalala');

-- --------------------------------------------------------

--
-- Struktur dari tabel `surat_keluar`
--

CREATE TABLE `surat_keluar` (
  `id_surat_keluar` int(11) NOT NULL,
  `no_surat` varchar(90) NOT NULL,
  `perihal_surat` varchar(90) NOT NULL,
  `tanggal_surat` date NOT NULL,
  `dokumen_surat` varchar(90) NOT NULL,
  `penerima` varchar(90) NOT NULL,
  `nama_sifat_surat` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `surat_keluar`
--

INSERT INTO `surat_keluar` (`id_surat_keluar`, `no_surat`, `perihal_surat`, `tanggal_surat`, `dokumen_surat`, `penerima`, `nama_sifat_surat`) VALUES
(46, '400/01/432.312/2025', 'balasan', '2025-01-15', '22-028_Andre_PAW C (1) (2).pdf', 'dinas', 'biasa'),
(48, '542/01/432.312/2026', 'pengajuan', '2026-02-15', 'BENDAHARA.jpg', 'bupati', 'Penting'),
(49, '400/02/432.312/2026', 'pertemuan', '2026-06-15', 'Usecase.png', 'bupati', 'Penting'),
(50, '400/03/432.312/2025', 'pertemuan', '2025-01-15', 'LAPORAN TUGAS PRAKTIKUM.docx', 'bupati', 'Rahasia'),
(51, '501/04/432.312/2025', 'balasan', '2025-01-15', 'Screenshot (4).png', 'dinas', 'Biasa'),
(52, '501/05/432.312/2025', 'pengajuan', '2025-01-15', 'Screenshot (4).png', 'dinas', 'lumayan'),
(53, '501.3/06/432.312/2025', 'pertemuan', '2025-01-11', 'laporan_surat_2026.pdf', 'bupati', 'Penting'),
(55, '400.23/03/432.312/2026', 'pertemuan', '2026-01-29', 'laporan_surat_2025 (4).pdf', 'bupati', 'Penting'),
(56, '400/07/432.312/2025', 'pertemuan', '2025-01-23', 'Screenshot (5).png', 'bupati', 'Penting'),
(57, '400/008/432.312/2025', 'pertemuan', '2025-01-22', 'laporan_surat_2026.pdf', 'dinas', 'Rahasia'),
(62, '400/012/432.312/2025', 'pertemuan', '2025-01-22', '', 'bupati', 'penting'),
(63, '456.76.2/013/432.312/2025', 'pertemuan', '2025-01-22', 'laporan_surat_2026 (1).pdf', 'dinas', 'biasa'),
(64, '321/014/432.312/2025', 'pertemuan', '2025-01-22', '', 'bupati', 'biasa');

-- --------------------------------------------------------

--
-- Struktur dari tabel `surat_masuk`
--

CREATE TABLE `surat_masuk` (
  `id_surat` int(11) NOT NULL,
  `nomor_surat` varchar(90) NOT NULL,
  `perihal` varchar(90) NOT NULL,
  `tgl_surat` date NOT NULL,
  `terima_tanggal` date NOT NULL,
  `pengirim` varchar(90) NOT NULL,
  `nama_sifat_surat` varchar(90) NOT NULL,
  `dokumen` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `surat_masuk`
--

INSERT INTO `surat_masuk` (`id_surat`, `nomor_surat`, `perihal`, `tgl_surat`, `terima_tanggal`, `pengirim`, `nama_sifat_surat`, `dokumen`) VALUES
(8, '003/hma/2025', 'pengajuan', '2025-01-10', '2025-01-10', 'camat', 'rahasia', 'data_inventaris (6).xls'),
(9, '004/hma/2025', 'pemberitahuan', '2025-01-08', '2025-01-10', 'camat', 'Penting', 'data_inventaris (5).xls'),
(10, '002/hma/2025', 'pengajuan', '2025-01-09', '2025-01-10', 'camat', 'Rahasia', ''),
(11, '005/hma/2025', 'pengajuan', '2025-01-14', '2025-01-16', 'camat', 'Penting', ''),
(12, '006/hma/2025', 'pertemuan', '2025-01-12', '2025-01-13', 'bupati', 'rahasia', 'data_inventaris (8).xls'),
(13, '006/hma/2025', 'pengajuan', '2025-01-14', '2025-01-15', 'camat', 'Penting', ''),
(15, '009/hma/2025', 'pertemuan', '2025-01-17', '2025-01-17', 'bupati', 'Penting', ''),
(16, '0099/hma/2025', 'pertemuan', '2025-01-09', '2025-01-16', 'bupati', 'Rahasia', ''),
(17, '006.92/hma/2025', 'pengajuan', '2025-02-17', '2025-02-20', 'camat', 'Penting', 'Arsip Surat Masuk (5).xls'),
(18, '006.89/hma/2025', 'pertemuan', '2025-01-21', '2025-01-22', 'camat', 'Rahasia', 'Screenshot (1).png'),
(19, '006/hma/2025', 'pengajuan', '2026-01-20', '2026-01-24', 'camat', 'Rahasia', 'Screenshot (5).png'),
(20, '003/hma/2025', 'pertemuan', '2025-01-22', '2025-01-22', 'camat', 'biasa', 'Screenshot (4).png'),
(21, '006/hma/2025', 'pengajuan', '2025-01-22', '2025-01-22', 'camat', 'rahasia', 'Screenshot (1).png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','operator') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
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
-- Indeks untuk tabel `inventaris`
--
ALTER TABLE `inventaris`
  ADD PRIMARY KEY (`id_inventaris`);

--
-- Indeks untuk tabel `sifat_surat`
--
ALTER TABLE `sifat_surat`
  ADD PRIMARY KEY (`id_sifat`);

--
-- Indeks untuk tabel `surat_keluar`
--
ALTER TABLE `surat_keluar`
  ADD PRIMARY KEY (`id_surat_keluar`);

--
-- Indeks untuk tabel `surat_masuk`
--
ALTER TABLE `surat_masuk`
  ADD PRIMARY KEY (`id_surat`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `inventaris`
--
ALTER TABLE `inventaris`
  MODIFY `id_inventaris` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `sifat_surat`
--
ALTER TABLE `sifat_surat`
  MODIFY `id_sifat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `surat_keluar`
--
ALTER TABLE `surat_keluar`
  MODIFY `id_surat_keluar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT untuk tabel `surat_masuk`
--
ALTER TABLE `surat_masuk`
  MODIFY `id_surat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
