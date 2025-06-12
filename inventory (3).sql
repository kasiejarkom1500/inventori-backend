-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 01, 2024 at 07:49 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `reset_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token_created_at` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `nama`, `email`, `username`, `password`, `reset_token`, `token_created_at`) VALUES
(3, 'admin1', 'admin@gmail.com', 'admin1', '$2y$10$welTSHzvMGl8kFkfgBbDC.nx1vqmMkfrVUTZWY0hv/GJVjEvXIRPK', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin_pengajuan`
--

CREATE TABLE `admin_pengajuan` (
  `id_pengajuan` int NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jabatan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ruangan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_pengajuan`
--

INSERT INTO `admin_pengajuan` (`id_pengajuan`, `nama`, `jabatan`, `ruangan`, `username`, `password`) VALUES
(3, 'admin2', 'kabag', 'produksi', 'admin2', '$2y$10$EKpOb/Cl1OPCZN70g8pxpOUt1uC6IfTbfuP6YXDI0r9yhrfsLNfI.');

-- --------------------------------------------------------

--
-- Table structure for table `admin_ruangan`
--

CREATE TABLE `admin_ruangan` (
  `id_ruangan` int NOT NULL,
  `ruangan` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `penanggungjawab` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jabatan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_ruangan`
--

INSERT INTO `admin_ruangan` (`id_ruangan`, `ruangan`, `penanggungjawab`, `jabatan`) VALUES
(1, 'Loby', 'Sutino, S.E', ''),
(2, 'Aula', 'Sutino, S.E', ''),
(3, 'Vicon', 'Sutino, S.E', ''),
(4, 'Laktasi', 'Sutino, S.E', ''),
(5, 'Mushola', 'Sutino, S.E', ''),
(6, 'Arsip', 'Sutino, S.E', ''),
(7, 'produksi', 'Eny Tristanti, SST, M.E', ''),
(8, 'distribusi', 'Susiawati Kristiarini, SST', ''),
(9, 'Pengolahan IPDS', 'Agus Widodo SST., M.Si', ''),
(10, 'PST', 'Nopriansyah SST, M.Si', ''),
(11, 'Madya', 'Nova Moestafa SST, M.Si', ''),
(12, 'Umum', 'Juniar S.E', ''),
(13, 'Keuangan', 'Gafur S.ST., M.Si', ''),
(14, 'Kabag Umum', 'Marini Syafitri SST', ''),
(15, 'Sekretaris', 'Marini Syafitri SST', ''),
(16, 'Kepala BPS Provinsi Jambi', 'Marini Syafitri SST', ''),
(17, 'SDM/PBJ', 'Sunandar, S.E., M.Si.', ''),
(18, 'Sosial/Neraca', 'Sisilia Nurteta, SST, M.Si', '');

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` int NOT NULL,
  `kd_barang` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_barang` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `kategori` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `satuan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `stok` int DEFAULT '0',
  `harga_satuan` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `kd_barang`, `nama_barang`, `kategori`, `satuan`, `stok`, `harga_satuan`) VALUES
(221, 'Proident sed non in', 'Similique neque quis', 'ARK', 'Buku', 46, 17),
(222, 'Elit officia illum', 'Lorem mollitia numqu', 'ATK', 'Rim', 59, 83),
(225, 'Molestiae ducimus n', 'Ea possimus nostrum', 'ATK', 'Blok', 5, 77),
(227, 'Laborum enim molesti', 'Consectetur omnis do', 'ARK', 'Buah', 79, 97);

-- --------------------------------------------------------

--
-- Table structure for table `barang_keluar`
--

CREATE TABLE `barang_keluar` (
  `id_barang_keluar` int NOT NULL,
  `tgl_keluar` date NOT NULL,
  `id_barang` int NOT NULL,
  `jumlah` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `barang_masuk`
--

CREATE TABLE `barang_masuk` (
  `id_barang_masuk` int NOT NULL,
  `tgl_masuk` date NOT NULL,
  `id_barang` int NOT NULL,
  `jumlah` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `harga_pengajuan`
--

CREATE TABLE `harga_pengajuan` (
  `id_harga_pengajuan` int NOT NULL,
  `id_barang` int DEFAULT NULL,
  `harga_pengajuan` decimal(10,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `harga_pengajuan`
--

INSERT INTO `harga_pengajuan` (`id_harga_pengajuan`, `id_barang`, `harga_pengajuan`) VALUES
(2572, 221, '26'),
(2573, 222, '125'),
(2574, 225, '116'),
(2575, 227, '146');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan`
--

CREATE TABLE `pengajuan` (
  `id_pengajuan` int NOT NULL,
  `id_user` int NOT NULL,
  `id_barang` int NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` int NOT NULL,
  `total_harga` int NOT NULL,
  `status` enum('Pending','Approved','Rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `id_ruangan` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permintaan`
--

CREATE TABLE `permintaan` (
  `id_permintaan` int NOT NULL,
  `id_user` int NOT NULL,
  `id_barang` int NOT NULL,
  `tanggal` date NOT NULL,
  `jumlah` int NOT NULL,
  `id_ruangan` int NOT NULL,
  `status` enum('Pending','Approved','Rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permintaan`
--

INSERT INTO `permintaan` (`id_permintaan`, `id_user`, `id_barang`, `tanggal`, `jumlah`, `id_ruangan`, `status`) VALUES
(132, 33, 225, '2024-08-01', 2, 18, 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int NOT NULL,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `jabatan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ruangan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama`, `jabatan`, `ruangan`, `username`, `password`) VALUES
(1, 'loby', 'loby', 'loby', 'loby1', '$2y$10$oYrup.JKOPB2TOe8PAGJzeCwzr1LZ.sgUEHyPjGTZoi24dh/dZOSO'),
(33, 'loby2', 'loby2', 'loby2', 'loby2', '$2y$10$a8KUNFP7hRtoydwHCq1OEOodXdcQJw1/FH2qUJYq/m4nY9c6KwI5G');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_pengajuan`
--
ALTER TABLE `admin_pengajuan`
  ADD PRIMARY KEY (`id_pengajuan`);

--
-- Indexes for table `admin_ruangan`
--
ALTER TABLE `admin_ruangan`
  ADD PRIMARY KEY (`id_ruangan`);

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD UNIQUE KEY `kd_barang` (`kd_barang`);

--
-- Indexes for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD PRIMARY KEY (`id_barang_keluar`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD PRIMARY KEY (`id_barang_masuk`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `harga_pengajuan`
--
ALTER TABLE `harga_pengajuan`
  ADD PRIMARY KEY (`id_harga_pengajuan`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indexes for table `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD PRIMARY KEY (`id_pengajuan`),
  ADD KEY `id_ruangan` (`id_ruangan`);

--
-- Indexes for table `permintaan`
--
ALTER TABLE `permintaan`
  ADD PRIMARY KEY (`id_permintaan`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_barang` (`id_barang`),
  ADD KEY `permintaan_ibfk_3` (`id_ruangan`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_pengajuan`
--
ALTER TABLE `admin_pengajuan`
  MODIFY `id_pengajuan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `admin_ruangan`
--
ALTER TABLE `admin_ruangan`
  MODIFY `id_ruangan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=228;

--
-- AUTO_INCREMENT for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  MODIFY `id_barang_keluar` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  MODIFY `id_barang_masuk` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `harga_pengajuan`
--
ALTER TABLE `harga_pengajuan`
  MODIFY `id_harga_pengajuan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2576;

--
-- AUTO_INCREMENT for table `pengajuan`
--
ALTER TABLE `pengajuan`
  MODIFY `id_pengajuan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `permintaan`
--
ALTER TABLE `permintaan`
  MODIFY `id_permintaan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=133;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang_keluar`
--
ALTER TABLE `barang_keluar`
  ADD CONSTRAINT `barang_keluar_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `barang_masuk`
--
ALTER TABLE `barang_masuk`
  ADD CONSTRAINT `barang_masuk_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `harga_pengajuan`
--
ALTER TABLE `harga_pengajuan`
  ADD CONSTRAINT `fk_id_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE,
  ADD CONSTRAINT `harga_pengajuan_ibfk_1` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`);

--
-- Constraints for table `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD CONSTRAINT `pengajuan_ibfk_1` FOREIGN KEY (`id_ruangan`) REFERENCES `admin_ruangan` (`id_ruangan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
