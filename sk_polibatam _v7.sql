-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 01, 2026 at 07:40 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sk_polibatam`
--

-- --------------------------------------------------------

--
-- Table structure for table `anggota_dokumen`
--

CREATE TABLE `anggota_dokumen` (
  `id` int UNSIGNED NOT NULL,
  `id_dokumen` int UNSIGNED NOT NULL,
  `id_pengguna` int UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `anggota_dokumen`
--

INSERT INTO `anggota_dokumen` (`id`, `id_dokumen`, `id_pengguna`, `created_at`) VALUES
(1, 1, 8, '2026-06-30 01:55:38'),
(2, 1, 9, '2026-06-30 01:55:38'),
(3, 2, 4, '2026-06-30 02:36:45');

-- --------------------------------------------------------

--
-- Table structure for table `anggota_grup_verifikasi`
--

CREATE TABLE `anggota_grup_verifikasi` (
  `id` int UNSIGNED NOT NULL,
  `id_grup_verifikasi` int UNSIGNED NOT NULL,
  `id_pengguna` int UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `anggota_grup_verifikasi`
--

INSERT INTO `anggota_grup_verifikasi` (`id`, `id_grup_verifikasi`, `id_pengguna`, `created_at`) VALUES
(3, 5, 2, '2026-06-21 16:30:02'),
(4, 5, 4, '2026-06-21 16:30:02'),
(5, 6, 4, '2026-06-21 16:43:22'),
(6, 7, 4, '2026-06-22 08:13:56'),
(7, 7, 2, '2026-06-22 08:13:56'),
(8, 8, 2, '2026-06-22 08:14:14'),
(9, 8, 4, '2026-06-22 08:14:14'),
(10, 9, 2, '2026-06-22 17:13:23'),
(11, 9, 4, '2026-06-22 17:13:23'),
(12, 10, 4, '2026-06-23 04:58:17'),
(13, 11, 4, '2026-06-23 06:27:39'),
(14, 12, 4, '2026-06-23 06:43:08'),
(15, 13, 4, '2026-06-23 08:42:33'),
(16, 14, 4, '2026-06-23 08:42:43'),
(17, 15, 4, '2026-06-23 08:43:46'),
(18, 16, 4, '2026-06-23 08:44:04'),
(19, 17, 4, '2026-06-24 04:26:12'),
(20, 4, 6, NULL),
(21, 18, 7, NULL),
(22, 19, 8, NULL),
(23, 19, 9, NULL),
(24, 20, 6, '2026-06-24 19:38:04'),
(25, 21, 6, '2026-06-24 20:04:29'),
(26, 21, 7, '2026-06-24 20:04:29'),
(27, 22, 6, '2026-06-24 20:17:45'),
(28, 19, 10, NULL),
(29, 23, 6, '2026-06-26 07:47:08'),
(30, 24, 6, '2026-06-26 08:14:30'),
(31, 25, 6, '2026-06-26 09:57:54'),
(32, 26, 6, '2026-06-26 10:12:01'),
(33, 27, 6, '2026-06-28 11:36:02'),
(34, 28, 6, '2026-06-28 11:37:05'),
(35, 29, 6, '2026-06-29 02:51:27'),
(36, 30, 6, '2026-06-30 01:49:51'),
(37, 31, 6, '2026-06-30 02:34:30'),
(38, 31, 7, '2026-06-30 02:34:30'),
(39, 31, 8, '2026-06-30 02:34:30');

-- --------------------------------------------------------

--
-- Table structure for table `anggota_pengajuan`
--

CREATE TABLE `anggota_pengajuan` (
  `id` int UNSIGNED NOT NULL,
  `id_pengajuan` int UNSIGNED NOT NULL,
  `id_pengguna` int UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `anggota_pengajuan`
--

INSERT INTO `anggota_pengajuan` (`id`, `id_pengajuan`, `id_pengguna`, `created_at`) VALUES
(1, 1, 9, '2026-06-30 01:44:01'),
(2, 1, 8, '2026-06-30 01:44:01'),
(3, 2, 8, '2026-06-30 02:07:21'),
(4, 3, 4, '2026-06-30 02:33:27');

-- --------------------------------------------------------

--
-- Table structure for table `dokumen`
--

CREATE TABLE `dokumen` (
  `id` int UNSIGNED NOT NULL,
  `id_nomor_dokumen` int UNSIGNED NOT NULL,
  `id_pengguna` int UNSIGNED NOT NULL,
  `tipe` enum('SK','ST') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_dokumen` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tgl_dokumen` date NOT NULL,
  `filepath` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `dari_pengajuan` tinyint(1) NOT NULL DEFAULT '0',
  `kode_unik` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rendered_body` longtext COLLATE utf8mb4_unicode_ci,
  `verified_at` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dokumen`
--

INSERT INTO `dokumen` (`id`, `id_nomor_dokumen`, `id_pengguna`, `tipe`, `nama_dokumen`, `tgl_dokumen`, `filepath`, `catatan`, `dari_pengajuan`, `kode_unik`, `rendered_body`, `verified_at`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 'ST', 'laporan test 1', '2026-06-30', 'pengajuan/pengajuan_1_1782783841.pdf', 'nomor tidak ada', 1, '6a43221ac4fd2', NULL, NULL, 0, '2026-06-30 01:55:38', '2026-06-30 01:55:38'),
(2, 2, 5, 'ST', 'laporan test 12', '2026-06-30', 'pengajuan/pengajuan_3_1782786806.pdf', NULL, 3, '6a432bbd106f2', NULL, NULL, 0, '2026-06-30 02:36:45', '2026-06-30 02:36:45');

-- --------------------------------------------------------

--
-- Table structure for table `grup_verifikasi`
--

CREATE TABLE `grup_verifikasi` (
  `id` int UNSIGNED NOT NULL,
  `nama_grup` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_pengguna` int UNSIGNED NOT NULL,
  `tingkat` enum('1','2','3') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grup_verifikasi`
--

INSERT INTO `grup_verifikasi` (`id`, `nama_grup`, `id_pengguna`, `tingkat`, `is_deleted`, `created_at`, `updated_at`) VALUES
(4, 'Verifikator 1', 1, '1', 0, '2026-06-08 19:45:28', '2026-06-24 19:36:50'),
(5, 'Ad-Hoc: laporan (1782059402)', 5, NULL, 0, '2026-06-21 16:30:02', '2026-06-21 16:30:02'),
(6, 'Ad-Hoc: laporan (1782060202)', 5, NULL, 0, '2026-06-21 16:43:22', '2026-06-21 16:43:22'),
(7, 'Ad-Hoc: laporan (1782116036)', 5, NULL, 0, '2026-06-22 08:13:56', '2026-06-22 08:13:56'),
(8, 'Ad-Hoc: laporan (1782116054)', 5, NULL, 0, '2026-06-22 08:14:14', '2026-06-22 08:14:14'),
(9, 'Ad-Hoc: laporan 2 (1782148403)', 5, NULL, 0, '2026-06-22 17:13:23', '2026-06-22 17:13:23'),
(10, 'Ad-Hoc: laporan 3 (1782190697)', 5, NULL, 0, '2026-06-23 04:58:17', '2026-06-23 04:58:17'),
(11, 'Ad-Hoc: laporan 4 (1782196059)', 5, NULL, 0, '2026-06-23 06:27:39', '2026-06-23 06:27:39'),
(12, 'Ad-Hoc: laporan 4 (1782196988)', 5, NULL, 0, '2026-06-23 06:43:08', '2026-06-23 06:43:08'),
(13, 'Ad-Hoc: laporan 4 (1782204153)', 5, NULL, 0, '2026-06-23 08:42:33', '2026-06-23 08:42:33'),
(14, 'Ad-Hoc: laporan 4 (1782204163)', 5, NULL, 0, '2026-06-23 08:42:43', '2026-06-23 08:42:43'),
(15, 'Ad-Hoc: laporan 4 (1782204226)', 5, NULL, 0, '2026-06-23 08:43:46', '2026-06-23 08:43:46'),
(16, 'Ad-Hoc: laporan 4 (1782204244)', 5, NULL, 0, '2026-06-23 08:44:04', '2026-06-23 08:44:04'),
(17, 'Ad-Hoc: laporan 5 (1782275172)', 5, NULL, 0, '2026-06-24 04:26:12', '2026-06-24 04:26:12'),
(18, 'Verifikator 2', 1, '2', 0, '2026-06-24 04:35:14', '2026-06-24 19:36:57'),
(19, 'Verifikator 3', 1, '3', 0, '2026-06-24 04:35:34', '2026-06-24 19:37:03'),
(20, 'Ad-Hoc: laporan (1782329884)', 5, NULL, 0, '2026-06-24 19:38:04', '2026-06-24 19:38:04'),
(21, 'Ad-Hoc: laporan (1782331469)', 5, NULL, 0, '2026-06-24 20:04:29', '2026-06-24 20:04:29'),
(22, 'Ad-Hoc: laporan 2 (1782332265)', 5, NULL, 0, '2026-06-24 20:17:45', '2026-06-24 20:17:45'),
(23, 'Ad-Hoc: laporan (1782460028)', 1, NULL, 0, '2026-06-26 07:47:08', '2026-06-26 07:47:08'),
(24, 'Ad-Hoc: laporan (1782461670)', 1, NULL, 0, '2026-06-26 08:14:30', '2026-06-26 08:14:30'),
(25, 'Ad-Hoc: SK Contoh (1782467874)', 1, NULL, 0, '2026-06-26 09:57:54', '2026-06-26 09:57:54'),
(26, 'Ad-Hoc: Tes (1782468721)', 1, NULL, 0, '2026-06-26 10:12:01', '2026-06-26 10:12:01'),
(27, 'Ad-Hoc: laporan (1782646562)', 1, NULL, 0, '2026-06-28 11:36:02', '2026-06-28 11:36:02'),
(28, 'Ad-Hoc: laporan (1782646625)', 1, NULL, 0, '2026-06-28 11:37:05', '2026-06-28 11:37:05'),
(29, 'Ad-Hoc: laporan (1782701487)', 1, NULL, 0, '2026-06-29 02:51:27', '2026-06-29 02:51:27'),
(30, 'Ad-Hoc: laporan test 1 (1782784191)', 1, NULL, 0, '2026-06-30 01:49:51', '2026-06-30 01:49:51'),
(31, 'Ad-Hoc: laporan test 12 (1782786870)', 1, NULL, 0, '2026-06-30 02:34:30', '2026-06-30 02:34:30');

-- --------------------------------------------------------

--
-- Table structure for table `grup_verifikasi_dokumen`
--

CREATE TABLE `grup_verifikasi_dokumen` (
  `id` int UNSIGNED NOT NULL,
  `id_dokumen` int UNSIGNED NOT NULL,
  `id_grup_verifikasi` int UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grup_verifikasi_pengajuan`
--

CREATE TABLE `grup_verifikasi_pengajuan` (
  `id` int UNSIGNED NOT NULL,
  `id_pengajuan` int UNSIGNED NOT NULL,
  `id_grup_verifikasi` int UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lampiran_pengajuan`
--

CREATE TABLE `lampiran_pengajuan` (
  `id` bigint UNSIGNED NOT NULL,
  `id_pengajuan` int UNSIGNED NOT NULL,
  `nama_file` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filepath` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lampiran_pengajuan`
--

INSERT INTO `lampiran_pengajuan` (`id`, `id_pengajuan`, `nama_file`, `filepath`, `created_at`, `updated_at`) VALUES
(1, 1, 'P8-P9_Pemrograman_Basis_Data - Konsep Client Server 1.pdf', 'lampiran_pengajuan/lmp_1_b44a7404-c20f-4e79-844e-b768129a4f13.pdf', '2026-06-29 18:44:01', '2026-06-29 18:44:01'),
(2, 3, 'P8-P9_Pemrograman_Basis_Data - Konsep Client Server 1.pdf', 'lampiran_pengajuan/lmp_3_2edba961-18f4-424f-b42f-44d278ab7e3d.pdf', '2026-06-29 19:33:26', '2026-06-29 19:33:26');

-- --------------------------------------------------------

--
-- Table structure for table `nomor_dokumen`
--

CREATE TABLE `nomor_dokumen` (
  `id` int UNSIGNED NOT NULL,
  `tipe` enum('SK','ST') COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` year NOT NULL,
  `urutan` int UNSIGNED NOT NULL,
  `nomor_terformat` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nomor_dokumen`
--

INSERT INTO `nomor_dokumen` (`id`, `tipe`, `tahun`, `urutan`, `nomor_terformat`, `created_at`) VALUES
(1, 'ST', '2026', 1, '001/ts/ssd', '2026-06-30 01:55:38'),
(2, 'ST', '2026', 2, 'lkjl', '2026-06-30 02:36:45');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan`
--

CREATE TABLE `pengajuan` (
  `id` int UNSIGNED NOT NULL,
  `id_pengguna` int UNSIGNED NOT NULL,
  `id_grup_verifikasi_verifikator` int UNSIGNED DEFAULT NULL,
  `judul` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe` enum('SK','ST') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Draf',
  `daftar_menimbang` text COLLATE utf8mb4_unicode_ci,
  `daftar_memperhatikan` text COLLATE utf8mb4_unicode_ci,
  `daftar_memutuskan` text COLLATE utf8mb4_unicode_ci,
  `ada_lampiran` tinyint(1) NOT NULL DEFAULT '0',
  `filepath` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `filepath_lampiran` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rencana_pengambilan` date DEFAULT NULL,
  `tgl_terbit` date DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `urutan_antrian` int UNSIGNED DEFAULT NULL,
  `id_verifikator_sekarang` bigint UNSIGNED DEFAULT NULL,
  `nomor_diusulkan` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengajuan`
--

INSERT INTO `pengajuan` (`id`, `id_pengguna`, `id_grup_verifikasi_verifikator`, `judul`, `tipe`, `status`, `daftar_menimbang`, `daftar_memperhatikan`, `daftar_memutuskan`, `ada_lampiran`, `filepath`, `filepath_lampiran`, `rencana_pengambilan`, `tgl_terbit`, `catatan`, `urutan_antrian`, `id_verifikator_sekarang`, `nomor_diusulkan`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, 5, 30, 'laporan test 1', 'ST', 'Diterbitkan', '', '', '', 1, 'pengajuan/pengajuan_1_1782783841.pdf', NULL, NULL, NULL, 'nomor tidak ada', 0, NULL, '001/ts/ssd', 0, '2026-06-30 01:44:01', '2026-06-30 01:55:38'),
(2, 1, NULL, 'laporan test 12', 'ST', 'Diproses Admin', '', '', '', 0, 'pengajuan/pengajuan_2_1782785241.docx', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-06-30 02:07:20', '2026-06-30 02:07:21'),
(3, 5, 31, 'laporan test 12', 'ST', 'Diterbitkan', '', '', '', 1, 'pengajuan/pengajuan_3_1782786806.pdf', NULL, NULL, NULL, NULL, 2, NULL, 'lkjl', 0, '2026-06-30 02:33:26', '2026-06-30 02:36:45');

-- --------------------------------------------------------

--
-- Table structure for table `pengajuan_peraturan`
--

CREATE TABLE `pengajuan_peraturan` (
  `id` int UNSIGNED NOT NULL,
  `id_pengajuan` int UNSIGNED NOT NULL,
  `id_peraturan` int UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengajuan_peraturan`
--

INSERT INTO `pengajuan_peraturan` (`id`, `id_pengajuan`, `id_peraturan`, `created_at`) VALUES
(1, 2, 1, '2026-06-30 02:07:21'),
(2, 3, 1, '2026-06-30 02:33:26');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id` int UNSIGNED NOT NULL,
  `nip` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `unit` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jabatan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id`, `nip`, `username`, `password`, `nama`, `unit`, `jabatan`, `is_admin`, `is_deleted`, `created_at`, `updated_at`) VALUES
(1, '123456', 'admin', '$2y$12$QR8BdVy3TSEP441kgtGatuEkPu0DTr33IjgKQatbU10NrAjujgl5i', 'Admin Super', 'Pusat', NULL, 1, 0, '2026-06-07 17:47:17', '2026-06-24 19:32:33'),
(2, '222222', 'sdasd', '$2y$12$HKRsFLRV3EWc6wq5Toys7uEVtnrzuEEtWMq2Bz4LfX9D5q7AAZ4Y2', 'sdsdsd', 'Pusat', 'sdsdsds', 0, 1, '2026-06-08 19:34:14', '2026-06-24 18:10:43'),
(3, '111111', 'test1', '$2y$12$Iq6qiGLYJ2IoHzwWwIz/J.DtukZN5jPkjhG8ArXIgP9uI7XyI2MqC', 'sdss', 'Pusat', 'sdsdsd', 0, 1, '2026-06-08 19:34:44', '2026-06-24 18:11:01'),
(4, '333333', 'verif', '$2y$12$P0BOuT5qft/IJXIXTXsRne9qs0oVOkLN5DoN6.v.axVLCOqaTBTqS', 'verif', 'Pusat', 'sdsdsd', 0, 0, '2026-06-08 19:47:40', '2026-06-08 19:48:02'),
(5, '444444', 'pengguna', '$2y$12$dbQBJkxBw2kXV9hhCsPe9uUw9e531BQ6Ol/Gyo96GjH4A30BD8qfy', 'pengguna', 'Pusat', 'Dosen', 0, 0, '2026-06-08 19:49:50', '2026-06-24 04:42:46'),
(6, '122331', 'verif1', '$2y$12$ovzAq3/veOzufORQoTHN8OjzvCLvZBp3fGUKiFwQhah7/BlySsRxq', 'verif1', 'Jurusan Teknik Elektro', 'staff', 0, 0, '2026-06-24 04:36:21', '2026-06-24 04:37:10'),
(7, '1233221', 'verif2', '$2y$12$JQ2QUWVRFF4MjxzRYHTOdud4Sg.pTVEwI0bQrqvFxw69Rz2mWFjYK', 'verif2', 'Jurusan Teknik Elektro', 'kepala jurusan', 0, 0, '2026-06-24 04:37:00', '2026-06-24 04:37:00'),
(8, '23123231', 'verif3', '$2y$12$L.EU.OnZ4ncN6sl1ryMb9OTLSjx6LbQtRIxC6eVcA4.w7AY.nxvzO', 'verif3', 'Pusat', 'Direktur', 0, 0, '2026-06-24 04:38:04', '2026-06-24 04:38:04'),
(9, '3424324', 'verif3.', '$2y$12$0Z1gcrVaYxHwea8ifWnTeOMrg0C9/VyFI3MfxLpoqt4O1gDDv/wUq', 'verif3.', 'Pusat', 'Wakil direktur', 0, 0, '2026-06-24 04:39:07', '2026-06-24 08:39:58'),
(10, '192825732893', 'simanjuntak.eka', '$2y$10$rOEMspD0lUcAeaDYOeOSE.gcAyLCgDdiHgey1S9UVyrQ48ZQMWRQe', 'Violet Kayla Riyanti', 'Unit accusamus', 'Staf Administrasi', 0, 1, '2026-06-24 16:47:03', '2026-06-24 18:27:58'),
(11, '195327593917', 'iriana11', '$2y$10$W6iwQ8mi2t.ZB.foHXqsfuy2OP7oyQKc2DKFVVtOzzWDBnWNH5Cau', 'Galih Sihombing', 'Unit est', 'Direktur', 0, 1, '2026-06-24 16:47:03', '2026-06-26 02:12:10'),
(12, '195829943174', 'rusman.wijayanti', '$2y$10$jGirmAzPPvQGS7GjkJpKROAw0g4wQybixdAy1aNHlo1Ni5lnvc3fS', 'Danuja Iswahyudi', 'Unit blanditiis', 'Kajur Informatika', 0, 1, '2026-06-24 16:47:03', '2026-06-26 02:12:23'),
(13, '193121397073', 'manggriawan', '$2y$12$QR8BdVy3TSEP441kgtGatuEkPu0DTr33IjgKQatbU10NrAjujgl5i', 'Aurora Purwanti', 'Unit quia', 'Direktur', 0, 1, '2026-06-24 16:47:03', '2026-06-26 02:12:30'),
(14, '196242743393', 'rika.yulianti', '$2y$10$q5Ddq8IZTP5tOfWFTBD0U.bhkT9dVGAbR5QuCuxUNHrqgKparfKLK', 'Cahyo Firmansyah', 'Unit rerum', 'Laboran', 0, 1, '2026-06-24 16:47:03', '2026-06-26 02:12:36');

-- --------------------------------------------------------

--
-- Table structure for table `peraturan`
--

CREATE TABLE `peraturan` (
  `id` int UNSIGNED NOT NULL,
  `id_pengguna` int UNSIGNED NOT NULL,
  `kode` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `judul` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun` year NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `peraturan`
--

INSERT INTO `peraturan` (`id`, `id_pengguna`, `kode`, `judul`, `jenis`, `tahun`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, 1, 'UU no. 2021', 'penambahan anggota perkerja', 'UU', '2026', NULL, '2026-06-30 01:12:16', '2026-06-30 01:12:16');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_pengajuan`
--

CREATE TABLE `riwayat_pengajuan` (
  `id` int UNSIGNED NOT NULL,
  `id_pengajuan` int UNSIGNED NOT NULL,
  `id_pengguna` int UNSIGNED NOT NULL,
  `aksi` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `catatan_aksi` text COLLATE utf8mb4_unicode_ci,
  `versi` int UNSIGNED NOT NULL DEFAULT '1',
  `snapshot_konten` json DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `riwayat_pengajuan`
--

INSERT INTO `riwayat_pengajuan` (`id`, `id_pengajuan`, `id_pengguna`, `aksi`, `catatan_aksi`, `versi`, `snapshot_konten`, `created_at`) VALUES
(1, 1, 5, 'DIBUAT', 'Pengajuan baru dibuat', 1, NULL, '2026-06-30 01:44:01'),
(2, 1, 1, 'Diteruskan ke Verifikator', 'Admin mengirim dokumen ke Antrian Verifikasi', 1, NULL, '2026-06-30 01:49:51'),
(3, 1, 6, 'DITOLAK', 'Verifikator menolak dokumen. Catatan: nomor tidak ada', 1, NULL, '2026-06-30 01:52:21'),
(4, 1, 1, 'Diteruskan ke Verifikator', 'Admin mengirim ulang dokumen ke Verifikator (Re-Review)', 1, NULL, '2026-06-30 01:53:19'),
(5, 1, 6, 'DITERBITKAN', 'Verifikator akhir menyetujui dokumen dan dokumen resmi diterbitkan', 1, NULL, '2026-06-30 01:55:38'),
(6, 2, 1, 'DIBUAT', 'Pengajuan baru dibuat', 1, NULL, '2026-06-30 02:07:21'),
(7, 3, 5, 'DIBUAT', 'Pengajuan baru dibuat', 1, NULL, '2026-06-30 02:33:27'),
(8, 3, 1, 'Diteruskan ke Verifikator', 'Admin mengirim dokumen ke Antrian Verifikasi', 1, NULL, '2026-06-30 02:34:30'),
(9, 3, 6, 'DISETUJUI', 'Verifikator menyetujui dokumen (menunggu verifikator selanjutnya)', 1, NULL, '2026-06-30 02:35:19'),
(10, 3, 7, 'DISETUJUI', 'Verifikator menyetujui dokumen (menunggu verifikator selanjutnya)', 1, NULL, '2026-06-30 02:36:04'),
(11, 3, 8, 'DITERBITKAN', 'Verifikator akhir menyetujui dokumen dan dokumen resmi diterbitkan', 1, NULL, '2026-06-30 02:36:45');

-- --------------------------------------------------------

--
-- Table structure for table `template_surat`
--

CREATE TABLE `template_surat` (
  `id` int UNSIGNED NOT NULL,
  `id_pengguna` int UNSIGNED NOT NULL,
  `tipe` enum('SK','ST') COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_template` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `filepath` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `versi` int UNSIGNED NOT NULL DEFAULT '1',
  `is_aktif` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `template_surat`
--

INSERT INTO `template_surat` (`id`, `id_pengguna`, `tipe`, `nama_template`, `filepath`, `versi`, `is_aktif`, `created_at`, `updated_at`) VALUES
(1, 1, 'ST', 'Surat tes 1', 'templates/contoh_surat_satu.docx', 1, 1, '2026-06-07 18:00:10', '2026-06-30 01:02:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `anggota_dokumen`
--
ALTER TABLE `anggota_dokumen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_adok_dok_pengguna` (`id_dokumen`,`id_pengguna`),
  ADD KEY `fk_adok_pengguna` (`id_pengguna`);

--
-- Indexes for table `anggota_grup_verifikasi`
--
ALTER TABLE `anggota_grup_verifikasi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_agv_grup_pengguna` (`id_grup_verifikasi`,`id_pengguna`),
  ADD KEY `fk_agv_pengguna` (`id_pengguna`);

--
-- Indexes for table `anggota_pengajuan`
--
ALTER TABLE `anggota_pengajuan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_apgj_pgj_pengguna` (`id_pengajuan`,`id_pengguna`),
  ADD KEY `fk_apgj_pengguna` (`id_pengguna`);

--
-- Indexes for table `dokumen`
--
ALTER TABLE `dokumen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_dokumen_kode_unik` (`kode_unik`),
  ADD KEY `fk_dok_nomor` (`id_nomor_dokumen`),
  ADD KEY `fk_dok_pengguna` (`id_pengguna`);

--
-- Indexes for table `grup_verifikasi`
--
ALTER TABLE `grup_verifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_gv_pengguna` (`id_pengguna`);

--
-- Indexes for table `grup_verifikasi_dokumen`
--
ALTER TABLE `grup_verifikasi_dokumen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_gvd_dokumen` (`id_dokumen`),
  ADD KEY `fk_gvd_grup` (`id_grup_verifikasi`);

--
-- Indexes for table `grup_verifikasi_pengajuan`
--
ALTER TABLE `grup_verifikasi_pengajuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_gvp_pengajuan` (`id_pengajuan`),
  ADD KEY `fk_gvp_grup` (`id_grup_verifikasi`);

--
-- Indexes for table `lampiran_pengajuan`
--
ALTER TABLE `lampiran_pengajuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_pengajuan` (`id_pengajuan`);

--
-- Indexes for table `nomor_dokumen`
--
ALTER TABLE `nomor_dokumen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_nomor_terformat` (`nomor_terformat`),
  ADD UNIQUE KEY `uq_tipe_tahun_urutan` (`tipe`,`tahun`,`urutan`);

--
-- Indexes for table `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pgj_pengguna` (`id_pengguna`),
  ADD KEY `fk_pgj_grup_verifikasi` (`id_grup_verifikasi_verifikator`);

--
-- Indexes for table `pengajuan_peraturan`
--
ALTER TABLE `pengajuan_peraturan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pgj_peraturan` (`id_pengajuan`,`id_peraturan`),
  ADD KEY `fk_pgj_per_peraturan` (`id_peraturan`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pengguna_nip` (`nip`),
  ADD UNIQUE KEY `uq_pengguna_username` (`username`);

--
-- Indexes for table `peraturan`
--
ALTER TABLE `peraturan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_peraturan_kode` (`kode`),
  ADD KEY `fk_peraturan_pengguna` (`id_pengguna`);

--
-- Indexes for table `riwayat_pengajuan`
--
ALTER TABLE `riwayat_pengajuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rwy_pengajuan` (`id_pengajuan`),
  ADD KEY `fk_rwy_pengguna` (`id_pengguna`);

--
-- Indexes for table `template_surat`
--
ALTER TABLE `template_surat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tmpl_pengguna` (`id_pengguna`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `anggota_dokumen`
--
ALTER TABLE `anggota_dokumen`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `anggota_grup_verifikasi`
--
ALTER TABLE `anggota_grup_verifikasi`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `anggota_pengajuan`
--
ALTER TABLE `anggota_pengajuan`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dokumen`
--
ALTER TABLE `dokumen`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `grup_verifikasi`
--
ALTER TABLE `grup_verifikasi`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `grup_verifikasi_dokumen`
--
ALTER TABLE `grup_verifikasi_dokumen`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grup_verifikasi_pengajuan`
--
ALTER TABLE `grup_verifikasi_pengajuan`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lampiran_pengajuan`
--
ALTER TABLE `lampiran_pengajuan`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `nomor_dokumen`
--
ALTER TABLE `nomor_dokumen`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pengajuan`
--
ALTER TABLE `pengajuan`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `pengajuan_peraturan`
--
ALTER TABLE `pengajuan_peraturan`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `peraturan`
--
ALTER TABLE `peraturan`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `riwayat_pengajuan`
--
ALTER TABLE `riwayat_pengajuan`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `template_surat`
--
ALTER TABLE `template_surat`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `anggota_dokumen`
--
ALTER TABLE `anggota_dokumen`
  ADD CONSTRAINT `fk_adok_dokumen` FOREIGN KEY (`id_dokumen`) REFERENCES `dokumen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_adok_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `anggota_grup_verifikasi`
--
ALTER TABLE `anggota_grup_verifikasi`
  ADD CONSTRAINT `fk_agv_grup` FOREIGN KEY (`id_grup_verifikasi`) REFERENCES `grup_verifikasi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_agv_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `anggota_pengajuan`
--
ALTER TABLE `anggota_pengajuan`
  ADD CONSTRAINT `fk_apgj_pengajuan` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_apgj_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dokumen`
--
ALTER TABLE `dokumen`
  ADD CONSTRAINT `fk_dok_nomor` FOREIGN KEY (`id_nomor_dokumen`) REFERENCES `nomor_dokumen` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dok_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `grup_verifikasi`
--
ALTER TABLE `grup_verifikasi`
  ADD CONSTRAINT `fk_gv_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `grup_verifikasi_dokumen`
--
ALTER TABLE `grup_verifikasi_dokumen`
  ADD CONSTRAINT `fk_gvd_dokumen` FOREIGN KEY (`id_dokumen`) REFERENCES `dokumen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_gvd_grup` FOREIGN KEY (`id_grup_verifikasi`) REFERENCES `grup_verifikasi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `grup_verifikasi_pengajuan`
--
ALTER TABLE `grup_verifikasi_pengajuan`
  ADD CONSTRAINT `fk_gvp_grup` FOREIGN KEY (`id_grup_verifikasi`) REFERENCES `grup_verifikasi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_gvp_pengajuan` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lampiran_pengajuan`
--
ALTER TABLE `lampiran_pengajuan`
  ADD CONSTRAINT `lampiran_pengajuan_ibfk_1` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD CONSTRAINT `fk_pgj_grup_verifikasi` FOREIGN KEY (`id_grup_verifikasi_verifikator`) REFERENCES `grup_verifikasi` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pgj_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `pengajuan_peraturan`
--
ALTER TABLE `pengajuan_peraturan`
  ADD CONSTRAINT `fk_pgj_per_pengajuan` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pgj_per_peraturan` FOREIGN KEY (`id_peraturan`) REFERENCES `peraturan` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `peraturan`
--
ALTER TABLE `peraturan`
  ADD CONSTRAINT `fk_peraturan_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `riwayat_pengajuan`
--
ALTER TABLE `riwayat_pengajuan`
  ADD CONSTRAINT `fk_rwy_pengajuan` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rwy_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `template_surat`
--
ALTER TABLE `template_surat`
  ADD CONSTRAINT `fk_tmpl_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
