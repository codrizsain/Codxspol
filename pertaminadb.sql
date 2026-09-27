-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 27 Sep 2026 pada 13.46
-- Versi server: 10.1.38-MariaDB
-- Versi PHP: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pertaminadb`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `akun`
--

CREATE TABLE `akun` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `akun`
--

INSERT INTO `akun` (`id`, `nama`, `username`, `password`, `role`) VALUES
(1, 'Administrator', 'admin', 'admin123', 'admin'),
(2, 'Rizki Pengguna', 'rizki', '123456', 'user'),
(3, 'Amirul', 'amirul', 'amirul123', 'user'),
(4, 'rizki ardiansyah', 'sasa', 'sasa1', 'user');

-- --------------------------------------------------------

--
-- Struktur dari tabel `lokasi_tersimpan`
--

CREATE TABLE `lokasi_tersimpan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `spbu_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `lokasi_tersimpan`
--

INSERT INTO `lokasi_tersimpan` (`id`, `user_id`, `spbu_id`) VALUES
(2, 2, 4),
(3, 3, 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `spbu`
--

CREATE TABLE `spbu` (
  `id` int(11) NOT NULL,
  `id_spbu` varchar(20) DEFAULT NULL,
  `nama_spbu` varchar(100) DEFAULT NULL,
  `alamat` text,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `spbu`
--

INSERT INTO `spbu` (`id`, `id_spbu`, `nama_spbu`, `alamat`, `latitude`, `longitude`) VALUES
(1, '14212267', 'SPBU Huta Padang', 'Dusun 8, Desa Huta Padang, Kec. Bandar Pasir Mandoge', '2.7846056', '99.2480716'),
(2, '14212273', 'SPBU Air Batu', 'Jl. Ahmad Yani, Tj. Alam, Kec. Air Batu', '2.9716155', '99.6127402'),
(3, '14212222', 'SPBU Simpang Empat', 'Jl. Lintas Sumatra, Pulau Maria', '2.7583560', '99.5701655'),
(4, '14212298', 'SPBU Sei Kepayang', 'Tj Balai–Tj Ledong, Sei Kepayang Tengah', '2.9541839', '99.8079059'),
(5, '14212252', 'SPBU Sei Renggas', 'Jl Kisaran Barat, Sei Renggas', '2.9596045', '99.5888299'),
(6, '14212293', 'SPBU Teladan', 'Jl Imam Bonjol', '2.9747426', '99.6275023'),
(7, '14212279', 'SPBU Teluk Dalam', 'Air Teluk Hessa', '2.9749163', '99.5476747'),
(8, '14212297', 'SPBU Aek Songsongan', 'Bandar Pulau', '2.6735048', '99.5105762'),
(9, '14212268', 'SPBU Hessa Air Genting', 'Hessa Air Genting', '2.9252132', '99.3992203'),
(10, '14212278', 'SPBU Aek Loba', 'Aek Loba', '2.6558141', '99.3247362'),
(11, '14212227', 'SPBU Sentang', 'Jl Gatot Subroto', '2.9679337', '99.6219886'),
(12, '14213233', 'SPBU Aek Teluk Kiri', 'Aek Teluk Kiri', '3.0192030', '99.5792021'),
(13, '14212220', 'SPBU Mekar Baru', 'Jl HOS Cokroaminoto', '2.9862791', '99.6138160'),
(14, '14212290', 'SPBU Aek Ledong', 'Aek Ledong', '2.5878489', '99.6328480'),
(15, '14212291', 'SPBU Air Joman', 'Air Joman', '2.9965100', '99.6773966');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `akun`
--
ALTER TABLE `akun`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `lokasi_tersimpan`
--
ALTER TABLE `lokasi_tersimpan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `spbu_id` (`spbu_id`);

--
-- Indeks untuk tabel `spbu`
--
ALTER TABLE `spbu`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `akun`
--
ALTER TABLE `akun`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `lokasi_tersimpan`
--
ALTER TABLE `lokasi_tersimpan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `spbu`
--
ALTER TABLE `spbu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `lokasi_tersimpan`
--
ALTER TABLE `lokasi_tersimpan`
  ADD CONSTRAINT `lokasi_tersimpan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `akun` (`id`),
  ADD CONSTRAINT `lokasi_tersimpan_ibfk_2` FOREIGN KEY (`spbu_id`) REFERENCES `spbu` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
