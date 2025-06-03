-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 03, 2025 at 05:41 PM
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
-- Database: `db_pa1`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dokumen_proyek_akhir`
--

CREATE TABLE `dokumen_proyek_akhir` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mahasiswa_id` bigint(20) UNSIGNED NOT NULL,
  `jenis_dokumen_id` bigint(20) UNSIGNED NOT NULL,
  `nama_file_asli` varchar(255) NOT NULL,
  `nama_file_unik` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `ekstensi_file` varchar(10) NOT NULL,
  `ukuran_file` bigint(20) UNSIGNED NOT NULL,
  `versi` int(11) NOT NULL DEFAULT 1,
  `catatan_mahasiswa` text DEFAULT NULL,
  `status_review` enum('pending','approved','revision_needed') NOT NULL DEFAULT 'pending',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `catatan_reviewer` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE `dosen` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nidn` varchar(20) NOT NULL,
  `prodi_id` bigint(20) UNSIGNED NOT NULL,
  `spesialisasi` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dosen`
--

INSERT INTO `dosen` (`id`, `user_id`, `nidn`, `prodi_id`, `spesialisasi`, `created_at`, `updated_at`) VALUES
(1, 3, '0124098904', 1, 'Lektor', '2025-06-03 01:46:25', '2025-06-03 01:46:25');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `history_bimbingan`
--

CREATE TABLE `history_bimbingan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mahasiswa_id` bigint(20) UNSIGNED NOT NULL,
  `dosen_id` bigint(20) UNSIGNED NOT NULL,
  `request_bimbingan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tanggal_bimbingan` datetime NOT NULL,
  `topik` varchar(255) NOT NULL,
  `catatan_mahasiswa` text DEFAULT NULL,
  `catatan_dosen` text DEFAULT NULL,
  `pertemuan_ke` int(10) UNSIGNED DEFAULT NULL,
  `status_kehadiran` enum('hadir','tidak_hadir_mahasiswa','tidak_hadir_dosen') NOT NULL DEFAULT 'hadir',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `history_bimbingan`
--

INSERT INTO `history_bimbingan` (`id`, `mahasiswa_id`, `dosen_id`, `request_bimbingan_id`, `tanggal_bimbingan`, `topik`, `catatan_mahasiswa`, `catatan_dosen`, `pertemuan_ke`, `status_kehadiran`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2025-06-03 15:00:00', 'Fitur yang akan ada pada web del cafee', NULL, NULL, 1, 'hadir', '2025-06-03 01:52:47', '2025-06-03 01:52:47'),
(2, 2, 1, 3, '2025-06-05 08:00:00', 'berdiskusi sebelum maju menuju seminar', NULL, NULL, 1, 'hadir', '2025-06-03 02:24:56', '2025-06-03 02:24:56'),
(3, 3, 1, 5, '2025-06-05 10:00:00', '312iasduip321908adsisadjhlsadmn', NULL, NULL, 1, 'hadir', '2025-06-03 06:50:56', '2025-06-03 06:50:56');

-- --------------------------------------------------------

--
-- Table structure for table `jenis_dokumen`
--

CREATE TABLE `jenis_dokumen` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_jenis` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE `mahasiswa` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nim` varchar(20) NOT NULL,
  `prodi_id` bigint(20) UNSIGNED NOT NULL,
  `angkatan` year(4) NOT NULL,
  `nomor_kelompok` varchar(50) DEFAULT NULL,
  `dosen_pembimbing_id` bigint(20) UNSIGNED DEFAULT NULL,
  `judul_proyek_akhir` text DEFAULT NULL,
  `status_proyek_akhir` enum('belum_ada','pengajuan_judul','bimbingan','selesai','revisi') NOT NULL DEFAULT 'belum_ada',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`id`, `user_id`, `nim`, `prodi_id`, `angkatan`, `nomor_kelompok`, `dosen_pembimbing_id`, `judul_proyek_akhir`, `status_proyek_akhir`, `created_at`, `updated_at`) VALUES
(1, 2, '42324001', 1, '2024', '11', 1, 'Rancang bangun website Del Cafee', 'bimbingan', '2025-06-03 01:45:58', '2025-06-03 01:49:27'),
(2, 4, '42324038', 1, '2024', '4', 1, 'sistem informasi proyek akhir', 'bimbingan', '2025-06-03 01:59:37', '2025-06-03 02:11:53'),
(3, 7, '42324005', 1, '2024', '4', 1, 'sistem informasi proyek akhir', 'bimbingan', '2025-06-03 05:27:01', '2025-06-03 06:17:56'),
(4, 8, '42324012', 1, '2024', '4', NULL, NULL, 'belum_ada', '2025-06-03 07:41:03', '2025-06-03 07:41:03'),
(5, 9, '42324039', 1, '2024', '3', 1, 'sistem informasi proyek akhir', 'bimbingan', '2025-06-03 07:42:13', '2025-06-03 08:12:50'),
(7, 11, '42324006', 1, '2024', '12', NULL, NULL, 'belum_ada', '2025-06-03 07:57:50', '2025-06-03 07:57:50');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2025_05_13_072407_create_users_table', 1),
(4, '2025_05_13_072424_create_prodi_table', 1),
(5, '2025_05_13_072438_create_dosen_table', 1),
(6, '2025_05_13_072453_create_mahasiswa_table', 1),
(7, '2025_05_13_072513_create_request_judul_table', 1),
(8, '2025_05_13_072526_create_request_bimbingan_table', 1),
(9, '2025_05_13_072538_create_history_bimbingan_table', 1),
(10, '2025_05_13_072553_create_jenis_dokumen_table', 1),
(11, '2025_05_13_072615_create_dokumen_proyek_akhir_table', 1),
(12, '2025_05_13_072629_create_forums_table', 1),
(13, '2025_05_13_072643_create_forum_messages_table', 1),
(14, '2025_05_13_072700_create_log_activities_table', 1),
(15, '2025_05_13_075414_create_personal_access_tokens_table', 1),
(16, '2025_05_13_132432_create_sessions_table', 1),
(17, '2025_05_14_033702_add_user_id_to_dosen_table', 1),
(18, '2025_05_14_083039_add_timestamps_and_details_to_dokumen_proyek_akhir_table', 1),
(19, '2025_05_14_084228_remove_nama_file_column_from_dokumen_proyek_akhir_table', 1),
(20, '2025_05_14_084301_modify_nama_file_column_in_dokumen_proyek_akhir_table', 1),
(21, '2025_05_14_135151_add_fields_to_request_bimbingan_table', 1),
(22, '2025_05_14_161132_add_deleted_at_to_users_table', 1),
(23, '2025_06_02_023310_create_notifications_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) NOT NULL,
  `type` varchar(255) NOT NULL,
  `notifiable_type` varchar(255) NOT NULL,
  `notifiable_id` bigint(20) UNSIGNED NOT NULL,
  `data` text NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `notifiable_type`, `notifiable_id`, `data`, `read_at`, `created_at`, `updated_at`) VALUES
('0187ea9f-073c-4533-9bc3-6cfccd0a629a', 'App\\Notifications\\RequestJudulNotification', 'App\\Models\\User', 10, '{\"title\":\"Respon Judul\",\"message\":\"Dosen telah memberikan respon pada pengajuan judul Anda.\",\"request_judul_id\":7,\"from\":\"Hernawati Susanti Samosir, SST., M.Kom.\",\"role\":\"dosen\"}', NULL, '2025-06-03 08:21:52', '2025-06-03 08:21:52'),
('0b3173a2-8e75-4e34-ad2b-a05ed641998d', 'App\\Notifications\\RequestJudulNotification', 'App\\Models\\User', 3, '{\"title\":\"Pengajuan Judul Baru\",\"message\":\"Ada pengajuan judul baru dari mahasiswa: Greis Lumbantoruan\",\"request_judul_id\":8,\"from\":\"Greis Lumbantoruan\",\"role\":\"mahasiswa\"}', NULL, '2025-06-03 08:19:12', '2025-06-03 08:19:12'),
('0ced442f-0371-4c9e-bbd2-6e99daef1be1', 'App\\Notifications\\RequestBimbinganNotification', 'App\\Models\\User', 3, '{\"title\":\"Pengajuan Bimbingan Baru\",\"message\":\"Ada pengajuan bimbingan baru dari mahasiswa: oktova samosir\",\"request_bimbingan_id\":1,\"from\":\"oktova samosir\",\"role\":\"mahasiswa\"}', '2025-06-03 01:52:51', '2025-06-03 01:51:08', '2025-06-03 01:52:51'),
('10af44f2-969d-4cd4-8e68-df17147b77b4', 'App\\Notifications\\RequestJudulNotification', 'App\\Models\\User', 3, '{\"title\":\"Pengajuan Judul Baru\",\"message\":\"Ada pengajuan judul baru dari mahasiswa: Gabe Akasia Simanjuntak\",\"request_judul_id\":6,\"from\":\"Gabe Akasia Simanjuntak\",\"role\":\"mahasiswa\"}', NULL, '2025-06-03 08:11:34', '2025-06-03 08:11:34'),
('1337b2ad-74be-4187-a7ef-19e09c970870', 'App\\Notifications\\RequestBimbinganNotification', 'App\\Models\\User', 2, '{\"title\":\"Respon Bimbingan\",\"message\":\"Dosen telah memberikan respon pada pengajuan bimbingan Anda.\",\"request_bimbingan_id\":1,\"from\":\"Hernawati Susanti Samosir, SST., M.Kom.\",\"role\":\"dosen\"}', '2025-06-03 01:53:06', '2025-06-03 01:52:47', '2025-06-03 01:53:06'),
('186ffec7-bcd4-45a2-be47-3e26615e7c38', 'App\\Notifications\\RequestJudulNotification', 'App\\Models\\User', 9, '{\"title\":\"Respon Judul\",\"message\":\"Dosen telah memberikan respon pada pengajuan judul Anda.\",\"request_judul_id\":5,\"from\":\"Hernawati Susanti Samosir, SST., M.Kom.\",\"role\":\"dosen\"}', NULL, '2025-06-03 08:12:52', '2025-06-03 08:12:52'),
('212352f9-fe90-4302-8a55-9b9ed98622f1', 'App\\Notifications\\RequestBimbinganNotification', 'App\\Models\\User', 2, '{\"title\":\"Respon Bimbingan\",\"message\":\"Dosen telah memberikan respon pada pengajuan bimbingan Anda.\",\"request_bimbingan_id\":2,\"from\":\"Hernawati Susanti Samosir, SST., M.Kom.\",\"role\":\"dosen\"}', NULL, '2025-06-03 02:25:21', '2025-06-03 02:25:21'),
('39806307-161d-4c2c-8744-66fcc734bff2', 'App\\Notifications\\RequestJudulNotification', 'App\\Models\\User', 3, '{\"title\":\"Pengajuan Judul Baru\",\"message\":\"Ada pengajuan judul baru dari mahasiswa: Frans Adriano Sihombing\",\"request_judul_id\":3,\"from\":\"Frans Adriano Sihombing\",\"role\":\"mahasiswa\"}', NULL, '2025-06-03 05:44:03', '2025-06-03 05:44:03'),
('431ec5e0-cee9-40c7-811b-c513518d4c8e', 'App\\Notifications\\RequestBimbinganNotification', 'App\\Models\\User', 4, '{\"title\":\"Respon Bimbingan\",\"message\":\"Dosen telah memberikan respon pada pengajuan bimbingan Anda.\",\"request_bimbingan_id\":3,\"from\":\"Hernawati Susanti Samosir, SST., M.Kom.\",\"role\":\"dosen\"}', '2025-06-03 02:27:56', '2025-06-03 02:24:57', '2025-06-03 02:27:56'),
('469d2522-2297-4bd0-81d1-0acfc1f18964', 'App\\Notifications\\RequestJudulNotification', 'App\\Models\\User', 3, '{\"title\":\"Pengajuan Judul Baru\",\"message\":\"Ada pengajuan judul baru dari mahasiswa: Greis Lumbantoruan\",\"request_judul_id\":9,\"from\":\"Greis Lumbantoruan\",\"role\":\"mahasiswa\"}', NULL, '2025-06-03 08:19:36', '2025-06-03 08:19:36'),
('7bdc800f-9d18-4059-81ee-1f1c1234fb9c', 'App\\Notifications\\RequestJudulNotification', 'App\\Models\\User', 3, '{\"title\":\"Pengajuan Judul Baru\",\"message\":\"Ada pengajuan judul baru dari mahasiswa: Gabe Akasia Simanjuntak\",\"request_judul_id\":5,\"from\":\"Gabe Akasia Simanjuntak\",\"role\":\"mahasiswa\"}', NULL, '2025-06-03 08:11:19', '2025-06-03 08:11:19'),
('8061ab49-d906-4321-acba-965a834781c1', 'App\\Notifications\\RequestBimbinganNotification', 'App\\Models\\User', 3, '{\"title\":\"Pengajuan Bimbingan Baru\",\"message\":\"Ada pengajuan bimbingan baru dari mahasiswa: ADITIA XAVERIUS ARAPENTA TARIGAN\",\"request_bimbingan_id\":4,\"from\":\"ADITIA XAVERIUS ARAPENTA TARIGAN\",\"role\":\"mahasiswa\"}', NULL, '2025-06-03 05:57:06', '2025-06-03 05:57:06'),
('8922bb4c-3db1-4700-aa6a-e9b52a30b122', 'App\\Notifications\\RequestJudulNotification', 'App\\Models\\User', 3, '{\"title\":\"Pengajuan Judul Baru\",\"message\":\"Ada pengajuan judul baru dari mahasiswa: Gabe Akasia Simanjuntak\",\"request_judul_id\":4,\"from\":\"Gabe Akasia Simanjuntak\",\"role\":\"mahasiswa\"}', NULL, '2025-06-03 08:11:04', '2025-06-03 08:11:04'),
('a24067a1-14a4-4d85-80fd-a66d5f3be5c2', 'App\\Notifications\\RequestJudulNotification', 'App\\Models\\User', 10, '{\"title\":\"Respon Judul\",\"message\":\"Dosen telah memberikan respon pada pengajuan judul Anda.\",\"request_judul_id\":9,\"from\":\"Hernawati Susanti Samosir, SST., M.Kom.\",\"role\":\"dosen\"}', NULL, '2025-06-03 08:20:12', '2025-06-03 08:20:12'),
('a4a4d693-ede5-4616-a873-e37d8b00f5b3', 'App\\Notifications\\RequestBimbinganNotification', 'App\\Models\\User', 3, '{\"title\":\"Pengajuan Bimbingan Baru\",\"message\":\"Ada pengajuan bimbingan baru dari mahasiswa: oktova samosir\",\"request_bimbingan_id\":2,\"from\":\"oktova samosir\",\"role\":\"mahasiswa\"}', '2025-06-03 02:15:03', '2025-06-03 01:54:32', '2025-06-03 02:15:03'),
('aabb780b-60ea-4226-92e0-5636cf55bff6', 'App\\Notifications\\RequestJudulNotification', 'App\\Models\\User', 10, '{\"title\":\"Respon Judul\",\"message\":\"Dosen telah memberikan respon pada pengajuan judul Anda.\",\"request_judul_id\":8,\"from\":\"Hernawati Susanti Samosir, SST., M.Kom.\",\"role\":\"dosen\"}', NULL, '2025-06-03 08:20:24', '2025-06-03 08:20:24'),
('b60d4be9-4efe-4667-9d24-2350f9f7e2f3', 'App\\Notifications\\RequestJudulNotification', 'App\\Models\\User', 3, '{\"title\":\"Pengajuan Judul Baru\",\"message\":\"Ada pengajuan judul baru dari mahasiswa: Greis Lumbantoruan\",\"request_judul_id\":7,\"from\":\"Greis Lumbantoruan\",\"role\":\"mahasiswa\"}', NULL, '2025-06-03 08:18:51', '2025-06-03 08:18:51'),
('b73a537a-4631-4958-bf3d-8911586504de', 'App\\Notifications\\RequestJudulNotification', 'App\\Models\\User', 2, '{\"title\":\"Respon Judul\",\"message\":\"Dosen telah memberikan respon pada pengajuan judul Anda.\",\"request_judul_id\":1,\"from\":\"Hernawati Susanti Samosir, SST., M.Kom.\",\"role\":\"dosen\"}', '2025-06-03 01:50:05', '2025-06-03 01:49:28', '2025-06-03 01:50:05'),
('b8c9084a-b4be-48fe-a6ba-e49a94cc34a6', 'App\\Notifications\\RequestBimbinganNotification', 'App\\Models\\User', 3, '{\"title\":\"Pengajuan Bimbingan Baru\",\"message\":\"Ada pengajuan bimbingan baru dari mahasiswa: ADITIA XAVERIUS ARAPENTA TARIGAN\",\"request_bimbingan_id\":3,\"from\":\"ADITIA XAVERIUS ARAPENTA TARIGAN\",\"role\":\"mahasiswa\"}', '2025-06-03 02:27:12', '2025-06-03 02:16:40', '2025-06-03 02:27:12'),
('bf041cd6-4886-4ab4-a796-4caca51b2155', 'App\\Notifications\\RequestBimbinganNotification', 'App\\Models\\User', 4, '{\"title\":\"Respon Bimbingan\",\"message\":\"Dosen telah memberikan respon pada pengajuan bimbingan Anda.\",\"request_bimbingan_id\":4,\"from\":\"Hernawati Susanti Samosir, SST., M.Kom.\",\"role\":\"dosen\"}', NULL, '2025-06-03 07:19:49', '2025-06-03 07:19:49'),
('c499192e-8d8c-438c-aa23-8cebecb6b447', 'App\\Notifications\\RequestBimbinganNotification', 'App\\Models\\User', 4, '{\"title\":\"Respon Bimbingan\",\"message\":\"Dosen telah memberikan respon pada pengajuan bimbingan Anda.\",\"request_bimbingan_id\":6,\"from\":\"Hernawati Susanti Samosir, SST., M.Kom.\",\"role\":\"dosen\"}', NULL, '2025-06-03 07:24:41', '2025-06-03 07:24:41'),
('cb016262-a548-4e13-ab32-906e2ac6d0d7', 'App\\Notifications\\RequestJudulNotification', 'App\\Models\\User', 7, '{\"title\":\"Respon Judul\",\"message\":\"Dosen telah memberikan respon pada pengajuan judul Anda.\",\"request_judul_id\":3,\"from\":\"Hernawati Susanti Samosir, SST., M.Kom.\",\"role\":\"dosen\"}', NULL, '2025-06-03 06:17:58', '2025-06-03 06:17:58'),
('cfc9544e-a363-49db-9b98-5258a52cea67', 'App\\Notifications\\RequestBimbinganNotification', 'App\\Models\\User', 7, '{\"title\":\"Respon Bimbingan\",\"message\":\"Dosen telah memberikan respon pada pengajuan bimbingan Anda.\",\"request_bimbingan_id\":5,\"from\":\"Hernawati Susanti Samosir, SST., M.Kom.\",\"role\":\"dosen\"}', NULL, '2025-06-03 06:50:57', '2025-06-03 06:50:57'),
('d35810bd-831c-4a7d-a4d1-3ec93aaba988', 'App\\Notifications\\RequestJudulNotification', 'App\\Models\\User', 3, '{\"title\":\"Pengajuan Judul Baru\",\"message\":\"Ada pengajuan judul baru dari mahasiswa: oktova samosir\",\"request_judul_id\":1,\"from\":\"oktova samosir\",\"role\":\"mahasiswa\"}', '2025-06-03 01:48:02', '2025-06-03 01:47:34', '2025-06-03 01:48:02'),
('d5a35e81-74c7-4abc-8690-051a061db280', 'App\\Notifications\\RequestJudulNotification', 'App\\Models\\User', 3, '{\"title\":\"Pengajuan Judul Baru\",\"message\":\"Ada pengajuan judul baru dari mahasiswa: ADITIA XAVERIUS ARAPENTA TARIGAN\",\"request_judul_id\":2,\"from\":\"ADITIA XAVERIUS ARAPENTA TARIGAN\",\"role\":\"mahasiswa\"}', '2025-06-03 02:15:03', '2025-06-03 02:00:19', '2025-06-03 02:15:03'),
('e11eecd6-5c9b-41a4-9d62-e8d641d42819', 'App\\Notifications\\RequestBimbinganNotification', 'App\\Models\\User', 3, '{\"title\":\"Pengajuan Bimbingan Baru\",\"message\":\"Ada pengajuan bimbingan baru dari mahasiswa: ADITIA XAVERIUS ARAPENTA TARIGAN\",\"request_bimbingan_id\":6,\"from\":\"ADITIA XAVERIUS ARAPENTA TARIGAN\",\"role\":\"mahasiswa\"}', NULL, '2025-06-03 07:23:17', '2025-06-03 07:23:17'),
('ec8ab05a-dc13-413a-bb73-24c72c12be64', 'App\\Notifications\\RequestBimbinganNotification', 'App\\Models\\User', 3, '{\"title\":\"Pengajuan Bimbingan Baru\",\"message\":\"Ada pengajuan bimbingan baru dari mahasiswa: Frans Adriano Sihombing\",\"request_bimbingan_id\":5,\"from\":\"Frans Adriano Sihombing\",\"role\":\"mahasiswa\"}', NULL, '2025-06-03 06:50:12', '2025-06-03 06:50:12'),
('f6983b0d-491c-4c3b-ba79-4074ee4c9221', 'App\\Notifications\\RequestJudulNotification', 'App\\Models\\User', 9, '{\"title\":\"Respon Judul\",\"message\":\"Dosen telah memberikan respon pada pengajuan judul Anda.\",\"request_judul_id\":6,\"from\":\"Hernawati Susanti Samosir, SST., M.Kom.\",\"role\":\"dosen\"}', NULL, '2025-06-03 08:12:25', '2025-06-03 08:12:25'),
('f8be3e50-d5bd-4ee2-af3f-0555b696c62e', 'App\\Notifications\\RequestBimbinganNotification', 'App\\Models\\User', 3, '{\"title\":\"Pengajuan Bimbingan Baru\",\"message\":\"Ada pengajuan bimbingan baru dari mahasiswa: ADITIA XAVERIUS ARAPENTA TARIGAN\",\"request_bimbingan_id\":7,\"from\":\"ADITIA XAVERIUS ARAPENTA TARIGAN\",\"role\":\"mahasiswa\"}', NULL, '2025-06-03 07:26:05', '2025-06-03 07:26:05'),
('fcb052d4-c995-4016-aa00-ed36db464b2e', 'App\\Notifications\\RequestJudulNotification', 'App\\Models\\User', 4, '{\"title\":\"Respon Judul\",\"message\":\"Dosen telah memberikan respon pada pengajuan judul Anda.\",\"request_judul_id\":2,\"from\":\"Hernawati Susanti Samosir, SST., M.Kom.\",\"role\":\"dosen\"}', '2025-06-03 02:27:56', '2025-06-03 02:11:54', '2025-06-03 02:27:56'),
('ff279d1e-d141-424b-b5b4-4f03e7c9e45b', 'App\\Notifications\\RequestJudulNotification', 'App\\Models\\User', 9, '{\"title\":\"Respon Judul\",\"message\":\"Dosen telah memberikan respon pada pengajuan judul Anda.\",\"request_judul_id\":4,\"from\":\"Hernawati Susanti Samosir, SST., M.Kom.\",\"role\":\"dosen\"}', NULL, '2025-06-03 08:12:43', '2025-06-03 08:12:43');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prodi`
--

CREATE TABLE `prodi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kode_prodi` varchar(20) NOT NULL,
  `nama_prodi` varchar(100) NOT NULL,
  `fakultas` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `prodi`
--

INSERT INTO `prodi` (`id`, `kode_prodi`, `nama_prodi`, `fakultas`) VALUES
(1, 'TI', 'Teknologi Informasi', 'Vokasi'),
(2, 'TK', 'Teknologi Komputer', 'Vokasi'),
(3, 'TRPL', 'Teknologi Rekayasa Perangkat Lunak', 'Vokasi');

-- --------------------------------------------------------

--
-- Table structure for table `request_bimbingan`
--

CREATE TABLE `request_bimbingan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mahasiswa_id` bigint(20) UNSIGNED NOT NULL,
  `dosen_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_usulan` date NOT NULL,
  `tanggal_dosen` date DEFAULT NULL,
  `jam_usulan` time NOT NULL,
  `jam_dosen` time DEFAULT NULL,
  `lokasi_usulan` varchar(255) DEFAULT NULL,
  `topik_bimbingan` text NOT NULL,
  `status_request` enum('pending','approved','rejected','rescheduled') NOT NULL DEFAULT 'pending',
  `catatan` text DEFAULT NULL,
  `catatan_dosen` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `request_bimbingan`
--

INSERT INTO `request_bimbingan` (`id`, `mahasiswa_id`, `dosen_id`, `tanggal_usulan`, `tanggal_dosen`, `jam_usulan`, `jam_dosen`, `lokasi_usulan`, `topik_bimbingan`, `status_request`, `catatan`, `catatan_dosen`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-06-03', '2025-06-03', '15:00:00', '15:00:00', 'Gd Vokasi', 'Fitur yang akan ada pada web del cafee', 'approved', NULL, 'Segera', '2025-06-03 01:51:06', '2025-06-03 01:52:47'),
(2, 1, 1, '2025-06-05', NULL, '10:00:00', NULL, 'GD 515', 'Menanyakan prihal kewajiban hosting', 'rejected', NULL, NULL, '2025-06-03 01:54:31', '2025-06-03 02:25:21'),
(3, 2, 1, '2025-06-05', '2025-06-05', '08:00:00', '08:00:00', 'Gd Vokasi', 'berdiskusi sebelum maju menuju seminar', 'approved', NULL, NULL, '2025-06-03 02:16:38', '2025-06-03 02:24:56'),
(4, 2, 1, '2025-06-04', '2025-06-03', '10:00:00', '05:00:00', 'GD512', '908132ijaewnkqiwp1392', 'rescheduled', NULL, NULL, '2025-06-03 05:57:05', '2025-06-03 07:19:49'),
(5, 3, 1, '2025-06-05', '2025-06-05', '10:00:00', '10:00:00', 'Gd Vokasi', '312iasduip321908adsisadjhlsadmn', 'approved', NULL, NULL, '2025-06-03 06:50:10', '2025-06-03 06:50:56'),
(6, 2, 1, '2025-06-04', '2025-06-03', '10:00:00', '18:00:00', 'Gd Vokasi', '123123adsadsqweeqw123123qwsdads', 'rescheduled', NULL, 'sdfsdffdse323232fds', '2025-06-03 07:23:16', '2025-06-03 07:24:40'),
(7, 2, 1, '2025-06-04', NULL, '10:00:00', NULL, 'vokasi lt 1', 'asdsadok123mk qwkoqewadsads', 'pending', NULL, NULL, '2025-06-03 07:26:04', '2025-06-03 07:26:04');

-- --------------------------------------------------------

--
-- Table structure for table `request_judul`
--

CREATE TABLE `request_judul` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mahasiswa_id` bigint(20) UNSIGNED NOT NULL,
  `dosen_tujuan_id` bigint(20) UNSIGNED NOT NULL,
  `judul_diajukan` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `catatan_dosen` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `request_judul`
--

INSERT INTO `request_judul` (`id`, `mahasiswa_id`, `dosen_tujuan_id`, `judul_diajukan`, `deskripsi`, `status`, `catatan_dosen`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Rancang bangun website Del Cafee', 'Merancang aplikasi berbasis website untuk membantu mempermudah pemesanan oleh mahasiswa di area del', 'approved', 'Ajukan bimbingan anda', '2025-06-03 01:47:34', '2025-06-03 01:49:27'),
(2, 2, 1, 'sistem informasi proyek akhir', 'memanajemen proyek akhir mahasiswa vokasi', 'approved', NULL, '2025-06-03 02:00:17', '2025-06-03 02:11:53'),
(3, 3, 1, 'sistem informasi proyek akhir', '123456789ijhgfdewq234er5tyui', 'approved', NULL, '2025-06-03 05:44:00', '2025-06-03 06:17:56'),
(4, 5, 1, 'rancang bangun website desa taon marisi', 'asdmkadsmkasdmadsklamksadkm', 'rejected', NULL, '2025-06-03 08:11:01', '2025-06-03 08:12:41'),
(5, 5, 1, 'sistem informasi proyek akhir', 'adsewq1233312123123123123', 'approved', NULL, '2025-06-03 08:11:16', '2025-06-03 08:12:50'),
(6, 5, 1, 'mfjndsfnfdsmnfdsmnkfdsjmnk', 'njsfdmdsfkdsfjkdsfjkmndsfmnkj', 'rejected', 'sadadsadsqweeqwqewsad', '2025-06-03 08:11:33', '2025-06-03 08:12:22');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('NI5aRWAyxzXhVXTkboTJJXQiJRHuEESO3SLizQsF', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZEpPVnRzRTlPM2V0NzVwQXQ1NUs1U0piQ1Q2ZGdLUnlPRE9IdXl4MCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fX0=', 1748964705),
('rxSbLoM24WpBjLkJgsUFhFdQuIYq2kY6HaUZjWcM', 3, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/137.0.0.0 Safari/537.36 Edg/137.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicFBwak9vSkJ4NVcxWDE5cnJsTjg0NmdNbzlLVHVBWFZMYjNleVRURiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjM7fQ==', 1748960781);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','dosen','admin') NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin User', 'admin', 'admin@example.com', '2025-06-03 01:45:13', '$2y$12$MlIUY.2dqXTYXVDszaKHZ.3VR9Nc7H0CKuw3Yo1AdJWYtAO/tBXn.', 'admin', NULL, '2025-06-03 01:45:13', '2025-06-03 01:45:13', NULL),
(2, 'oktova samosir', 'oktova samosir', 'oktova@gmail.com', NULL, '$2y$12$69eBdWuhala3CB03csVz/O7m7SqcQgDwlQejQdGLVjb6EujrCfmby', 'mahasiswa', NULL, '2025-06-03 01:45:58', '2025-06-03 01:45:58', NULL),
(3, 'Hernawati Susanti Samosir, SST., M.Kom.', 'Herna Samosir', 'herna@gmail.com', NULL, '$2y$12$vUnjgDYvjvtqNWeU1KIkSumLIFFlVRBaGJ226/fZ/a1DKCWtAxMgi', 'dosen', NULL, '2025-06-03 01:46:25', '2025-06-03 01:46:25', NULL),
(4, 'ADITIA XAVERIUS ARAPENTA TARIGAN', 'Aditia Tarigan', 'aditia@gmail.com', NULL, '$2y$12$RkJUcuMloV1mzLR6vkfEVu9KN9hgVljfNxsoqJlLiFwGxiAe2qkQy', 'mahasiswa', NULL, '2025-06-03 01:59:37', '2025-06-03 01:59:37', NULL),
(5, 'johannes', 'johannes', 'johannes@gmail.com', NULL, '$2y$12$GXjywQUWMMEXWHQ2hNb/2uVilOlfx/yGQsUjM2LSwSv63HVCTQlpO', 'dosen', NULL, '2025-06-03 05:22:50', '2025-06-03 05:22:57', '2025-06-03 05:22:57'),
(6, 'joy aruan', 'joy aruan', 'joyaruan@gmail.com', NULL, '$2y$12$0uFNJiKEWcsxpzJGdsEm4eQzLKepNzoLiAHyiuChp402DLDYJ/E6m', 'dosen', NULL, '2025-06-03 05:25:32', '2025-06-03 05:25:39', '2025-06-03 05:25:39'),
(7, 'Frans Adriano Sihombing', 'Frans Adriano Sihombing', 'frans1@gmail.com', NULL, '$2y$12$//YiyyGDj54Alm5ml4GC3.FGVdvivTqJ8S1V93/Xu/i3fuivUuTDS', 'mahasiswa', NULL, '2025-06-03 05:27:01', '2025-06-03 05:27:01', NULL),
(8, 'johannes', 'johannes1', 'johannes1@gmail.com', NULL, '$2y$12$kympXg9mcBLw0ZweGfrNWOw5neECQcVWkmJOT/dTKer6toG2p3kMq', 'mahasiswa', NULL, '2025-06-03 07:41:03', '2025-06-03 07:41:03', NULL),
(9, 'Gabe Akasia Simanjuntak', 'Gabe Simanjuntak', 'gabe@gmail.com', NULL, '$2y$12$vO6cVEOw2Cs1wOSeEbEgZOCYgyboEAPkz1ujF4oqjrnb.N05l3kg2', 'mahasiswa', NULL, '2025-06-03 07:42:13', '2025-06-03 07:42:13', NULL),
(10, 'Greis Lumbantoruan', 'Greis Lumbantoruan', 'greis@gmail.com', NULL, '$2y$12$4NdhB6JOLF/HLBN4V34r3.ZnXWSqaSHcw3VHuscOggV6TRurO36Yu', 'mahasiswa', NULL, '2025-06-03 07:44:52', '2025-06-03 08:23:30', '2025-06-03 08:23:30'),
(11, 'brahmana', 'brahmana', 'brahmana@gmail.com', NULL, '$2y$12$BD82Bit9SFR1iaZsZPAHg.4JGSjVgqhXIJTsav/dn7w/oVn2Jxryq', 'mahasiswa', NULL, '2025-06-03 07:57:50', '2025-06-03 07:57:50', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `dokumen_proyek_akhir`
--
ALTER TABLE `dokumen_proyek_akhir`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dokumen_proyek_akhir_mahasiswa_id_foreign` (`mahasiswa_id`),
  ADD KEY `dokumen_proyek_akhir_jenis_dokumen_id_foreign` (`jenis_dokumen_id`),
  ADD KEY `dokumen_proyek_akhir_reviewed_by_foreign` (`reviewed_by`);

--
-- Indexes for table `dosen`
--
ALTER TABLE `dosen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dosen_nidn_unique` (`nidn`),
  ADD KEY `dosen_user_id_foreign` (`user_id`),
  ADD KEY `dosen_prodi_id_foreign` (`prodi_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `history_bimbingan`
--
ALTER TABLE `history_bimbingan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `history_bimbingan_mahasiswa_id_foreign` (`mahasiswa_id`),
  ADD KEY `history_bimbingan_dosen_id_foreign` (`dosen_id`),
  ADD KEY `history_bimbingan_request_bimbingan_id_foreign` (`request_bimbingan_id`);

--
-- Indexes for table `jenis_dokumen`
--
ALTER TABLE `jenis_dokumen`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `jenis_dokumen_nama_jenis_unique` (`nama_jenis`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mahasiswa_nim_unique` (`nim`),
  ADD KEY `mahasiswa_user_id_foreign` (`user_id`),
  ADD KEY `mahasiswa_prodi_id_foreign` (`prodi_id`),
  ADD KEY `mahasiswa_dosen_pembimbing_id_foreign` (`dosen_pembimbing_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `prodi`
--
ALTER TABLE `prodi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `prodi_kode_prodi_unique` (`kode_prodi`);

--
-- Indexes for table `request_bimbingan`
--
ALTER TABLE `request_bimbingan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_bimbingan_mahasiswa_id_foreign` (`mahasiswa_id`),
  ADD KEY `request_bimbingan_dosen_id_foreign` (`dosen_id`);

--
-- Indexes for table `request_judul`
--
ALTER TABLE `request_judul`
  ADD PRIMARY KEY (`id`),
  ADD KEY `request_judul_mahasiswa_id_foreign` (`mahasiswa_id`),
  ADD KEY `request_judul_dosen_tujuan_id_foreign` (`dosen_tujuan_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dokumen_proyek_akhir`
--
ALTER TABLE `dokumen_proyek_akhir`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dosen`
--
ALTER TABLE `dosen`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `history_bimbingan`
--
ALTER TABLE `history_bimbingan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `jenis_dokumen`
--
ALTER TABLE `jenis_dokumen`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prodi`
--
ALTER TABLE `prodi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `request_bimbingan`
--
ALTER TABLE `request_bimbingan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `request_judul`
--
ALTER TABLE `request_judul`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dokumen_proyek_akhir`
--
ALTER TABLE `dokumen_proyek_akhir`
  ADD CONSTRAINT `dokumen_proyek_akhir_jenis_dokumen_id_foreign` FOREIGN KEY (`jenis_dokumen_id`) REFERENCES `jenis_dokumen` (`id`),
  ADD CONSTRAINT `dokumen_proyek_akhir_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dokumen_proyek_akhir_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `dosen`
--
ALTER TABLE `dosen`
  ADD CONSTRAINT `dosen_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`),
  ADD CONSTRAINT `dosen_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `history_bimbingan`
--
ALTER TABLE `history_bimbingan`
  ADD CONSTRAINT `history_bimbingan_dosen_id_foreign` FOREIGN KEY (`dosen_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `history_bimbingan_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `history_bimbingan_request_bimbingan_id_foreign` FOREIGN KEY (`request_bimbingan_id`) REFERENCES `request_bimbingan` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `mahasiswa_dosen_pembimbing_id_foreign` FOREIGN KEY (`dosen_pembimbing_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `mahasiswa_prodi_id_foreign` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`id`),
  ADD CONSTRAINT `mahasiswa_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `request_bimbingan`
--
ALTER TABLE `request_bimbingan`
  ADD CONSTRAINT `request_bimbingan_dosen_id_foreign` FOREIGN KEY (`dosen_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_bimbingan_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `request_judul`
--
ALTER TABLE `request_judul`
  ADD CONSTRAINT `request_judul_dosen_tujuan_id_foreign` FOREIGN KEY (`dosen_tujuan_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `request_judul_mahasiswa_id_foreign` FOREIGN KEY (`mahasiswa_id`) REFERENCES `mahasiswa` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
