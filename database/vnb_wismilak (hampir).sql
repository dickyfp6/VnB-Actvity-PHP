-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Apr 2026 pada 19.14
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vnb_wismilak`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `actor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action_type` varchar(100) NOT NULL,
  `target_type` varchar(100) NOT NULL,
  `target_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `description` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_number` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `date_joined` date NOT NULL,
  `induction_date` date DEFAULT NULL,
  `company` varchar(100) NOT NULL,
  `division_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `position_id` bigint(20) UNSIGNED DEFAULT NULL,
  `placement` varchar(100) DEFAULT NULL,
  `level` varchar(50) DEFAULT NULL,
  `employee_status` varchar(50) NOT NULL DEFAULT 'PKWTT',
  `email` varchar(255) NOT NULL,
  `whatsapp` varchar(20) DEFAULT NULL,
  `manager_functional_id` bigint(20) UNSIGNED DEFAULT NULL,
  `manager_operational_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vnb_period_start` date DEFAULT NULL,
  `vnb_period_end` date DEFAULT NULL,
  `vnb_status` enum('not_started','active','completed','canceled') NOT NULL DEFAULT 'not_started',
  `employment_state` varchar(20) NOT NULL DEFAULT 'active',
  `status_changed_at` timestamp NULL DEFAULT NULL,
  `status_change_reason` text DEFAULT NULL,
  `status_changed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `employees`
--

INSERT INTO `employees` (`id`, `employee_number`, `name`, `date_joined`, `induction_date`, `company`, `division_id`, `department_id`, `position_id`, `placement`, `level`, `employee_status`, `email`, `whatsapp`, `manager_functional_id`, `manager_operational_id`, `vnb_period_start`, `vnb_period_end`, `vnb_status`, `employment_state`, `status_changed_at`, `status_change_reason`, `status_changed_by`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '5026221011', 'Employee', '2026-04-01', '2026-04-07', 'PT Gawih Djaja', 5, 4, 2, 'Bengkulu', 'Staff/Supervisor', 'OS', 'employee@vnb.local', '082123456789', 1, NULL, NULL, NULL, 'active', 'active', NULL, NULL, NULL, NULL, '2026-04-07 16:00:59', '2026-04-07 16:00:59', NULL),
(2, '5026221078', 'Ahnaf Fathan', '2026-04-01', '2026-04-07', 'PT Gelora Djaja', 6, 12, 5, 'Bandung', 'Staff/Supervisor', 'PKWTT', 'ahnaf@vnb.id', '081234567890', 2, NULL, NULL, NULL, 'active', 'active', NULL, NULL, NULL, NULL, '2026-04-07 16:00:59', '2026-04-07 16:00:59', NULL),
(3, '5026221063', 'Regina Dwi', '2026-04-01', '2026-04-07', 'PT Wismilak Inti Makmur, Tbk', 6, 5, 3, 'Banjarmasin', 'Manager', 'PKWTT', 'rere@vnb.id', '082123456788', 1, 2, NULL, NULL, 'active', 'active', NULL, NULL, NULL, NULL, '2026-04-07 16:00:59', '2026-04-07 16:00:59', NULL),
(4, '5026221073', 'Silfia Mei', '2026-04-07', '2026-04-08', 'PT Wismilak Inti Makmur, Tbk', 7, 8, 7, 'Bengkulu', 'Non-Staff', 'PKWTT', 'silfi@vnb.id', '8999880980', 1, 2, '2026-04-08', '2027-04-07', 'not_started', 'active', NULL, NULL, NULL, NULL, '2026-04-07 16:31:46', '2026-04-07 16:33:53', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `imports`
--

CREATE TABLE `imports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `imported_by` bigint(20) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `total_rows` int(11) NOT NULL,
  `success_rows` int(11) NOT NULL DEFAULT 0,
  `error_rows` int(11) NOT NULL DEFAULT 0,
  `summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`summary`)),
  `status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `import_rows`
--

CREATE TABLE `import_rows` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `import_id` bigint(20) UNSIGNED NOT NULL,
  `row_number` int(11) NOT NULL,
  `raw_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`raw_data`)),
  `status` enum('success','skipped','error','duplicate') NOT NULL DEFAULT 'success',
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `managers`
--

CREATE TABLE `managers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `employee_number` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `division` varchar(100) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `managers`
--

INSERT INTO `managers` (`id`, `name`, `email`, `employee_number`, `company`, `division`, `status`, `user_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Manager', 'manager@vnb.local', '5026221022', 'PT Gawih Djaja', 'Information and Technology', 'active', 11, '2026-04-07 16:00:59', '2026-04-07 16:00:59', NULL),
(2, 'Dicky Febri Primadhani', 'dicky@vnb.id', '5026221036', 'PT Wismilak Inti Makmur, Tbk', 'Information and Technology', 'active', 13, '2026-04-07 16:00:59', '2026-04-07 16:00:59', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_companies`
--

CREATE TABLE `master_companies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_companies`
--

INSERT INTO `master_companies` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'PT Wismilak Inti Makmur, Tbk', '2026-04-07 02:53:45', '2026-04-07 02:53:45', NULL),
(2, 'PT Gelora Djaja', '2026-04-07 02:53:45', '2026-04-07 02:53:45', NULL),
(3, 'PT Gawih Djaja', '2026-04-07 02:53:45', '2026-04-07 02:53:45', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_departments`
--

CREATE TABLE `master_departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_departments`
--

INSERT INTO `master_departments` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 'Accounting', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(5, 'Application Development', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(6, 'Brand Group', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(7, 'C&B and HRIS', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(8, 'Civil Engineering', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(9, 'Engangement', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(10, 'Engineering', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(11, 'Factory Laboratory', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(12, 'Finance', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(13, 'General FAT', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(14, 'General HRD', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(15, 'General Marketing', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(16, 'General SFM', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(17, 'Health', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(18, 'Internal Audit', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(19, 'Legal & Permit', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(20, 'Market Research', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(21, 'Marketing Controller', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(22, 'POD', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(23, 'PPIC', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(24, 'Primary', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(25, 'Production', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(26, 'Public Relations', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(27, 'Quality Control', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(28, 'Research and Development', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(29, 'Regional Marketing II', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(30, 'Regional Marketing III', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(31, 'Regional Marketing IV', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(32, 'Regional Marketing V', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(33, 'Regional Marketing VI', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(34, 'Regional Sales I', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(35, 'Regional Sales II', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(36, 'Regional Sales III', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(37, 'Regional Sales IV', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(38, 'Regional Sales V', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(39, 'Regional Sales VI', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(40, 'SAP', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL),
(41, 'Secondary SKM', '2026-04-07 02:54:55', '2026-04-07 02:54:55', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_divisions`
--

CREATE TABLE `master_divisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_divisions`
--

INSERT INTO `master_divisions` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(4, 'Factory', '2026-04-07 02:54:24', '2026-04-07 02:54:24', NULL),
(5, 'Finance, Accounting, and Tax', '2026-04-07 02:54:24', '2026-04-07 02:54:24', NULL),
(6, 'Human Resource', '2026-04-07 02:54:24', '2026-04-07 02:54:24', NULL),
(7, 'Information and Technology', '2026-04-07 02:54:24', '2026-04-07 02:54:24', NULL),
(8, 'Marketing', '2026-04-07 02:54:24', '2026-04-07 02:54:24', NULL),
(9, 'Sales Field Marketing', '2026-04-07 02:54:24', '2026-04-07 02:54:24', NULL),
(10, 'Support', '2026-04-07 02:54:24', '2026-04-07 02:54:24', NULL),
(11, 'WIM - Filter', '2026-04-07 02:54:24', '2026-04-07 02:54:24', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_employee_statuses`
--

CREATE TABLE `master_employee_statuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_employee_statuses`
--

INSERT INTO `master_employee_statuses` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'PKWTT', '2026-04-07 08:58:38', '2026-04-07 08:58:38', NULL),
(2, 'PKWT', '2026-04-07 08:58:38', '2026-04-07 08:58:38', NULL),
(3, 'OS', '2026-04-07 08:58:38', '2026-04-07 08:58:38', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_levels`
--

CREATE TABLE `master_levels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_levels`
--

INSERT INTO `master_levels` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Staff/Supervisor', '2026-04-07 08:58:38', '2026-04-07 08:58:38', NULL),
(2, 'Non-Staff', '2026-04-07 08:58:38', '2026-04-07 08:58:38', NULL),
(3, 'Manager', '2026-04-07 08:58:38', '2026-04-07 08:58:38', NULL),
(4, 'Harian', '2026-04-07 08:58:38', '2026-04-07 08:58:38', NULL),
(5, 'Mingguan', '2026-04-07 08:58:38', '2026-04-07 08:58:38', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_placements`
--

CREATE TABLE `master_placements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_placements`
--

INSERT INTO `master_placements` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(75, 'Bandung', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(76, 'Banjarmasin', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(77, 'Bengkulu', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(78, 'Bogor', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(79, 'Buntaran 9', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(80, 'Cirebon', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(81, 'Head Office', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(82, 'Head Office (Online)', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(83, 'Jakarta (1)', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(84, 'Jakarta (2)', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(85, 'Jakarta Representative Office', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(86, 'Jember', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(87, 'Jombang', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(88, 'Kediri', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(89, 'Malang', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(90, 'Medan', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(91, 'Padangsidimpuan', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(92, 'Pamekasan', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(93, 'Pati', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(94, 'Pematangsiantar', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(95, 'Purwokerto', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(96, 'Semarang', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(97, 'Solo', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(98, 'Stockpoint Jambi (Bengkulu)', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(99, 'Surabaya', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(100, 'Tangerang', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(101, 'Tegal', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(102, 'WIM Factory B18', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(103, 'WIM Head Office', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL),
(104, 'Yogyakarta', '2026-04-07 03:51:03', '2026-04-07 03:51:03', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_positions`
--

CREATE TABLE `master_positions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `master_positions`
--

INSERT INTO `master_positions` (`id`, `name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'ABAPER', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(2, 'Admin POSM', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(3, 'Area Finance Administrator', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(4, 'Area Marketing Administrator', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(5, 'Area Marketing Manager', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(6, 'Area Marketing Supervisor', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(7, 'Area Sales Supervisor', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(8, 'Area Warehouse Head', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(9, 'Associate Brand Manager', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(10, 'AWH Helper', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(11, 'Brand Manager', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(12, 'C&B Specialist', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(13, 'Central Technical Store Coordinator', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(14, 'Conversion Kit, Tools & Change Parts Specialist', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(15, 'Data Processing Staff', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(16, 'Designer', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(17, 'Digital Activation Specialist', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(18, 'Driver', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(19, 'Engagement Administrator', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(20, 'External Relation Liaison', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(21, 'Factory Audit Staff', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(22, 'General QA & Documentation Officer', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(23, 'Health Performance Analyst', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(24, 'HO Administration Supervisor (HOAS)', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(25, 'HR Strategy & Analytics', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(26, 'HRIS Specialist', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(27, 'Junior Cigarette Designer', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(28, 'Junior Electrician', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(29, 'Junior Mechanic', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(30, 'Learning & Development Supervisor', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(31, 'Machine Operator', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(32, 'Marketing & SFM Audit Staff', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(33, 'Marketing Alpha Program', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(34, 'Marketing Field Controller Staff', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(35, 'Material Development Specialist', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(36, 'Merchandiser', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(37, 'Middle Mechanic', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(38, 'Modern Trade Manager', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(39, 'Organic Research Analyst', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(40, 'Permit Specialist', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(41, 'Preventive Maintenance Electrician', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(42, 'Preventive Maintenance Mechanic', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(43, 'Primary Engineering Manager', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(44, 'Primary Production Junior Supervisor', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(45, 'Product Development Supervisor', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(46, 'Production Administrator', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(47, 'Promotor', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(48, 'Public Relation Officer', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(49, 'QC Supervisor', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(50, 'Quantity Project Surveyor', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(51, 'Reference Material Specialist', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(52, 'Regional Administration Manager VI', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(53, 'Regional Sales Manager VI', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(54, 'Retail Salesman', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(55, 'Sales Coordinator', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(56, 'Sales Field Marketing Information Officer', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(57, 'Sales Modern Special Trade', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(58, 'SE Plan Estimator & Quantity Surveyor', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(59, 'Sensory Specialist', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(60, 'SKM Middle Mechanic Making', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(61, 'SKM Middle Mechanic Packing', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(62, 'SKM Preventive Maintenance & Project Supervisor', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(63, 'SKM Senior Mechanic Making', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(64, 'SM & FG (Outsourcing)', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(65, 'SMST', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(66, 'Stamp Manager', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(67, 'Stamp Procurement & Payment Officer', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(68, 'Support & FAT Audit Staff', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(69, 'Support & FAT Audit Supervisor', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(70, 'Training Officer', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(71, 'Wholesale Salesman', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(72, 'WIM Finance Officer', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(73, 'WIM General Accounting Officer', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL),
(74, 'WSP Officer', '2026-04-07 03:51:20', '2026-04-07 03:51:20', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2024_01_01_000000_create_master_data_tables', 1),
(3, '2024_01_02_000000_create_employees_table', 1),
(4, '2024_01_02_100000_create_users_table', 1),
(5, '2024_01_03_000000_create_vnb_planning_tables', 1),
(6, '2024_01_04_000000_create_vnb_evidence_tables', 1),
(7, '2024_01_05_000000_create_notifications_and_imports_tables', 1),
(8, '2024_01_06_000000_create_audit_and_cancellation_tables', 1),
(9, '2024_01_07_000000_create_vnb_revisions_tables', 1),
(10, '2026_03_05_140108_create_cache_table', 1),
(11, '2026_03_05_140109_create_permission_tables', 1),
(12, '2026_03_13_000001_create_managers_table', 1),
(13, '2026_03_13_000002_create_extended_master_tables', 1),
(14, '2026_03_13_000003_create_vnb_framework_table', 1),
(15, '2026_03_13_000004_add_activity_fields_to_vnb_plan_items', 1),
(16, '2026_03_18_000005_simplify_master_data_tables', 1),
(17, '2026_03_18_170000_fix_employees_master_foreign_keys', 1),
(18, '2026_03_25_120000_add_lifecycle_fields_to_employees_table', 1),
(19, '2026_03_25_120000_update_employee_status_to_employment_type', 1),
(20, '2026_03_25_130000_add_master_employee_statuses_table', 1),
(21, '2026_03_25_140000_seed_default_master_levels_for_career_stage', 1),
(22, '2026_03_25_160500_add_temp_password_fields_to_users_table', 1),
(23, '2026_03_27_000001_add_employee_number_to_managers_table', 1),
(24, '2026_03_27_235900_create_vnb_plan_revisions_table', 1),
(25, '2026_04_06_153000_update_vnb_plan_revisions_columns', 1),
(26, '2026_04_06_160000_add_revision_requested_status_to_vnb_plans', 1),
(27, '2026_04_07_000001_restructure_vnb_plan_items_table', 2),
(28, '2026_04_07_154921_add_integration_columns_to_vnb_plan_items_table', 2),
(29, '2026_04_07_155000_remove_single_phase_framework_items', 3),
(30, '2024_01_03_100000_add_framework_item_to_vnb_plan_items', 4),
(31, '2026_04_07_000001_add_missing_fields_to_vnb_plan_items', 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 6),
(2, 'App\\Models\\User', 10),
(2, 'App\\Models\\User', 12),
(2, 'App\\Models\\User', 14),
(2, 'App\\Models\\User', 15),
(2, 'App\\Models\\User', 16),
(3, 'App\\Models\\User', 7),
(3, 'App\\Models\\User', 11),
(3, 'App\\Models\\User', 13),
(4, 'App\\Models\\User', 8),
(5, 'App\\Models\\User', 9);

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(100) NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `recipient_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `channel` varchar(50) NOT NULL COMMENT 'email, whatsapp, in-app',
  `status` enum('pending','sent','delivered','failed','bounced') NOT NULL DEFAULT 'pending',
  `error_message` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `sent_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `notification_logs`
--

CREATE TABLE `notification_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notification_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(50) NOT NULL,
  `details` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`details`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'create_employee', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(2, 'view_employee', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(3, 'edit_employee', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(4, 'delete_employee', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(5, 'import_employees', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(6, 'cancel_vnb', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(7, 'create_planning', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(8, 'edit_planning', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(9, 'submit_planning', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(10, 'view_planning', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(11, 'approve_planning', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(12, 'reject_planning', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(13, 'upload_evidence', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(14, 'view_evidence', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(15, 'verify_evidence', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(16, 'reject_evidence', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(17, 'view_dashboard', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(18, 'view_reports', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(19, 'export_reports', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(20, 'manage_master_data', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(21, 'view_settings', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(22, 'manage_users', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(2, 'employee', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(3, 'manager', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(4, 'pcx_manager', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28'),
(5, 'intercomm', 'web', '2026-04-07 02:36:28', '2026-04-07 02:36:28');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(1, 4),
(2, 1),
(2, 3),
(2, 4),
(2, 5),
(3, 1),
(3, 4),
(4, 1),
(4, 4),
(5, 1),
(5, 4),
(6, 1),
(6, 4),
(7, 1),
(7, 2),
(8, 1),
(8, 2),
(9, 1),
(9, 2),
(10, 1),
(10, 2),
(10, 3),
(10, 4),
(10, 5),
(11, 1),
(11, 3),
(12, 1),
(12, 3),
(13, 1),
(13, 2),
(14, 1),
(14, 2),
(14, 3),
(14, 4),
(14, 5),
(15, 1),
(15, 3),
(16, 1),
(16, 3),
(17, 1),
(17, 2),
(17, 3),
(17, 4),
(17, 5),
(18, 1),
(18, 3),
(18, 4),
(18, 5),
(19, 1),
(19, 4),
(20, 1),
(20, 4),
(21, 1),
(22, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `temp_password_encrypted` text DEFAULT NULL,
  `temp_password_generated_at` timestamp NULL DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `temp_password_encrypted`, `temp_password_generated_at`, `phone`, `avatar`, `status`, `employee_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(6, 'Admin User', 'admin@vnb.local', '2026-04-07 09:04:01', '$2y$12$GeY/.4/ad2U0c./SMbvfwuXeGfON1V4fldLsMZy7OcNP6mC9LyIXK', NULL, NULL, NULL, NULL, 'active', NULL, NULL, '2026-04-07 09:03:00', '2026-04-07 09:04:01'),
(8, 'PCX Manager', 'pcx@vnb.local', '2026-04-07 09:04:02', '$2y$12$kUs9CvCf2UudHUB8cLdNu.uQ8yKyrJRoa7oqtdVx3bSNpdw7bDJEK', NULL, NULL, NULL, NULL, 'active', NULL, NULL, '2026-04-07 09:03:01', '2026-04-07 09:04:02'),
(9, 'Intercomm User', 'intercomm@vnb.local', '2026-04-07 09:04:02', '$2y$12$WVDMO.ikD7iLIw0mT8uPJei.ZFwhiruglv6KQs.DKH.brcPh0i0aK', NULL, NULL, NULL, NULL, 'active', NULL, NULL, '2026-04-07 09:03:01', '2026-04-07 09:04:02'),
(11, 'Manager', 'manager@vnb.local', NULL, '$2y$12$dB5wLaWoLZs/dKPuTsgirenumQlaX6iHx11MJe0ZSnYg.fv9alip.', 'eyJpdiI6Ikg3bkNRWTl6a2hGMGQwYWViT3lLTWc9PSIsInZhbHVlIjoiMEFSTVNENXBMbXZtZVY1TmtMelMyQT09IiwibWFjIjoiZmE4YzBlZDJhZTJlZGEyZTQ4ZWNjMmUwZTc2ZmVhN2ZjYTljOTVhNDFmNjM4N2QxMjdmNWM2MjU5Yjk3ODhjNiIsInRhZyI6IiJ9', '2026-04-07 09:39:49', NULL, NULL, 'active', NULL, NULL, '2026-04-07 09:39:49', '2026-04-07 15:26:06'),
(12, 'Employee', 'employee@vnb.local', NULL, '$2y$12$nNsVbvuPopVciFABSrI6POTqwGoG5Cn4I5pVdUM3T/4z1gn9FNeU2', 'eyJpdiI6IklLZjFBbC9TL3VMUnp4TkVkWCttdHc9PSIsInZhbHVlIjoibmx0STE3Nzl5dkYzQXd2bWpidDlRZz09IiwibWFjIjoiZGE2NzUxMWMwZmNjOWU2MDQ0NjY0Nzc0YjJmODg5OTJmNTQ3YzZjNDJmOTc5N2ZiN2EzNGMzMDcxMjYzNjdkYiIsInRhZyI6IiJ9', '2026-04-07 09:40:31', '082123456789', NULL, 'active', 1, NULL, '2026-04-07 09:40:31', '2026-04-07 09:56:38'),
(13, 'Dicky Febri Primadhani', 'dicky@vnb.id', NULL, '$2y$12$XospnSVzGAK5Mv7VsHXeJexT78CH6aGWifNL4Rq7CEqhtVvFjTUIm', 'eyJpdiI6IjVmWG1hL3lRWVY0NWY5YkdxMWNQTkE9PSIsInZhbHVlIjoiRDB5N2pKL2RybzlUWGpSakxmd1FhUT09IiwibWFjIjoiOGQ2NDNkNmVjZWQxNzIyODMyMTZhNjhhNDczZWVhM2ZjOTQ3ZDhkZDFhZWJjYzk5ZmFiMjBmZTgwMDczODZlMCIsInRhZyI6IiJ9', '2026-04-07 09:46:35', NULL, NULL, 'active', NULL, NULL, '2026-04-07 09:46:35', '2026-04-07 09:46:35'),
(14, 'Ahnaf Fathan', 'fathan@vnb.id', NULL, '$2y$12$Zdp7TWDotEMT2Pc9TfQPXeuYTjbD8KsCiQfQy8ysu8tmrUMBqZriC', 'eyJpdiI6IlViWHBDeEp4NllzTHdLbHhpb1ZLdFE9PSIsInZhbHVlIjoiQ3lLa2FSSHBQU1NRYTFuMW9pM3JFdz09IiwibWFjIjoiNzE0Y2IyYWMwYmYxYWIzNzhiOTg2MWMzNWViNmE4NDU5OGNmNTQ1MmU4OWMyYThlZDllYTc2OTVlNDdlZDU3MyIsInRhZyI6IiJ9', '2026-04-07 09:49:34', '081234567890', NULL, 'active', 2, NULL, '2026-04-07 09:49:34', '2026-04-07 09:49:34'),
(15, 'Regina Dwi', 'rere@vnb.id', NULL, '$2y$12$wBfJRW4ceHS0ELn9H6h7Xuw6uBxSg0.NDqqn5oYc1PX6VFlgCFw7O', 'eyJpdiI6Ing3elloMDlJMWt1bS9OS2Q3N3dZTEE9PSIsInZhbHVlIjoiOGNVNzZoR1k4akczQ2ZjUVRUZlhqdz09IiwibWFjIjoiNWQzNmRkMTA1YzBiMTk0MGJkODVlNzFiODQ1Y2QxMGFiZWQ1NjA2ZDIzNmU1M2I2ZmE3ZmVkMjY0OGUxYjdiNiIsInRhZyI6IiJ9', '2026-04-07 09:49:55', '082123456788', NULL, 'active', 3, NULL, '2026-04-07 09:49:55', '2026-04-07 09:49:55'),
(16, 'Silfia Mei', 'silfi@vnb.id', NULL, '$2y$12$A8YPSa9ja3qkOYX5qq.t7eGU/LmEJO7F1ax2JP1ddH54saJt4A4OC', 'eyJpdiI6Inh5eWJHazJiQWl0cWFoZkdNcEhxZlE9PSIsInZhbHVlIjoiVk5FdzF4UkJNclJXN1R2N2RMWk16Zz09IiwibWFjIjoiNzIxMmVmYzRlYWQ4Y2RlODBjOGFjMzBhZjk1ZjBjYTA3NTAyZDQ2NDAxMjE5MWVkMjQxNmVlMTM4ZTA1OWZlZiIsInRhZyI6IiJ9', '2026-04-07 16:31:46', '8999880980', NULL, 'active', 4, NULL, '2026-04-07 16:31:46', '2026-04-07 16:33:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `vnb_cancellations`
--

CREATE TABLE `vnb_cancellations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `reason` enum('budaya_kerja','tidak_cocok_vnb','others') NOT NULL COMMENT 'Culture, Not Suitable, Others',
  `notes` text NOT NULL,
  `canceled_by` bigint(20) UNSIGNED NOT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approval_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approval_notes` text DEFAULT NULL,
  `canceled_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `vnb_evidences`
--

CREATE TABLE `vnb_evidences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `plan_item_id` bigint(20) UNSIGNED NOT NULL,
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_type` varchar(50) NOT NULL,
  `file_size` int(11) NOT NULL,
  `s3_url` varchar(255) DEFAULT NULL COMMENT 'Supabase Storage URL',
  `description` text DEFAULT NULL,
  `status` enum('pending_verification','verified','rejected') NOT NULL DEFAULT 'pending_verification',
  `verification_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `vnb_framework_items`
--

CREATE TABLE `vnb_framework_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `career_stage` varchar(50) NOT NULL COMMENT 'manage_self_non_staff | manage_self_staff | manage_others | manage_manager | manage_function',
  `behaviour` varchar(100) NOT NULL COMMENT 'Empathy | Be A Wismilak Ambassador | Effective & Efficient | Speak with Data | Collaborative | Decisive | Open Mind',
  `phase` varchar(20) NOT NULL COMMENT '1-3 | 4-6 | 6+',
  `integration_1` text DEFAULT NULL,
  `integration_2` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `vnb_framework_items`
--

INSERT INTO `vnb_framework_items` (`id`, `career_stage`, `behaviour`, `phase`, `integration_1`, `integration_2`, `created_at`, `updated_at`) VALUES
(1, 'manage_self_non_staff', 'Empathy', '1-3', 'Memberikan apresiasi tertulis kepada rekan-rekan kerjanya', 'Bertanya kepada rekan satu tim tentang tantangan atau kesulitan yang mereka hadapi, memberikan saran atau membantu mereka sesuai dengan wewenang dan tanggung jawab', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(2, 'manage_self_non_staff', 'Empathy', '4-6', 'Inisiatif Menawarkan bantuan pada rekan kerja yang membutuhkan bantuan sesuai dengan ranahnya', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(3, 'manage_self_non_staff', 'Empathy', '6+', 'Membantu mengenalkan cara kerja ke rekan baru', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(4, 'manage_self_non_staff', 'Be A Wismilak Ambassador', '1-3', 'Mempelajari sejarah atau filosofi perusahaan, lalu membagikan 1 hal yang paling menginspirasi', 'Mengunggah atau membagikan konten positif tentang kegiatan perusahaan di media sosial internal/perusahaan (jika diperbolehkan)', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(5, 'manage_self_non_staff', 'Be A Wismilak Ambassador', '4-6', 'Mengikuti aktivitas-aktivitas Wismilak', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(6, 'manage_self_non_staff', 'Be A Wismilak Ambassador', '6+', 'Menjadi contoh kedisiplinan', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(7, 'manage_self_non_staff', 'Effective & Efficient', '1-3', 'Mengetahui ranah pekerjaannya sesuai job desc dan visi misi perusahaan', 'Mengikuti pelatihan penggunaan alat kerja/SOP yang benar dan membuat laporan singkat pemahaman', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(8, 'manage_self_non_staff', 'Effective & Efficient', '4-6', 'Disiplin Bekerja sesuai dengan target perusahaan (waktu dan materi)', 'Mengusulkan ide-ide (>1) untuk menyederhanakan pekerjaan harian atau sistem kerja & standarisasi', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(9, 'manage_self_non_staff', 'Effective & Efficient', '6+', 'Menghindari melakukan kesalahan berulang. Dan dapat Mengingatkan rekan jika kerja tidak sesuai SOP', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(10, 'manage_self_non_staff', 'Speak with Data', '1-3', 'Melaporkan hasil kerja sesuai jumlah nyata', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(11, 'manage_self_non_staff', 'Speak with Data', '4-6', 'Mengakui kesalahan tanpa ditutup-tutupi', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(12, 'manage_self_non_staff', 'Speak with Data', '6+', 'Menyampaikan jika hasil turun & cari penyebab', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(13, 'manage_self_non_staff', 'Collaborative', '1-3', 'Mengetahui peran tim yang terlibat dalam pekerja hariannya', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(14, 'manage_self_non_staff', 'Collaborative', '4-6', 'Saling membantu / back up rekan kerja ketika dibutuhkan untuk mencapai target perusahaan', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(15, 'manage_self_non_staff', 'Collaborative', '6+', 'Menjaga suasana kerja kondusif (Hal apa saja yang dapat dilakukan untuk mencapai hal ini)', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(16, 'manage_self_non_staff', 'Decisive', '1-3', 'Langsung melapor jika ada kendala---Mengajukan solusi untuk penyelesaian masalah dalam pekerjaan harian, kemudian konsultasikan kepada atasan sebelum dilakukan', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(17, 'manage_self_non_staff', 'Decisive', '4-6', 'Tidak membiarkan kesalahan berulang --- Menyampaikan kepada atasan peluang atau masalah (loophole) yang dapat berdampak ke departemen, divisi, atau perusahaan', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(18, 'manage_self_non_staff', 'Decisive', '6+', 'Memberi saran sederhana jika lihat potensi masalah', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(19, 'manage_self_non_staff', 'Open Mind', '1-3', 'Meminta dan memberikan 1 feedback dari atasan, rekan kerja, atau team dan menerapkannya.', 'Menyampaikan hambatan yang dialami kepada Atasan dan menyusun Action Plan untuk mengatasinya', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(20, 'manage_self_non_staff', 'Open Mind', '4-6', 'Mengetahui kelebihan dan kekurangan yang dimiliki selama bekerja, dan dipersilahkan mengajukan pelatihan apabila diperlukan', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(21, 'manage_self_non_staff', 'Open Mind', '6+', 'Berani menyampaikan ide unik/kreatif (tanpa takut salah) kepada atasan', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(22, 'manage_self_staff', 'Empathy', '1-3', 'Memberikan apresiasi tertulis kepada rekan-rekan kerjanya', 'Bertanya kepada rekan satu tim tentang tantangan atau kesulitan yang mereka hadapi, memberikan saran atau membantu mereka sesuai dengan wewenang dan tanggung jawab', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(23, 'manage_self_staff', 'Empathy', '4-6', 'Menginisiasi diskusi dengan rekan berbeda divisi / departemen (klien internal) atau klien eksternal untuk memahami tantangan dan membantu mereka sesuai dengan wewenang dan tanggung jawab', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(24, 'manage_self_staff', 'Empathy', '6+', 'Karyawan ditugaskan menjadi \"buddy\" untuk rekan baru atau rekan satu tim dalam project tertentu', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(25, 'manage_self_staff', 'Be A Wismilak Ambassador', '1-3', 'Mempelajari sejarah atau filosofi perusahaan, lalu membagikan 1 hal yang paling menginspirasi', 'Mengunggah atau membagikan konten positif tentang kegiatan perusahaan di media sosial internal/perusahaan (jika diperbolehkan)', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(26, 'manage_self_staff', 'Be A Wismilak Ambassador', '4-6', 'Mengikuti aktivitas-aktivitas Wismilak', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(27, 'manage_self_staff', 'Be A Wismilak Ambassador', '6+', 'Menjadi panitia, pelaksana, atau koordinator dalam minimal 1 event perusahaan', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(28, 'manage_self_staff', 'Effective & Efficient', '1-3', 'Mengisi template \"Visi & Misi Alignment Map\": mencocokkan pekerjaan harian dengan kontribusinya terhadap visi & misi perusahaan', 'Mengikuti pelatihan penggunaan alat kerja / SOP yang benar dan membuat laporan ringkas pemahaman', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(29, 'manage_self_staff', 'Effective & Efficient', '4-6', 'Membuat pertimbangan cost vs benefit perusahaan dari pekerjaan yang dilakukan', 'Mengusulkan ide-ide (>1) untuk menyederhanakan pekerjaan harian atau sistem kerja & standarisasi', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(30, 'manage_self_staff', 'Effective & Efficient', '6+', 'Melatih 1 rekan kerja tentang tips kerja efisien yang sudah ter-standard', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(31, 'manage_self_staff', 'Speak with Data', '1-3', 'Belajar membaca atau memahami minimal 1 jenis data dasar yang digunakan di divisinya (misalnya: output, volume, target)', 'Mencatat minimal 1 hal yang bisa diukur dari pekerjaannya dan melacak progresnya', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(32, 'manage_self_staff', 'Speak with Data', '4-6', 'Menyusun target kerja (Specific, Measurable, Achievable, Reachable, Time bound) berdasarkan data yang dikumpulkan', 'Menyusun dan presentasi laporan berbasis data dari pekerjaan yang dilakukan', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(33, 'manage_self_staff', 'Speak with Data', '6+', 'Memberikan usulan pada proses kerja berdasarkan data yang dipresentasikan dan menunjukkan progress.', 'Studi kasus: membedakan mana data atau informasi milik Divisi dan Perusahaan yang boleh dan tidak boleh disebarluaskan', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(34, 'manage_self_staff', 'Collaborative', '1-3', 'Identifikasi impact pekerjaan karyawan pada bagian, departemen atau divisi lain (user) untuk mengenal peran mereka dan sebaliknya', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(35, 'manage_self_staff', 'Collaborative', '4-6', 'Ikut serta dalam 1 kegiatan atau project tim lintas fungsi', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(36, 'manage_self_staff', 'Collaborative', '6+', 'Mengusulkan dan menjadi koordinator kolaborasi baru yang bisa menguntungkan dua tim/departemen/divisi', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(37, 'manage_self_staff', 'Decisive', '1-3', 'Mengajukan solusi untuk penyelesaian masalah dalam pekerjaan harian, kemudian konsultasikan kepada atasan sebelum dilakukan', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(38, 'manage_self_staff', 'Decisive', '4-6', 'Menyampaikan kepada atasan peluang atau masalah (loophole) yang dapat berdampak ke departemen, divisi, atau perusahaan', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(39, 'manage_self_staff', 'Decisive', '6+', 'Memberi usulan strategis kepada atasan yang berdampak pada sistem/proses ke depan dan lanjutkan dengan PDCA (Plan , Do, Check, Action)', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(40, 'manage_self_staff', 'Open Mind', '1-3', 'Meminta dan memberikan 1 feedback dari atasan, rekan kerja, atau team dan menerapkannya.', 'Menyampaikan hambatan yang dialami kepada Atasan dan menyusun Action Plan untuk mengatasinya', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(41, 'manage_self_staff', 'Open Mind', '4-6', 'Membuat list Strength, Area for Development, dan mengikuti pelatihan yang sesuai', 'Menjadi fasilitator atau moderator dalam sesi diskusi untuk integrasi berbagai ide', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(42, 'manage_self_staff', 'Open Mind', '6+', 'Menyampaikan ide unik/kreatif (tanpa takut salah) kepada atasan, lalu menilai bersama potensi pengembangannya.', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(43, 'manage_others', 'Be A Wismilak Ambassador', '1-3', 'Mempelajari sejarah atau filosofi perusahaan, lalu membagikan 1 hal yang paling menginspirasi', 'Mengunggah atau membagikan konten positif tentang kegiatan perusahaan di media sosial internal/perusahaan (jika diperbolehkan)', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(44, 'manage_others', 'Be A Wismilak Ambassador', '4-6', 'Mengikuti aktivitas-aktivitas Wismilak', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(45, 'manage_others', 'Be A Wismilak Ambassador', '6+', 'Menjadi panitia, pelaksana, atau koordinator dalam minimal 1 event perusahaan', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(46, 'manage_others', 'Collaborative', '1-3', 'Identifikasi impact pekerjaan karyawan pada bagian, departemen atau divisi lain (user) untuk mengenal peran mereka dan sebaliknya', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(47, 'manage_others', 'Collaborative', '4-6', 'Ikut serta dalam 1 kegiatan atau project tim lintas fungsi', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(48, 'manage_others', 'Collaborative', '6+', 'Mengusulkan dan menjadi koordinator kolaborasi baru yang bisa menguntungkan dua tim/departemen/divisi', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(49, 'manage_others', 'Decisive', '1-3', 'Mengajukan solusi untuk penyelesaian masalah dalam pekerjaan harian, kemudian konsultasikan kepada atasan sebelum dilakukan', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(50, 'manage_others', 'Decisive', '4-6', 'Menyampaikan kepada atasan peluang atau masalah (loophole) yang dapat berdampak ke departemen, divisi, atau perusahaan', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(51, 'manage_others', 'Decisive', '6+', 'Memberi usulan strategis kepada atasan yang berdampak pada sistem/proses ke depan dan lanjutkan dengan PDCA (Plan , Do, Check, Action)', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(52, 'manage_others', 'Effective & Efficient', '1-3', 'Mengisi template \"Visi & Misi Alignment Map\": mencocokkan pekerjaan harian dengan kontribusinya terhadap visi & misi perusahaan', 'Mengikuti pelatihan penggunaan alat kerja / SOP yang benar dan membuat laporan ringkas pemahaman', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(53, 'manage_others', 'Effective & Efficient', '4-6', 'Membuat pertimbangan cost vs benefit perusahaan dari pekerjaan yang dilakukan', 'Mengusulkan ide-ide (>1) untuk menyederhanakan pekerjaan harian atau sistem kerja & standarisasi', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(54, 'manage_others', 'Effective & Efficient', '6+', 'Melatih 1 rekan kerja tentang tips kerja efisien yang sudah ter-standard', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(55, 'manage_others', 'Empathy', '1-3', 'Memberikan apresiasi tertulis kepada rekan-rekan kerjanya', 'Bertanya kepada rekan satu tim tentang tantangan atau kesulitan yang mereka hadapi, memberikan saran atau membantu mereka sesuai dengan wewenang dan tanggung jawab', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(56, 'manage_others', 'Empathy', '4-6', 'Menginisiasi diskusi dengan rekan berbeda divisi / departemen (klien internal) atau klien eksternal untuk memahami tantangan dan membantu mereka sesuai dengan wewenang dan tanggung jawab', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(57, 'manage_others', 'Empathy', '6+', 'Karyawan ditugaskan menjadi \"buddy\" untuk rekan baru atau rekan satu tim dalam project tertentu', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(58, 'manage_others', 'Open Mind', '1-3', 'Meminta dan memberikan 1 feedback dari atasan, rekan kerja, atau team dan menerapkannya.', 'Menyampaikan hambatan yang dialami kepada Atasan dan menyusun Action Plan untuk mengatasinya', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(59, 'manage_others', 'Open Mind', '4-6', 'Membuat list Strength, Area for Development, dan mengikuti pelatihan yang sesuai', 'Menjadi fasilitator atau moderator dalam sesi diskusi untuk integrasi berbagai ide', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(60, 'manage_others', 'Open Mind', '6+', 'Menyampaikan ide unik/kreatif (tanpa takut salah) kepada atasan, lalu menilai bersama potensi pengembangannya.', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(61, 'manage_others', 'Speak with Data', '1-3', 'Belajar membaca atau memahami minimal 1 jenis data dasar yang digunakan di divisinya (misalnya: output, volume, target)', 'Mencatat minimal 1 hal yang bisa diukur dari pekerjaannya dan melacak progresnya', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(62, 'manage_others', 'Speak with Data', '4-6', 'Menyusun target kerja (Specific, Measurable, Achievable, Reachable, Time bound) berdasarkan data yang dikumpulkan', 'Menyusun dan presentasi laporan berbasis data dari pekerjaan yang dilakukan', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(63, 'manage_others', 'Speak with Data', '6+', 'Memberikan usulan pada proses kerja berdasarkan data yang dipresentasikan dan menunjukkan progress.', 'Studi kasus: membedakan mana data atau informasi milik Divisi dan Perusahaan yang boleh dan tidak boleh disebarluaskan', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(64, 'manage_managers', 'Be A Wismilak Ambassador', '1-3', 'Mempelajari sejarah atau filosofi perusahaan, lalu membagikan 1 hal yang paling menginspirasi', 'Mengunggah atau membagikan konten positif tentang kegiatan perusahaan di media sosial internal/perusahaan (jika diperbolehkan)', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(65, 'manage_managers', 'Be A Wismilak Ambassador', '4-6', 'Mengikuti aktivitas-aktivitas Wismilak', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(66, 'manage_managers', 'Be A Wismilak Ambassador', '6+', 'Menjadi panitia, pelaksana, atau koordinator dalam minimal 1 event perusahaan', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(67, 'manage_managers', 'Collaborative', '1-3', 'Identifikasi impact pekerjaan karyawan pada bagian, departemen atau divisi lain (user) untuk mengenal peran mereka dan sebaliknya', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(68, 'manage_managers', 'Collaborative', '4-6', 'Ikut serta dalam 1 kegiatan atau project tim lintas fungsi', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(69, 'manage_managers', 'Collaborative', '6+', 'Mengusulkan dan menjadi koordinator kolaborasi baru yang bisa menguntungkan dua tim/departemen/divisi', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(70, 'manage_managers', 'Decisive', '1-3', 'Mengajukan solusi untuk penyelesaian masalah dalam pekerjaan harian, kemudian konsultasikan kepada atasan sebelum dilakukan', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(71, 'manage_managers', 'Decisive', '4-6', 'Menyampaikan kepada atasan peluang atau masalah (loophole) yang dapat berdampak ke departemen, divisi, atau perusahaan', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(72, 'manage_managers', 'Decisive', '6+', 'Memberi usulan strategis kepada atasan yang berdampak pada sistem/proses ke depan dan lanjutkan dengan PDCA (Plan , Do, Check, Action)', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(73, 'manage_managers', 'Effective & Efficient', '1-3', 'Mengisi template \"Visi & Misi Alignment Map\": mencocokkan pekerjaan harian dengan kontribusinya terhadap visi & misi perusahaan', 'Mengikuti pelatihan penggunaan alat kerja / SOP yang benar dan membuat laporan ringkas pemahaman', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(74, 'manage_managers', 'Effective & Efficient', '4-6', 'Membuat pertimbangan cost vs benefit perusahaan dari pekerjaan yang dilakukan', 'Mengusulkan ide-ide (>1) untuk menyederhanakan pekerjaan harian atau sistem kerja & standarisasi', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(75, 'manage_managers', 'Effective & Efficient', '6+', 'Melatih 1 rekan kerja tentang tips kerja efisien yang sudah ter-standard', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(76, 'manage_managers', 'Empathy', '1-3', 'Memberikan apresiasi tertulis kepada rekan-rekan kerjanya', 'Bertanya kepada rekan satu tim tentang tantangan atau kesulitan yang mereka hadapi, memberikan saran atau membantu mereka sesuai dengan wewenang dan tanggung jawab', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(77, 'manage_managers', 'Empathy', '4-6', 'Menginisiasi diskusi dengan rekan berbeda divisi / departemen (klien internal) atau klien eksternal untuk memahami tantangan dan membantu mereka sesuai dengan wewenang dan tanggung jawab', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(78, 'manage_managers', 'Empathy', '6+', 'Karyawan ditugaskan menjadi \"buddy\" untuk rekan baru atau rekan satu tim dalam project tertentu', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(79, 'manage_managers', 'Open Mind', '1-3', 'Meminta dan memberikan 1 feedback dari atasan, rekan kerja, atau team dan menerapkannya.', 'Menyampaikan hambatan yang dialami kepada Atasan dan menyusun Action Plan untuk mengatasinya', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(80, 'manage_managers', 'Open Mind', '4-6', 'Membuat list Strength, Area for Development, dan mengikuti pelatihan yang sesuai', 'Menjadi fasilitator atau moderator dalam sesi diskusi untuk integrasi berbagai ide', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(81, 'manage_managers', 'Open Mind', '6+', 'Menyampaikan ide unik/kreatif (tanpa takut salah) kepada atasan, lalu menilai bersama potensi pengembangannya.', NULL, '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(82, 'manage_managers', 'Speak with Data', '1-3', 'Belajar membaca atau memahami minimal 1 jenis data dasar yang digunakan di divisinya (misalnya: output, volume, target)', 'Mencatat minimal 1 hal yang bisa diukur dari pekerjaannya dan melacak progresnya', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(83, 'manage_managers', 'Speak with Data', '4-6', 'Menyusun target kerja (Specific, Measurable, Achievable, Reachable, Time bound) berdasarkan data yang dikumpulkan', 'Menyusun dan presentasi laporan berbasis data dari pekerjaan yang dilakukan', '2026-04-07 16:00:49', '2026-04-07 16:00:49'),
(84, 'manage_managers', 'Speak with Data', '6+', 'Memberikan usulan pada proses kerja berdasarkan data yang dipresentasikan dan menunjukkan progress.', 'Studi kasus: membedakan mana data atau informasi milik Divisi dan Perusahaan yang boleh dan tidak boleh disebarluaskan', '2026-04-07 16:00:49', '2026-04-07 16:00:49');

-- --------------------------------------------------------

--
-- Struktur dari tabel `vnb_periods`
--

CREATE TABLE `vnb_periods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `phase_number` tinyint(4) NOT NULL COMMENT '1, 2, 3',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `cutoff_date` date NOT NULL COMMENT '25th of month',
  `status` enum('not_started','in_progress','ready_for_presentation','submitted','completed','rejected') NOT NULL DEFAULT 'not_started',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `vnb_periods`
--

INSERT INTO `vnb_periods` (`id`, `employee_id`, `phase_number`, `start_date`, `end_date`, `cutoff_date`, `status`, `created_at`, `updated_at`) VALUES
(4, 1, 1, '2026-04-07', '2026-08-06', '2026-08-25', 'in_progress', '2026-04-07 09:48:50', '2026-04-07 09:48:50'),
(5, 1, 2, '2026-08-07', '2026-12-06', '2026-12-25', 'not_started', '2026-04-07 09:48:50', '2026-04-07 09:48:50'),
(6, 1, 3, '2026-12-07', '2027-04-06', '2027-04-25', 'not_started', '2026-04-07 09:48:50', '2026-04-07 09:48:50'),
(7, 2, 1, '2026-04-07', '2026-08-06', '2026-08-25', 'in_progress', '2026-04-07 09:49:34', '2026-04-07 09:49:34'),
(8, 2, 2, '2026-08-07', '2026-12-06', '2026-12-25', 'not_started', '2026-04-07 09:49:34', '2026-04-07 09:49:34'),
(9, 2, 3, '2026-12-07', '2027-04-06', '2027-04-25', 'not_started', '2026-04-07 09:49:34', '2026-04-07 09:49:34'),
(10, 3, 1, '2026-04-07', '2026-08-06', '2026-08-25', 'in_progress', '2026-04-07 09:49:55', '2026-04-07 09:49:55'),
(11, 3, 2, '2026-08-07', '2026-12-06', '2026-12-25', 'not_started', '2026-04-07 09:49:55', '2026-04-07 09:49:55'),
(12, 3, 3, '2026-12-07', '2027-04-06', '2027-04-25', 'not_started', '2026-04-07 09:49:55', '2026-04-07 09:49:55'),
(16, 4, 1, '2026-04-08', '2026-08-07', '2026-08-25', 'not_started', '2026-04-07 16:33:53', '2026-04-07 16:33:53'),
(17, 4, 2, '2026-08-08', '2026-12-07', '2026-12-25', 'not_started', '2026-04-07 16:33:53', '2026-04-07 16:33:53'),
(18, 4, 3, '2026-12-08', '2027-04-07', '2027-04-25', 'not_started', '2026-04-07 16:33:53', '2026-04-07 16:33:53');

-- --------------------------------------------------------

--
-- Struktur dari tabel `vnb_plans`
--

CREATE TABLE `vnb_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `period_id` bigint(20) UNSIGNED NOT NULL,
  `phase_number` tinyint(4) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `planning_mode` enum('adjust_all','custom') NOT NULL DEFAULT 'custom',
  `status` enum('draft','waiting_manager_approval','approved','rejected','in_progress','submitted','revision_requested') DEFAULT 'draft',
  `revision_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Total revisions requested',
  `revision_notes` text DEFAULT NULL COMMENT 'Current revision notes from manager',
  `submitted_at` datetime DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `discussion_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `vnb_plans`
--

INSERT INTO `vnb_plans` (`id`, `employee_id`, `period_id`, `phase_number`, `title`, `description`, `planning_mode`, `status`, `revision_count`, `revision_notes`, `submitted_at`, `approved_at`, `approved_by`, `rejection_reason`, `discussion_notes`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 1, 'Rencana VnB - Employee', 'Auto-generated dari framework manage_self_staff', 'adjust_all', 'waiting_manager_approval', 0, NULL, '2026-04-07 23:48:10', NULL, NULL, NULL, NULL, '2026-04-07 09:56:14', '2026-04-07 16:48:10'),
(2, 3, 10, 1, 'Rencana VnB - Regina Dwi', 'Auto-generated dari framework manage_managers', 'adjust_all', 'draft', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-07 15:37:23', '2026-04-07 15:37:23'),
(3, 2, 7, 1, 'Rencana VnB - Ahnaf Fathan', 'Auto-generated dari framework manage_self_staff', 'adjust_all', 'draft', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-07 15:38:17', '2026-04-07 15:38:17'),
(4, 4, 16, 1, 'Rencana VnB - Silfia Mei', 'Auto-generated dari framework manage_self_staff', 'adjust_all', 'draft', 0, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-07 16:35:52', '2026-04-07 16:35:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `vnb_plan_items`
--

CREATE TABLE `vnb_plan_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `vnb_framework_id` bigint(20) UNSIGNED DEFAULT NULL,
  `plan_id` bigint(20) UNSIGNED NOT NULL,
  `framework_item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `activity_title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `integration_1` text DEFAULT NULL COMMENT 'Integrasi pengukuran 1',
  `integration_2` text DEFAULT NULL COMMENT 'Integrasi pengukuran 2',
  `implementation_date` date DEFAULT NULL,
  `deliverables` text NOT NULL,
  `activity_description` text DEFAULT NULL COMMENT 'UC006: execution description filled by Employee',
  `activity_date` date DEFAULT NULL COMMENT 'UC006: actual date the activity was done',
  `submission_status` enum('draft','waiting_approval','revision_required','completed','overdue') NOT NULL DEFAULT 'draft',
  `revision_notes` text DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `approved_functional_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_functional_at` datetime DEFAULT NULL,
  `approved_operational_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_operational_at` datetime DEFAULT NULL,
  `due_date` date DEFAULT NULL COMMENT '25th of last month of phase, auto-calculated',
  `behavior_metrics` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'checklist items' CHECK (json_valid(`behavior_metrics`)),
  `status` enum('draft','submitted','approved','revision','completed','rejected') NOT NULL DEFAULT 'draft' COMMENT 'Workflow status: draft → submitted → approved/revision → completed/rejected',
  `completion_percentage` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `vnb_plan_items`
--

INSERT INTO `vnb_plan_items` (`id`, `employee_id`, `vnb_framework_id`, `plan_id`, `framework_item_id`, `activity_title`, `description`, `integration_1`, `integration_2`, `implementation_date`, `deliverables`, `activity_description`, `activity_date`, `submission_status`, `revision_notes`, `submitted_at`, `approved_functional_by`, `approved_functional_at`, `approved_operational_by`, `approved_operational_at`, `due_date`, `behavior_metrics`, `status`, `completion_percentage`, `created_at`, `updated_at`) VALUES
(190, 1, NULL, 1, 25, 'Be A Wismilak Ambassador - Phase 1-3', 'Mempelajari sejarah atau filosofi perusahaan, lalu membagikan 1 hal yang paling menginspirasi | Mengunggah atau membagikan konten positif tentang kegiatan perusahaan di media sosial internal/perusahaan (jika diperbolehkan)', 'Mempelajari sejarah atau filosofi perusahaan, lalu membagikan 1 hal yang paling menginspirasi', 'Mengunggah atau membagikan konten positif tentang kegiatan perusahaan di media sosial internal/perusahaan (jika diperbolehkan)', '2026-04-07', 'Mempelajari sejarah atau filosofi perusahaan, lalu membagikan 1 hal yang paling menginspirasi\n---\nMengunggah atau membagikan konten positif tentang kegiatan perusahaan di media sosial internal/perusahaan (jika diperbolehkan)', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(191, 1, NULL, 1, 34, 'Collaborative - Phase 1-3', 'Identifikasi impact pekerjaan karyawan pada bagian, departemen atau divisi lain (user) untuk mengenal peran mereka dan sebaliknya', 'Identifikasi impact pekerjaan karyawan pada bagian, departemen atau divisi lain (user) untuk mengenal peran mereka dan sebaliknya', NULL, '2026-04-07', 'Identifikasi impact pekerjaan karyawan pada bagian, departemen atau divisi lain (user) untuk mengenal peran mereka dan sebaliknya', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(192, 1, NULL, 1, 37, 'Decisive - Phase 1-3', 'Mengajukan solusi untuk penyelesaian masalah dalam pekerjaan harian, kemudian konsultasikan kepada atasan sebelum dilakukan', 'Mengajukan solusi untuk penyelesaian masalah dalam pekerjaan harian, kemudian konsultasikan kepada atasan sebelum dilakukan', NULL, '2026-04-07', 'Mengajukan solusi untuk penyelesaian masalah dalam pekerjaan harian, kemudian konsultasikan kepada atasan sebelum dilakukan', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(193, 1, NULL, 1, 28, 'Effective & Efficient - Phase 1-3', 'Mengisi template \"Visi & Misi Alignment Map\": mencocokkan pekerjaan harian dengan kontribusinya terhadap visi & misi perusahaan | Mengikuti pelatihan penggunaan alat kerja / SOP yang benar dan membuat laporan ringkas pemahaman', 'Mengisi template \"Visi & Misi Alignment Map\": mencocokkan pekerjaan harian dengan kontribusinya terhadap visi & misi perusahaan', 'Mengikuti pelatihan penggunaan alat kerja / SOP yang benar dan membuat laporan ringkas pemahaman', '2026-04-07', 'Mengisi template \"Visi & Misi Alignment Map\": mencocokkan pekerjaan harian dengan kontribusinya terhadap visi & misi perusahaan\n---\nMengikuti pelatihan penggunaan alat kerja / SOP yang benar dan membuat laporan ringkas pemahaman', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(194, 1, NULL, 1, 22, 'Empathy - Phase 1-3', 'Memberikan apresiasi tertulis kepada rekan-rekan kerjanya | Bertanya kepada rekan satu tim tentang tantangan atau kesulitan yang mereka hadapi, memberikan saran atau membantu mereka sesuai dengan wewenang dan tanggung jawab', 'Memberikan apresiasi tertulis kepada rekan-rekan kerjanya', 'Bertanya kepada rekan satu tim tentang tantangan atau kesulitan yang mereka hadapi, memberikan saran atau membantu mereka sesuai dengan wewenang dan tanggung jawab', '2026-04-07', 'apresiasi gokilll\n---\nAsk Bertanya, Pesanan Menjawab', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(195, 1, NULL, 1, 40, 'Open Mind - Phase 1-3', 'Meminta dan memberikan 1 feedback dari atasan, rekan kerja, atau team dan menerapkannya. | Menyampaikan hambatan yang dialami kepada Atasan dan menyusun Action Plan untuk mengatasinya', 'Meminta dan memberikan 1 feedback dari atasan, rekan kerja, atau team dan menerapkannya.', 'Menyampaikan hambatan yang dialami kepada Atasan dan menyusun Action Plan untuk mengatasinya', '2026-04-07', 'iya\n---\nMenyampaikan hambatan yang dialami kepada Atasan dan menyusun Action Plan untuk mengatasinya', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(196, 1, NULL, 1, 31, 'Speak with Data - Phase 1-3', 'Belajar membaca atau memahami minimal 1 jenis data dasar yang digunakan di divisinya (misalnya: output, volume, target) | Mencatat minimal 1 hal yang bisa diukur dari pekerjaannya dan melacak progresnya', 'Belajar membaca atau memahami minimal 1 jenis data dasar yang digunakan di divisinya (misalnya: output, volume, target)', 'Mencatat minimal 1 hal yang bisa diukur dari pekerjaannya dan melacak progresnya', '2026-04-07', 'Belajar membaca atau memahami minimal 1 jenis data dasar yang digunakan di divisinya (misalnya: output, volume, target)\n---\nMencatat minimal 1 hal yang bisa diukur dari pekerjaannya dan melacak progresnya', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(197, 1, NULL, 1, 26, 'Be A Wismilak Ambassador - Phase 4-6', 'Mengikuti aktivitas-aktivitas Wismilak', 'Mengikuti aktivitas-aktivitas Wismilak', NULL, '2026-04-07', 'oke', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(198, 1, NULL, 1, 35, 'Collaborative - Phase 4-6', 'Ikut serta dalam 1 kegiatan atau project tim lintas fungsi', 'Ikut serta dalam 1 kegiatan atau project tim lintas fungsi', NULL, '2026-04-07', 'collab', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(199, 1, NULL, 1, 38, 'Decisive - Phase 4-6', 'Menyampaikan kepada atasan peluang atau masalah (loophole) yang dapat berdampak ke departemen, divisi, atau perusahaan', 'Menyampaikan kepada atasan peluang atau masalah (loophole) yang dapat berdampak ke departemen, divisi, atau perusahaan', NULL, '2026-04-07', '>', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(200, 1, NULL, 1, 29, 'Effective & Efficient - Phase 4-6', 'Membuat pertimbangan cost vs benefit perusahaan dari pekerjaan yang dilakukan | Mengusulkan ide-ide (>1) untuk menyederhanakan pekerjaan harian atau sistem kerja & standarisasi', 'Membuat pertimbangan cost vs benefit perusahaan dari pekerjaan yang dilakukan', 'Mengusulkan ide-ide (>1) untuk menyederhanakan pekerjaan harian atau sistem kerja & standarisasi', '2026-04-07', '?\n---\nide2 aja sih', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(201, 1, NULL, 1, 23, 'Empathy - Phase 4-6', 'Menginisiasi diskusi dengan rekan berbeda divisi / departemen (klien internal) atau klien eksternal untuk memahami tantangan dan membantu mereka sesuai dengan wewenang dan tanggung jawab', 'Menginisiasi diskusi dengan rekan berbeda divisi / departemen (klien internal) atau klien eksternal untuk memahami tantangan dan membantu mereka sesuai dengan wewenang dan tanggung jawab', NULL, '2026-04-07', 'haha', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(202, 1, NULL, 1, 41, 'Open Mind - Phase 4-6', 'Membuat list Strength, Area for Development, dan mengikuti pelatihan yang sesuai | Menjadi fasilitator atau moderator dalam sesi diskusi untuk integrasi berbagai ide', 'Membuat list Strength, Area for Development, dan mengikuti pelatihan yang sesuai', 'Menjadi fasilitator atau moderator dalam sesi diskusi untuk integrasi berbagai ide', '2026-04-07', 'hehe\n---\nalright', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(203, 1, NULL, 1, 32, 'Speak with Data - Phase 4-6', 'Menyusun target kerja (Specific, Measurable, Achievable, Reachable, Time bound) berdasarkan data yang dikumpulkan | Menyusun dan presentasi laporan berbasis data dari pekerjaan yang dilakukan', 'Menyusun target kerja (Specific, Measurable, Achievable, Reachable, Time bound) berdasarkan data yang dikumpulkan', 'Menyusun dan presentasi laporan berbasis data dari pekerjaan yang dilakukan', '2026-04-07', 'spok\n---\nMenyusun dan presentasi laporan berbasis data dari pekerjaan yang dilakukan', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(204, 1, NULL, 1, 27, 'Be A Wismilak Ambassador - Phase 6+', 'Menjadi panitia, pelaksana, atau koordinator dalam minimal 1 event perusahaan', 'Menjadi panitia, pelaksana, atau koordinator dalam minimal 1 event perusahaan', NULL, '2026-04-07', 'Menjadi panitia, pelaksana, atau koordinator dalam minimal 1 event perusahaan', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(205, 1, NULL, 1, 36, 'Collaborative - Phase 6+', 'Mengusulkan dan menjadi koordinator kolaborasi baru yang bisa menguntungkan dua tim/departemen/divisi', 'Mengusulkan dan menjadi koordinator kolaborasi baru yang bisa menguntungkan dua tim/departemen/divisi', NULL, '2026-04-07', 'Mengusulkan dan menjadi koordinator kolaborasi baru yang bisa menguntungkan dua tim/departemen/divisi', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(206, 1, NULL, 1, 39, 'Decisive - Phase 6+', 'Memberi usulan strategis kepada atasan yang berdampak pada sistem/proses ke depan dan lanjutkan dengan PDCA (Plan , Do, Check, Action)', 'Memberi usulan strategis kepada atasan yang berdampak pada sistem/proses ke depan dan lanjutkan dengan PDCA (Plan , Do, Check, Action)', NULL, '2026-04-07', 'Melatih 1 rekan kerja tentang tips kerja efisien yang sudah ter-standard', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(207, 1, NULL, 1, 30, 'Effective & Efficient - Phase 6+', 'Melatih 1 rekan kerja tentang tips kerja efisien yang sudah ter-standard', 'Melatih 1 rekan kerja tentang tips kerja efisien yang sudah ter-standard', NULL, '2026-04-07', 'Melatih 1 rekan kerja tentang tips kerja efisien yang sudah ter-standard', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(208, 1, NULL, 1, 24, 'Empathy - Phase 6+', 'Karyawan ditugaskan menjadi \"buddy\" untuk rekan baru atau rekan satu tim dalam project tertentu', 'Karyawan ditugaskan menjadi \"buddy\" untuk rekan baru atau rekan satu tim dalam project tertentu', NULL, '2026-04-07', 'Konsul kayanya dik', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(209, 1, NULL, 1, 42, 'Open Mind - Phase 6+', 'Menyampaikan ide unik/kreatif (tanpa takut salah) kepada atasan, lalu menilai bersama potensi pengembangannya.', 'Menyampaikan ide unik/kreatif (tanpa takut salah) kepada atasan, lalu menilai bersama potensi pengembangannya.', NULL, '2026-04-07', 'Menyampaikan ide unik/kreatif (tanpa takut salah) kepada atasan, lalu menilai bersama potensi pengembangannya.', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(210, 1, NULL, 1, 33, 'Speak with Data - Phase 6+', 'Memberikan usulan pada proses kerja berdasarkan data yang dipresentasikan dan menunjukkan progress. | Studi kasus: membedakan mana data atau informasi milik Divisi dan Perusahaan yang boleh dan tidak boleh disebarluaskan', 'Memberikan usulan pada proses kerja berdasarkan data yang dipresentasikan dan menunjukkan progress.', 'Studi kasus: membedakan mana data atau informasi milik Divisi dan Perusahaan yang boleh dan tidak boleh disebarluaskan', '2026-04-07', 'Memberikan usulan pada proses kerja berdasarkan data yang dipresentasikan dan menunjukkan progress.\n---\nStudi kasus: membedakan mana data atau informasi milik Divisi dan Perusahaan yang boleh dan tidak boleh disebarluaskan', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:48:06'),
(211, 3, NULL, 2, 64, 'Be A Wismilak Ambassador - Phase 1-3', 'Mempelajari sejarah atau filosofi perusahaan, lalu membagikan 1 hal yang paling menginspirasi | Mengunggah atau membagikan konten positif tentang kegiatan perusahaan di media sosial internal/perusahaan (jika diperbolehkan)', 'Mempelajari sejarah atau filosofi perusahaan, lalu membagikan 1 hal yang paling menginspirasi', 'Mengunggah atau membagikan konten positif tentang kegiatan perusahaan di media sosial internal/perusahaan (jika diperbolehkan)', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(212, 3, NULL, 2, 67, 'Collaborative - Phase 1-3', 'Identifikasi impact pekerjaan karyawan pada bagian, departemen atau divisi lain (user) untuk mengenal peran mereka dan sebaliknya', 'Identifikasi impact pekerjaan karyawan pada bagian, departemen atau divisi lain (user) untuk mengenal peran mereka dan sebaliknya', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(213, 3, NULL, 2, 70, 'Decisive - Phase 1-3', 'Mengajukan solusi untuk penyelesaian masalah dalam pekerjaan harian, kemudian konsultasikan kepada atasan sebelum dilakukan', 'Mengajukan solusi untuk penyelesaian masalah dalam pekerjaan harian, kemudian konsultasikan kepada atasan sebelum dilakukan', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(214, 3, NULL, 2, 73, 'Effective & Efficient - Phase 1-3', 'Mengisi template \"Visi & Misi Alignment Map\": mencocokkan pekerjaan harian dengan kontribusinya terhadap visi & misi perusahaan | Mengikuti pelatihan penggunaan alat kerja / SOP yang benar dan membuat laporan ringkas pemahaman', 'Mengisi template \"Visi & Misi Alignment Map\": mencocokkan pekerjaan harian dengan kontribusinya terhadap visi & misi perusahaan', 'Mengikuti pelatihan penggunaan alat kerja / SOP yang benar dan membuat laporan ringkas pemahaman', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(215, 3, NULL, 2, 76, 'Empathy - Phase 1-3', 'Memberikan apresiasi tertulis kepada rekan-rekan kerjanya | Bertanya kepada rekan satu tim tentang tantangan atau kesulitan yang mereka hadapi, memberikan saran atau membantu mereka sesuai dengan wewenang dan tanggung jawab', 'Memberikan apresiasi tertulis kepada rekan-rekan kerjanya', 'Bertanya kepada rekan satu tim tentang tantangan atau kesulitan yang mereka hadapi, memberikan saran atau membantu mereka sesuai dengan wewenang dan tanggung jawab', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(216, 3, NULL, 2, 79, 'Open Mind - Phase 1-3', 'Meminta dan memberikan 1 feedback dari atasan, rekan kerja, atau team dan menerapkannya. | Menyampaikan hambatan yang dialami kepada Atasan dan menyusun Action Plan untuk mengatasinya', 'Meminta dan memberikan 1 feedback dari atasan, rekan kerja, atau team dan menerapkannya.', 'Menyampaikan hambatan yang dialami kepada Atasan dan menyusun Action Plan untuk mengatasinya', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(217, 3, NULL, 2, 82, 'Speak with Data - Phase 1-3', 'Belajar membaca atau memahami minimal 1 jenis data dasar yang digunakan di divisinya (misalnya: output, volume, target) | Mencatat minimal 1 hal yang bisa diukur dari pekerjaannya dan melacak progresnya', 'Belajar membaca atau memahami minimal 1 jenis data dasar yang digunakan di divisinya (misalnya: output, volume, target)', 'Mencatat minimal 1 hal yang bisa diukur dari pekerjaannya dan melacak progresnya', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(218, 3, NULL, 2, 65, 'Be A Wismilak Ambassador - Phase 4-6', 'Mengikuti aktivitas-aktivitas Wismilak', 'Mengikuti aktivitas-aktivitas Wismilak', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(219, 3, NULL, 2, 68, 'Collaborative - Phase 4-6', 'Ikut serta dalam 1 kegiatan atau project tim lintas fungsi', 'Ikut serta dalam 1 kegiatan atau project tim lintas fungsi', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(220, 3, NULL, 2, 71, 'Decisive - Phase 4-6', 'Menyampaikan kepada atasan peluang atau masalah (loophole) yang dapat berdampak ke departemen, divisi, atau perusahaan', 'Menyampaikan kepada atasan peluang atau masalah (loophole) yang dapat berdampak ke departemen, divisi, atau perusahaan', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(221, 3, NULL, 2, 74, 'Effective & Efficient - Phase 4-6', 'Membuat pertimbangan cost vs benefit perusahaan dari pekerjaan yang dilakukan | Mengusulkan ide-ide (>1) untuk menyederhanakan pekerjaan harian atau sistem kerja & standarisasi', 'Membuat pertimbangan cost vs benefit perusahaan dari pekerjaan yang dilakukan', 'Mengusulkan ide-ide (>1) untuk menyederhanakan pekerjaan harian atau sistem kerja & standarisasi', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(222, 3, NULL, 2, 77, 'Empathy - Phase 4-6', 'Menginisiasi diskusi dengan rekan berbeda divisi / departemen (klien internal) atau klien eksternal untuk memahami tantangan dan membantu mereka sesuai dengan wewenang dan tanggung jawab', 'Menginisiasi diskusi dengan rekan berbeda divisi / departemen (klien internal) atau klien eksternal untuk memahami tantangan dan membantu mereka sesuai dengan wewenang dan tanggung jawab', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(223, 3, NULL, 2, 80, 'Open Mind - Phase 4-6', 'Membuat list Strength, Area for Development, dan mengikuti pelatihan yang sesuai | Menjadi fasilitator atau moderator dalam sesi diskusi untuk integrasi berbagai ide', 'Membuat list Strength, Area for Development, dan mengikuti pelatihan yang sesuai', 'Menjadi fasilitator atau moderator dalam sesi diskusi untuk integrasi berbagai ide', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(224, 3, NULL, 2, 83, 'Speak with Data - Phase 4-6', 'Menyusun target kerja (Specific, Measurable, Achievable, Reachable, Time bound) berdasarkan data yang dikumpulkan | Menyusun dan presentasi laporan berbasis data dari pekerjaan yang dilakukan', 'Menyusun target kerja (Specific, Measurable, Achievable, Reachable, Time bound) berdasarkan data yang dikumpulkan', 'Menyusun dan presentasi laporan berbasis data dari pekerjaan yang dilakukan', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(225, 3, NULL, 2, 66, 'Be A Wismilak Ambassador - Phase 6+', 'Menjadi panitia, pelaksana, atau koordinator dalam minimal 1 event perusahaan', 'Menjadi panitia, pelaksana, atau koordinator dalam minimal 1 event perusahaan', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(226, 3, NULL, 2, 69, 'Collaborative - Phase 6+', 'Mengusulkan dan menjadi koordinator kolaborasi baru yang bisa menguntungkan dua tim/departemen/divisi', 'Mengusulkan dan menjadi koordinator kolaborasi baru yang bisa menguntungkan dua tim/departemen/divisi', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(227, 3, NULL, 2, 72, 'Decisive - Phase 6+', 'Memberi usulan strategis kepada atasan yang berdampak pada sistem/proses ke depan dan lanjutkan dengan PDCA (Plan , Do, Check, Action)', 'Memberi usulan strategis kepada atasan yang berdampak pada sistem/proses ke depan dan lanjutkan dengan PDCA (Plan , Do, Check, Action)', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(228, 3, NULL, 2, 75, 'Effective & Efficient - Phase 6+', 'Melatih 1 rekan kerja tentang tips kerja efisien yang sudah ter-standard', 'Melatih 1 rekan kerja tentang tips kerja efisien yang sudah ter-standard', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(229, 3, NULL, 2, 78, 'Empathy - Phase 6+', 'Karyawan ditugaskan menjadi \"buddy\" untuk rekan baru atau rekan satu tim dalam project tertentu', 'Karyawan ditugaskan menjadi \"buddy\" untuk rekan baru atau rekan satu tim dalam project tertentu', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(230, 3, NULL, 2, 81, 'Open Mind - Phase 6+', 'Menyampaikan ide unik/kreatif (tanpa takut salah) kepada atasan, lalu menilai bersama potensi pengembangannya.', 'Menyampaikan ide unik/kreatif (tanpa takut salah) kepada atasan, lalu menilai bersama potensi pengembangannya.', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(231, 3, NULL, 2, 84, 'Speak with Data - Phase 6+', 'Memberikan usulan pada proses kerja berdasarkan data yang dipresentasikan dan menunjukkan progress. | Studi kasus: membedakan mana data atau informasi milik Divisi dan Perusahaan yang boleh dan tidak boleh disebarluaskan', 'Memberikan usulan pada proses kerja berdasarkan data yang dipresentasikan dan menunjukkan progress.', 'Studi kasus: membedakan mana data atau informasi milik Divisi dan Perusahaan yang boleh dan tidak boleh disebarluaskan', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(232, 2, NULL, 3, 25, 'Be A Wismilak Ambassador - Phase 1-3', 'Mempelajari sejarah atau filosofi perusahaan, lalu membagikan 1 hal yang paling menginspirasi | Mengunggah atau membagikan konten positif tentang kegiatan perusahaan di media sosial internal/perusahaan (jika diperbolehkan)', 'Mempelajari sejarah atau filosofi perusahaan, lalu membagikan 1 hal yang paling menginspirasi', 'Mengunggah atau membagikan konten positif tentang kegiatan perusahaan di media sosial internal/perusahaan (jika diperbolehkan)', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(233, 2, NULL, 3, 34, 'Collaborative - Phase 1-3', 'Identifikasi impact pekerjaan karyawan pada bagian, departemen atau divisi lain (user) untuk mengenal peran mereka dan sebaliknya', 'Identifikasi impact pekerjaan karyawan pada bagian, departemen atau divisi lain (user) untuk mengenal peran mereka dan sebaliknya', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(234, 2, NULL, 3, 37, 'Decisive - Phase 1-3', 'Mengajukan solusi untuk penyelesaian masalah dalam pekerjaan harian, kemudian konsultasikan kepada atasan sebelum dilakukan', 'Mengajukan solusi untuk penyelesaian masalah dalam pekerjaan harian, kemudian konsultasikan kepada atasan sebelum dilakukan', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(235, 2, NULL, 3, 28, 'Effective & Efficient - Phase 1-3', 'Mengisi template \"Visi & Misi Alignment Map\": mencocokkan pekerjaan harian dengan kontribusinya terhadap visi & misi perusahaan | Mengikuti pelatihan penggunaan alat kerja / SOP yang benar dan membuat laporan ringkas pemahaman', 'Mengisi template \"Visi & Misi Alignment Map\": mencocokkan pekerjaan harian dengan kontribusinya terhadap visi & misi perusahaan', 'Mengikuti pelatihan penggunaan alat kerja / SOP yang benar dan membuat laporan ringkas pemahaman', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(236, 2, NULL, 3, 22, 'Empathy - Phase 1-3', 'Memberikan apresiasi tertulis kepada rekan-rekan kerjanya | Bertanya kepada rekan satu tim tentang tantangan atau kesulitan yang mereka hadapi, memberikan saran atau membantu mereka sesuai dengan wewenang dan tanggung jawab', 'Memberikan apresiasi tertulis kepada rekan-rekan kerjanya', 'Bertanya kepada rekan satu tim tentang tantangan atau kesulitan yang mereka hadapi, memberikan saran atau membantu mereka sesuai dengan wewenang dan tanggung jawab', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(237, 2, NULL, 3, 40, 'Open Mind - Phase 1-3', 'Meminta dan memberikan 1 feedback dari atasan, rekan kerja, atau team dan menerapkannya. | Menyampaikan hambatan yang dialami kepada Atasan dan menyusun Action Plan untuk mengatasinya', 'Meminta dan memberikan 1 feedback dari atasan, rekan kerja, atau team dan menerapkannya.', 'Menyampaikan hambatan yang dialami kepada Atasan dan menyusun Action Plan untuk mengatasinya', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(238, 2, NULL, 3, 31, 'Speak with Data - Phase 1-3', 'Belajar membaca atau memahami minimal 1 jenis data dasar yang digunakan di divisinya (misalnya: output, volume, target) | Mencatat minimal 1 hal yang bisa diukur dari pekerjaannya dan melacak progresnya', 'Belajar membaca atau memahami minimal 1 jenis data dasar yang digunakan di divisinya (misalnya: output, volume, target)', 'Mencatat minimal 1 hal yang bisa diukur dari pekerjaannya dan melacak progresnya', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(239, 2, NULL, 3, 26, 'Be A Wismilak Ambassador - Phase 4-6', 'Mengikuti aktivitas-aktivitas Wismilak', 'Mengikuti aktivitas-aktivitas Wismilak', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(240, 2, NULL, 3, 35, 'Collaborative - Phase 4-6', 'Ikut serta dalam 1 kegiatan atau project tim lintas fungsi', 'Ikut serta dalam 1 kegiatan atau project tim lintas fungsi', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(241, 2, NULL, 3, 38, 'Decisive - Phase 4-6', 'Menyampaikan kepada atasan peluang atau masalah (loophole) yang dapat berdampak ke departemen, divisi, atau perusahaan', 'Menyampaikan kepada atasan peluang atau masalah (loophole) yang dapat berdampak ke departemen, divisi, atau perusahaan', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(242, 2, NULL, 3, 29, 'Effective & Efficient - Phase 4-6', 'Membuat pertimbangan cost vs benefit perusahaan dari pekerjaan yang dilakukan | Mengusulkan ide-ide (>1) untuk menyederhanakan pekerjaan harian atau sistem kerja & standarisasi', 'Membuat pertimbangan cost vs benefit perusahaan dari pekerjaan yang dilakukan', 'Mengusulkan ide-ide (>1) untuk menyederhanakan pekerjaan harian atau sistem kerja & standarisasi', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(243, 2, NULL, 3, 23, 'Empathy - Phase 4-6', 'Menginisiasi diskusi dengan rekan berbeda divisi / departemen (klien internal) atau klien eksternal untuk memahami tantangan dan membantu mereka sesuai dengan wewenang dan tanggung jawab', 'Menginisiasi diskusi dengan rekan berbeda divisi / departemen (klien internal) atau klien eksternal untuk memahami tantangan dan membantu mereka sesuai dengan wewenang dan tanggung jawab', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(244, 2, NULL, 3, 41, 'Open Mind - Phase 4-6', 'Membuat list Strength, Area for Development, dan mengikuti pelatihan yang sesuai | Menjadi fasilitator atau moderator dalam sesi diskusi untuk integrasi berbagai ide', 'Membuat list Strength, Area for Development, dan mengikuti pelatihan yang sesuai', 'Menjadi fasilitator atau moderator dalam sesi diskusi untuk integrasi berbagai ide', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(245, 2, NULL, 3, 32, 'Speak with Data - Phase 4-6', 'Menyusun target kerja (Specific, Measurable, Achievable, Reachable, Time bound) berdasarkan data yang dikumpulkan | Menyusun dan presentasi laporan berbasis data dari pekerjaan yang dilakukan', 'Menyusun target kerja (Specific, Measurable, Achievable, Reachable, Time bound) berdasarkan data yang dikumpulkan', 'Menyusun dan presentasi laporan berbasis data dari pekerjaan yang dilakukan', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(246, 2, NULL, 3, 27, 'Be A Wismilak Ambassador - Phase 6+', 'Menjadi panitia, pelaksana, atau koordinator dalam minimal 1 event perusahaan', 'Menjadi panitia, pelaksana, atau koordinator dalam minimal 1 event perusahaan', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(247, 2, NULL, 3, 36, 'Collaborative - Phase 6+', 'Mengusulkan dan menjadi koordinator kolaborasi baru yang bisa menguntungkan dua tim/departemen/divisi', 'Mengusulkan dan menjadi koordinator kolaborasi baru yang bisa menguntungkan dua tim/departemen/divisi', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(248, 2, NULL, 3, 39, 'Decisive - Phase 6+', 'Memberi usulan strategis kepada atasan yang berdampak pada sistem/proses ke depan dan lanjutkan dengan PDCA (Plan , Do, Check, Action)', 'Memberi usulan strategis kepada atasan yang berdampak pada sistem/proses ke depan dan lanjutkan dengan PDCA (Plan , Do, Check, Action)', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(249, 2, NULL, 3, 30, 'Effective & Efficient - Phase 6+', 'Melatih 1 rekan kerja tentang tips kerja efisien yang sudah ter-standard', 'Melatih 1 rekan kerja tentang tips kerja efisien yang sudah ter-standard', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(250, 2, NULL, 3, 24, 'Empathy - Phase 6+', 'Karyawan ditugaskan menjadi \"buddy\" untuk rekan baru atau rekan satu tim dalam project tertentu', 'Karyawan ditugaskan menjadi \"buddy\" untuk rekan baru atau rekan satu tim dalam project tertentu', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(251, 2, NULL, 3, 42, 'Open Mind - Phase 6+', 'Menyampaikan ide unik/kreatif (tanpa takut salah) kepada atasan, lalu menilai bersama potensi pengembangannya.', 'Menyampaikan ide unik/kreatif (tanpa takut salah) kepada atasan, lalu menilai bersama potensi pengembangannya.', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(252, 2, NULL, 3, 33, 'Speak with Data - Phase 6+', 'Memberikan usulan pada proses kerja berdasarkan data yang dipresentasikan dan menunjukkan progress. | Studi kasus: membedakan mana data atau informasi milik Divisi dan Perusahaan yang boleh dan tidak boleh disebarluaskan', 'Memberikan usulan pada proses kerja berdasarkan data yang dipresentasikan dan menunjukkan progress.', 'Studi kasus: membedakan mana data atau informasi milik Divisi dan Perusahaan yang boleh dan tidak boleh disebarluaskan', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:24:59', '2026-04-07 16:24:59'),
(295, 4, NULL, 4, 4, 'Be A Wismilak Ambassador - Phase 1-3', 'Mempelajari sejarah atau filosofi perusahaan, lalu membagikan 1 hal yang paling menginspirasi | Mengunggah atau membagikan konten positif tentang kegiatan perusahaan di media sosial internal/perusahaan (jika diperbolehkan)', 'Mempelajari sejarah atau filosofi perusahaan, lalu membagikan 1 hal yang paling menginspirasi', 'Mengunggah atau membagikan konten positif tentang kegiatan perusahaan di media sosial internal/perusahaan (jika diperbolehkan)', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(296, 4, NULL, 4, 13, 'Collaborative - Phase 1-3', 'Mengetahui peran tim yang terlibat dalam pekerja hariannya', 'Mengetahui peran tim yang terlibat dalam pekerja hariannya', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(297, 4, NULL, 4, 16, 'Decisive - Phase 1-3', 'Langsung melapor jika ada kendala---Mengajukan solusi untuk penyelesaian masalah dalam pekerjaan harian, kemudian konsultasikan kepada atasan sebelum dilakukan', 'Langsung melapor jika ada kendala---Mengajukan solusi untuk penyelesaian masalah dalam pekerjaan harian, kemudian konsultasikan kepada atasan sebelum dilakukan', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(298, 4, NULL, 4, 7, 'Effective & Efficient - Phase 1-3', 'Mengetahui ranah pekerjaannya sesuai job desc dan visi misi perusahaan | Mengikuti pelatihan penggunaan alat kerja/SOP yang benar dan membuat laporan singkat pemahaman', 'Mengetahui ranah pekerjaannya sesuai job desc dan visi misi perusahaan', 'Mengikuti pelatihan penggunaan alat kerja/SOP yang benar dan membuat laporan singkat pemahaman', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(299, 4, NULL, 4, 1, 'Empathy - Phase 1-3', 'Memberikan apresiasi tertulis kepada rekan-rekan kerjanya | Bertanya kepada rekan satu tim tentang tantangan atau kesulitan yang mereka hadapi, memberikan saran atau membantu mereka sesuai dengan wewenang dan tanggung jawab', 'Memberikan apresiasi tertulis kepada rekan-rekan kerjanya', 'Bertanya kepada rekan satu tim tentang tantangan atau kesulitan yang mereka hadapi, memberikan saran atau membantu mereka sesuai dengan wewenang dan tanggung jawab', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(300, 4, NULL, 4, 19, 'Open Mind - Phase 1-3', 'Meminta dan memberikan 1 feedback dari atasan, rekan kerja, atau team dan menerapkannya. | Menyampaikan hambatan yang dialami kepada Atasan dan menyusun Action Plan untuk mengatasinya', 'Meminta dan memberikan 1 feedback dari atasan, rekan kerja, atau team dan menerapkannya.', 'Menyampaikan hambatan yang dialami kepada Atasan dan menyusun Action Plan untuk mengatasinya', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(301, 4, NULL, 4, 10, 'Speak with Data - Phase 1-3', 'Melaporkan hasil kerja sesuai jumlah nyata', 'Melaporkan hasil kerja sesuai jumlah nyata', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(302, 4, NULL, 4, 5, 'Be A Wismilak Ambassador - Phase 4-6', 'Mengikuti aktivitas-aktivitas Wismilak', 'Mengikuti aktivitas-aktivitas Wismilak', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(303, 4, NULL, 4, 14, 'Collaborative - Phase 4-6', 'Saling membantu / back up rekan kerja ketika dibutuhkan untuk mencapai target perusahaan', 'Saling membantu / back up rekan kerja ketika dibutuhkan untuk mencapai target perusahaan', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(304, 4, NULL, 4, 17, 'Decisive - Phase 4-6', 'Tidak membiarkan kesalahan berulang --- Menyampaikan kepada atasan peluang atau masalah (loophole) yang dapat berdampak ke departemen, divisi, atau perusahaan', 'Tidak membiarkan kesalahan berulang --- Menyampaikan kepada atasan peluang atau masalah (loophole) yang dapat berdampak ke departemen, divisi, atau perusahaan', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(305, 4, NULL, 4, 8, 'Effective & Efficient - Phase 4-6', 'Disiplin Bekerja sesuai dengan target perusahaan (waktu dan materi) | Mengusulkan ide-ide (>1) untuk menyederhanakan pekerjaan harian atau sistem kerja & standarisasi', 'Disiplin Bekerja sesuai dengan target perusahaan (waktu dan materi)', 'Mengusulkan ide-ide (>1) untuk menyederhanakan pekerjaan harian atau sistem kerja & standarisasi', '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(306, 4, NULL, 4, 2, 'Empathy - Phase 4-6', 'Inisiatif Menawarkan bantuan pada rekan kerja yang membutuhkan bantuan sesuai dengan ranahnya', 'Inisiatif Menawarkan bantuan pada rekan kerja yang membutuhkan bantuan sesuai dengan ranahnya', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(307, 4, NULL, 4, 20, 'Open Mind - Phase 4-6', 'Mengetahui kelebihan dan kekurangan yang dimiliki selama bekerja, dan dipersilahkan mengajukan pelatihan apabila diperlukan', 'Mengetahui kelebihan dan kekurangan yang dimiliki selama bekerja, dan dipersilahkan mengajukan pelatihan apabila diperlukan', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(308, 4, NULL, 4, 11, 'Speak with Data - Phase 4-6', 'Mengakui kesalahan tanpa ditutup-tutupi', 'Mengakui kesalahan tanpa ditutup-tutupi', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(309, 4, NULL, 4, 6, 'Be A Wismilak Ambassador - Phase 6+', 'Menjadi contoh kedisiplinan', 'Menjadi contoh kedisiplinan', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(310, 4, NULL, 4, 15, 'Collaborative - Phase 6+', 'Menjaga suasana kerja kondusif (Hal apa saja yang dapat dilakukan untuk mencapai hal ini)', 'Menjaga suasana kerja kondusif (Hal apa saja yang dapat dilakukan untuk mencapai hal ini)', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(311, 4, NULL, 4, 18, 'Decisive - Phase 6+', 'Memberi saran sederhana jika lihat potensi masalah', 'Memberi saran sederhana jika lihat potensi masalah', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(312, 4, NULL, 4, 9, 'Effective & Efficient - Phase 6+', 'Menghindari melakukan kesalahan berulang. Dan dapat Mengingatkan rekan jika kerja tidak sesuai SOP', 'Menghindari melakukan kesalahan berulang. Dan dapat Mengingatkan rekan jika kerja tidak sesuai SOP', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(313, 4, NULL, 4, 3, 'Empathy - Phase 6+', 'Membantu mengenalkan cara kerja ke rekan baru', 'Membantu mengenalkan cara kerja ke rekan baru', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(314, 4, NULL, 4, 21, 'Open Mind - Phase 6+', 'Berani menyampaikan ide unik/kreatif (tanpa takut salah) kepada atasan', 'Berani menyampaikan ide unik/kreatif (tanpa takut salah) kepada atasan', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54'),
(315, 4, NULL, 4, 12, 'Speak with Data - Phase 6+', 'Menyampaikan jika hasil turun & cari penyebab', 'Menyampaikan jika hasil turun & cari penyebab', NULL, '2026-04-07', '', NULL, NULL, 'draft', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', 0, '2026-04-07 16:40:54', '2026-04-07 16:40:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `vnb_plan_revisions`
--

CREATE TABLE `vnb_plan_revisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vnb_plan_id` bigint(20) UNSIGNED NOT NULL,
  `revision_number` int(11) NOT NULL COMMENT 'Revision attempt number',
  `requested_by` bigint(20) UNSIGNED NOT NULL,
  `revision_notes` text NOT NULL COMMENT 'Catatan revisi dari manager',
  `status` enum('pending','in_progress','submitted','applied') NOT NULL DEFAULT 'pending' COMMENT 'pending=draft, in_progress=being worked on, submitted=nhire kirim, applied=manager approve',
  `requested_at` datetime NOT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `applied_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `vnb_plan_revision_details`
--

CREATE TABLE `vnb_plan_revision_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vnb_plan_revision_id` bigint(20) UNSIGNED NOT NULL,
  `vnb_plan_item_id` bigint(20) UNSIGNED NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Values sebelum revisi: title, desc, dates, deliverables, metrics' CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Values sesudah revisi' CHECK (json_valid(`new_values`)),
  `changed_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `vnb_progress`
--

CREATE TABLE `vnb_progress` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `plan_item_id` bigint(20) UNSIGNED NOT NULL,
  `behavior_progress` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`behavior_progress`)),
  `progress_percentage` int(11) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `last_updated_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_actor_id_created_at_index` (`actor_id`,`created_at`),
  ADD KEY `activity_logs_target_type_target_id_index` (`target_type`,`target_id`),
  ADD KEY `activity_logs_action_type_created_at_index` (`action_type`,`created_at`);

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_employee_number_unique` (`employee_number`),
  ADD UNIQUE KEY `employees_email_unique` (`email`),
  ADD KEY `employees_division_id_foreign` (`division_id`),
  ADD KEY `employees_department_id_foreign` (`department_id`),
  ADD KEY `employees_position_id_foreign` (`position_id`),
  ADD KEY `employees_manager_functional_id_foreign` (`manager_functional_id`),
  ADD KEY `employees_manager_operational_id_foreign` (`manager_operational_id`);

--
-- Indeks untuk tabel `imports`
--
ALTER TABLE `imports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `imports_imported_by_foreign` (`imported_by`);

--
-- Indeks untuk tabel `import_rows`
--
ALTER TABLE `import_rows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `import_rows_employee_id_foreign` (`employee_id`),
  ADD KEY `import_rows_import_id_status_index` (`import_id`,`status`);

--
-- Indeks untuk tabel `managers`
--
ALTER TABLE `managers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `managers_email_unique` (`email`),
  ADD UNIQUE KEY `managers_employee_number_unique` (`employee_number`),
  ADD KEY `managers_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `master_companies`
--
ALTER TABLE `master_companies`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_departments`
--
ALTER TABLE `master_departments`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_divisions`
--
ALTER TABLE `master_divisions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_employee_statuses`
--
ALTER TABLE `master_employee_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_levels`
--
ALTER TABLE `master_levels`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_placements`
--
ALTER TABLE `master_placements`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `master_positions`
--
ALTER TABLE `master_positions`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indeks untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_recipient_id_foreign` (`recipient_id`),
  ADD KEY `notifications_employee_id_status_index` (`employee_id`,`status`),
  ADD KEY `notifications_channel_status_index` (`channel`,`status`);

--
-- Indeks untuk tabel `notification_logs`
--
ALTER TABLE `notification_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notification_logs_notification_id_foreign` (`notification_id`);

--
-- Indeks untuk tabel `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indeks untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_employee_id_foreign` (`employee_id`);

--
-- Indeks untuk tabel `vnb_cancellations`
--
ALTER TABLE `vnb_cancellations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vnb_cancellations_canceled_by_foreign` (`canceled_by`),
  ADD KEY `vnb_cancellations_approved_by_foreign` (`approved_by`),
  ADD KEY `vnb_cancellations_employee_id_approval_status_index` (`employee_id`,`approval_status`);

--
-- Indeks untuk tabel `vnb_evidences`
--
ALTER TABLE `vnb_evidences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vnb_evidences_uploaded_by_foreign` (`uploaded_by`),
  ADD KEY `vnb_evidences_plan_item_id_status_index` (`plan_item_id`,`status`);

--
-- Indeks untuk tabel `vnb_framework_items`
--
ALTER TABLE `vnb_framework_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vnb_framework_items_career_stage_behaviour_phase_unique` (`career_stage`,`behaviour`,`phase`),
  ADD KEY `vnb_framework_items_career_stage_index` (`career_stage`);

--
-- Indeks untuk tabel `vnb_periods`
--
ALTER TABLE `vnb_periods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vnb_periods_employee_id_phase_number_unique` (`employee_id`,`phase_number`),
  ADD KEY `vnb_periods_employee_id_status_index` (`employee_id`,`status`);

--
-- Indeks untuk tabel `vnb_plans`
--
ALTER TABLE `vnb_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vnb_plans_approved_by_foreign` (`approved_by`),
  ADD KEY `vnb_plans_employee_id_status_index` (`employee_id`,`status`),
  ADD KEY `vnb_plans_period_id_status_index` (`period_id`,`status`);

--
-- Indeks untuk tabel `vnb_plan_items`
--
ALTER TABLE `vnb_plan_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vnb_plan_items_plan_id_status_index` (`plan_id`),
  ADD KEY `vnb_plan_items_employee_id_status_index` (`employee_id`,`status`),
  ADD KEY `vnb_plan_items_vnb_framework_id_index` (`vnb_framework_id`),
  ADD KEY `vnb_plan_items_approved_functional_by_index` (`approved_functional_by`),
  ADD KEY `vnb_plan_items_approved_operational_by_index` (`approved_operational_by`),
  ADD KEY `vnb_plan_items_framework_item_id_foreign` (`framework_item_id`);

--
-- Indeks untuk tabel `vnb_plan_revisions`
--
ALTER TABLE `vnb_plan_revisions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vnb_plan_revisions_vnb_plan_id_revision_number_unique` (`vnb_plan_id`,`revision_number`),
  ADD KEY `vnb_plan_revisions_requested_by_foreign` (`requested_by`),
  ADD KEY `vnb_plan_revisions_vnb_plan_id_revision_number_index` (`vnb_plan_id`,`revision_number`);

--
-- Indeks untuk tabel `vnb_plan_revision_details`
--
ALTER TABLE `vnb_plan_revision_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vnb_plan_revision_details_changed_by_foreign` (`changed_by`),
  ADD KEY `vnb_plan_revision_details_vnb_plan_revision_id_index` (`vnb_plan_revision_id`),
  ADD KEY `vnb_plan_revision_details_vnb_plan_item_id_index` (`vnb_plan_item_id`);

--
-- Indeks untuk tabel `vnb_progress`
--
ALTER TABLE `vnb_progress`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vnb_progress_employee_id_plan_item_id_unique` (`employee_id`,`plan_item_id`),
  ADD KEY `vnb_progress_plan_item_id_foreign` (`plan_item_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `imports`
--
ALTER TABLE `imports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `import_rows`
--
ALTER TABLE `import_rows`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `managers`
--
ALTER TABLE `managers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `master_companies`
--
ALTER TABLE `master_companies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `master_departments`
--
ALTER TABLE `master_departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT untuk tabel `master_divisions`
--
ALTER TABLE `master_divisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `master_employee_statuses`
--
ALTER TABLE `master_employee_statuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `master_levels`
--
ALTER TABLE `master_levels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `master_placements`
--
ALTER TABLE `master_placements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT untuk tabel `master_positions`
--
ALTER TABLE `master_positions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `notification_logs`
--
ALTER TABLE `notification_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `vnb_cancellations`
--
ALTER TABLE `vnb_cancellations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `vnb_evidences`
--
ALTER TABLE `vnb_evidences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `vnb_framework_items`
--
ALTER TABLE `vnb_framework_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=85;

--
-- AUTO_INCREMENT untuk tabel `vnb_periods`
--
ALTER TABLE `vnb_periods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT untuk tabel `vnb_plans`
--
ALTER TABLE `vnb_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `vnb_plan_items`
--
ALTER TABLE `vnb_plan_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=316;

--
-- AUTO_INCREMENT untuk tabel `vnb_plan_revisions`
--
ALTER TABLE `vnb_plan_revisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `vnb_plan_revision_details`
--
ALTER TABLE `vnb_plan_revision_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `vnb_progress`
--
ALTER TABLE `vnb_progress`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_actor_id_foreign` FOREIGN KEY (`actor_id`) REFERENCES `employees` (`id`);

--
-- Ketidakleluasaan untuk tabel `employees`
--
ALTER TABLE `employees`
  ADD CONSTRAINT `employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `master_departments` (`id`),
  ADD CONSTRAINT `employees_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `master_divisions` (`id`),
  ADD CONSTRAINT `employees_manager_functional_id_foreign` FOREIGN KEY (`manager_functional_id`) REFERENCES `managers` (`id`),
  ADD CONSTRAINT `employees_manager_operational_id_foreign` FOREIGN KEY (`manager_operational_id`) REFERENCES `managers` (`id`),
  ADD CONSTRAINT `employees_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `master_positions` (`id`);

--
-- Ketidakleluasaan untuk tabel `imports`
--
ALTER TABLE `imports`
  ADD CONSTRAINT `imports_imported_by_foreign` FOREIGN KEY (`imported_by`) REFERENCES `employees` (`id`);

--
-- Ketidakleluasaan untuk tabel `import_rows`
--
ALTER TABLE `import_rows`
  ADD CONSTRAINT `import_rows_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `import_rows_import_id_foreign` FOREIGN KEY (`import_id`) REFERENCES `imports` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `managers`
--
ALTER TABLE `managers`
  ADD CONSTRAINT `managers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_recipient_id_foreign` FOREIGN KEY (`recipient_id`) REFERENCES `employees` (`id`);

--
-- Ketidakleluasaan untuk tabel `notification_logs`
--
ALTER TABLE `notification_logs`
  ADD CONSTRAINT `notification_logs_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `vnb_cancellations`
--
ALTER TABLE `vnb_cancellations`
  ADD CONSTRAINT `vnb_cancellations_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `vnb_cancellations_canceled_by_foreign` FOREIGN KEY (`canceled_by`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `vnb_cancellations_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `vnb_evidences`
--
ALTER TABLE `vnb_evidences`
  ADD CONSTRAINT `vnb_evidences_plan_item_id_foreign` FOREIGN KEY (`plan_item_id`) REFERENCES `vnb_plan_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vnb_evidences_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `employees` (`id`);

--
-- Ketidakleluasaan untuk tabel `vnb_periods`
--
ALTER TABLE `vnb_periods`
  ADD CONSTRAINT `vnb_periods_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `vnb_plans`
--
ALTER TABLE `vnb_plans`
  ADD CONSTRAINT `vnb_plans_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `vnb_plans_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vnb_plans_period_id_foreign` FOREIGN KEY (`period_id`) REFERENCES `vnb_periods` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `vnb_plan_items`
--
ALTER TABLE `vnb_plan_items`
  ADD CONSTRAINT `vnb_plan_items_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vnb_plan_items_framework_item_id_foreign` FOREIGN KEY (`framework_item_id`) REFERENCES `vnb_framework_items` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vnb_plan_items_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `vnb_plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vnb_plan_items_vnb_framework_id_foreign` FOREIGN KEY (`vnb_framework_id`) REFERENCES `vnb_framework_items` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `vnb_plan_revisions`
--
ALTER TABLE `vnb_plan_revisions`
  ADD CONSTRAINT `vnb_plan_revisions_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `vnb_plan_revisions_vnb_plan_id_foreign` FOREIGN KEY (`vnb_plan_id`) REFERENCES `vnb_plans` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `vnb_plan_revision_details`
--
ALTER TABLE `vnb_plan_revision_details`
  ADD CONSTRAINT `vnb_plan_revision_details_changed_by_foreign` FOREIGN KEY (`changed_by`) REFERENCES `employees` (`id`),
  ADD CONSTRAINT `vnb_plan_revision_details_vnb_plan_item_id_foreign` FOREIGN KEY (`vnb_plan_item_id`) REFERENCES `vnb_plan_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vnb_plan_revision_details_vnb_plan_revision_id_foreign` FOREIGN KEY (`vnb_plan_revision_id`) REFERENCES `vnb_plan_revisions` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `vnb_progress`
--
ALTER TABLE `vnb_progress`
  ADD CONSTRAINT `vnb_progress_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `vnb_progress_plan_item_id_foreign` FOREIGN KEY (`plan_item_id`) REFERENCES `vnb_plan_items` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
