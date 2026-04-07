-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Apr 2026 pada 11.25
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
(1, 'manage_self_non_staff', 'Empathy', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(2, 'manage_self_non_staff', 'Empathy', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(3, 'manage_self_non_staff', 'Empathy', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(4, 'manage_self_non_staff', 'Be A Wismilak Ambassador', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(5, 'manage_self_non_staff', 'Be A Wismilak Ambassador', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(6, 'manage_self_non_staff', 'Be A Wismilak Ambassador', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(7, 'manage_self_non_staff', 'Effective & Efficient', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(8, 'manage_self_non_staff', 'Effective & Efficient', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(9, 'manage_self_non_staff', 'Effective & Efficient', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(10, 'manage_self_non_staff', 'Speak with Data', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(11, 'manage_self_non_staff', 'Speak with Data', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(12, 'manage_self_non_staff', 'Speak with Data', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(13, 'manage_self_non_staff', 'Collaborative', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(14, 'manage_self_non_staff', 'Collaborative', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(15, 'manage_self_non_staff', 'Collaborative', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(16, 'manage_self_non_staff', 'Decisive', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(17, 'manage_self_non_staff', 'Decisive', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(18, 'manage_self_non_staff', 'Decisive', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(19, 'manage_self_non_staff', 'Open Mind', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(20, 'manage_self_non_staff', 'Open Mind', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(21, 'manage_self_non_staff', 'Open Mind', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(22, 'manage_self_staff', 'Empathy', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(23, 'manage_self_staff', 'Empathy', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(24, 'manage_self_staff', 'Empathy', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(25, 'manage_self_staff', 'Be A Wismilak Ambassador', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(26, 'manage_self_staff', 'Be A Wismilak Ambassador', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(27, 'manage_self_staff', 'Be A Wismilak Ambassador', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(28, 'manage_self_staff', 'Effective & Efficient', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(29, 'manage_self_staff', 'Effective & Efficient', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(30, 'manage_self_staff', 'Effective & Efficient', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(31, 'manage_self_staff', 'Speak with Data', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(32, 'manage_self_staff', 'Speak with Data', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(33, 'manage_self_staff', 'Speak with Data', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(34, 'manage_self_staff', 'Collaborative', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(35, 'manage_self_staff', 'Collaborative', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(36, 'manage_self_staff', 'Collaborative', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(37, 'manage_self_staff', 'Decisive', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(38, 'manage_self_staff', 'Decisive', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(39, 'manage_self_staff', 'Decisive', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(40, 'manage_self_staff', 'Open Mind', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(41, 'manage_self_staff', 'Open Mind', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(42, 'manage_self_staff', 'Open Mind', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(85, 'manage_function', 'Empathy', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(86, 'manage_function', 'Empathy', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(87, 'manage_function', 'Empathy', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(88, 'manage_function', 'Be A Wismilak Ambassador', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(89, 'manage_function', 'Be A Wismilak Ambassador', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(90, 'manage_function', 'Be A Wismilak Ambassador', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(91, 'manage_function', 'Effective & Efficient', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(92, 'manage_function', 'Effective & Efficient', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(93, 'manage_function', 'Effective & Efficient', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(94, 'manage_function', 'Speak with Data', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(95, 'manage_function', 'Speak with Data', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(96, 'manage_function', 'Speak with Data', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(97, 'manage_function', 'Collaborative', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(98, 'manage_function', 'Collaborative', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(99, 'manage_function', 'Collaborative', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(100, 'manage_function', 'Decisive', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(101, 'manage_function', 'Decisive', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(102, 'manage_function', 'Decisive', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(103, 'manage_function', 'Open Mind', '1', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(104, 'manage_function', 'Open Mind', '2', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(105, 'manage_function', 'Open Mind', '3', NULL, NULL, '2026-04-07 09:04:04', '2026-04-07 09:04:04'),
(106, 'manage_self_non_staff', 'Empathy', '1-3', 'Memberikan apresiasi tertulis kepada rekan-rekan kerjanya', 'Bertanya kepada rekan satu tim tentang tantangan atau kesulitan yang mereka hadapi, memberikan saran atau membantu mereka sesuai dengan wewenang dan tanggung jawab', '2026-04-07 09:09:21', '2026-04-07 09:09:21'),
(107, 'manage_self_non_staff', 'Empathy', '4-6', 'Inisiatif Menawarkan bantuan pada rekan kerja yang membutuhkan bantuan sesuai dengan ranahnya', NULL, '2026-04-07 09:09:22', '2026-04-07 09:09:22'),
(108, 'manage_self_non_staff', 'Empathy', '6+', 'Membantu mengenalkan cara kerja ke rekan baru', NULL, '2026-04-07 09:09:23', '2026-04-07 09:09:23'),
(109, 'manage_self_non_staff', 'Be A Wismilak Ambassador', '1-3', 'Mempelajari sejarah atau filosofi perusahaan, lalu membagikan 1 hal yang paling menginspirasi', 'Mengunggah atau membagikan konten positif tentang kegiatan perusahaan di media sosial internal/perusahaan (jika diperbolehkan)', '2026-04-07 09:09:24', '2026-04-07 09:09:24'),
(110, 'manage_self_non_staff', 'Be A Wismilak Ambassador', '4-6', 'Mengikuti aktivitas-aktivitas Wismilak', NULL, '2026-04-07 09:09:25', '2026-04-07 09:09:25'),
(111, 'manage_self_non_staff', 'Be A Wismilak Ambassador', '6+', 'Menjadi contoh kedisiplinan', NULL, '2026-04-07 09:09:26', '2026-04-07 09:09:26'),
(112, 'manage_self_non_staff', 'Effective & Efficient', '1-3', 'Mengetahui ranah pekerjaannya sesuai job desc dan visi misi perusahaan', 'Mengikuti pelatihan penggunaan alat kerja/SOP yang benar dan membuat laporan singkat pemahaman', '2026-04-07 09:09:27', '2026-04-07 09:09:27'),
(113, 'manage_self_non_staff', 'Effective & Efficient', '4-6', 'Disiplin Bekerja sesuai dengan target perusahaan (waktu dan materi)', 'Mengusulkan ide-ide (>1) untuk menyederhanakan pekerjaan harian atau sistem kerja & standarisasi', '2026-04-07 09:09:28', '2026-04-07 09:09:28'),
(114, 'manage_self_non_staff', 'Effective & Efficient', '6+', 'Menghindari melakukan kesalahan berulang. Dan dapat Mengingatkan rekan jika kerja tidak sesuai SOP', NULL, '2026-04-07 09:09:29', '2026-04-07 09:09:29'),
(115, 'manage_self_non_staff', 'Speak with Data', '1-3', 'Melaporkan hasil kerja sesuai jumlah nyata', NULL, '2026-04-07 09:09:30', '2026-04-07 09:09:30'),
(116, 'manage_self_non_staff', 'Speak with Data', '4-6', 'Mengakui kesalahan tanpa ditutup-tutupi', NULL, '2026-04-07 09:09:31', '2026-04-07 09:09:31'),
(117, 'manage_self_non_staff', 'Speak with Data', '6+', 'Menyampaikan jika hasil turun & cari penyebab', NULL, '2026-04-07 09:09:32', '2026-04-07 09:09:32'),
(118, 'manage_self_non_staff', 'Collaborative', '1-3', 'Mengetahui peran tim yang terlibat dalam pekerja hariannya', NULL, '2026-04-07 09:09:33', '2026-04-07 09:09:33'),
(119, 'manage_self_non_staff', 'Collaborative', '4-6', 'Saling membantu / back up rekan kerja ketika dibutuhkan untuk mencapai target perusahaan', NULL, '2026-04-07 09:09:34', '2026-04-07 09:09:34'),
(120, 'manage_self_non_staff', 'Collaborative', '6+', 'Menjaga suasana kerja kondusif (Hal apa saja yang dapat dilakukan untuk mencapai hal ini)', NULL, '2026-04-07 09:09:35', '2026-04-07 09:09:35'),
(121, 'manage_self_non_staff', 'Decisive', '1-3', 'Langsung melapor jika ada kendala---Mengajukan solusi untuk penyelesaian masalah dalam pekerjaan harian, kemudian konsultasikan kepada atasan sebelum dilakukan', NULL, '2026-04-07 09:09:36', '2026-04-07 09:09:36'),
(122, 'manage_self_non_staff', 'Decisive', '4-6', 'Tidak membiarkan kesalahan berulang --- Menyampaikan kepada atasan peluang atau masalah (loophole) yang dapat berdampak ke departemen, divisi, atau perusahaan', NULL, '2026-04-07 09:09:37', '2026-04-07 09:09:37'),
(123, 'manage_self_non_staff', 'Decisive', '6+', 'Memberi saran sederhana jika lihat potensi masalah', NULL, '2026-04-07 09:09:37', '2026-04-07 09:09:37'),
(124, 'manage_self_non_staff', 'Open Mind', '1-3', 'Meminta dan memberikan 1 feedback dari atasan, rekan kerja, atau team dan menerapkannya.', 'Menyampaikan hambatan yang dialami kepada Atasan dan menyusun Action Plan untuk mengatasinya', '2026-04-07 09:09:38', '2026-04-07 09:09:38'),
(125, 'manage_self_non_staff', 'Open Mind', '4-6', 'Mengetahui kelebihan dan kekurangan yang dimiliki selama bekerja, dan dipersilahkan mengajukan pelatihan apabila diperlukan', NULL, '2026-04-07 09:09:39', '2026-04-07 09:09:39'),
(126, 'manage_self_non_staff', 'Open Mind', '6+', 'Berani menyampaikan ide unik/kreatif (tanpa takut salah) kepada atasan', NULL, '2026-04-07 09:09:40', '2026-04-07 09:09:40'),
(127, 'manage_self_staff', 'Empathy', '1-3', 'Memberikan apresiasi tertulis kepada rekan-rekan kerjanya', 'Bertanya kepada rekan satu tim tentang tantangan atau kesulitan yang mereka hadapi, memberikan saran atau membantu mereka sesuai dengan wewenang dan tanggung jawab', '2026-04-07 09:14:31', '2026-04-07 09:14:31'),
(128, 'manage_self_staff', 'Empathy', '4-6', 'Menginisiasi diskusi dengan rekan berbeda divisi / departemen (klien internal) atau klien eksternal untuk memahami tantangan dan membantu mereka sesuai dengan wewenang dan tanggung jawab', NULL, '2026-04-07 09:14:31', '2026-04-07 09:14:31'),
(129, 'manage_self_staff', 'Empathy', '6+', 'Karyawan ditugaskan menjadi “buddy” untuk rekan baru atau rekan satu tim dalam project tertentu', NULL, '2026-04-07 09:14:32', '2026-04-07 09:14:32'),
(130, 'manage_self_staff', 'Be A Wismilak Ambassador', '1-3', 'Mempelajari sejarah atau filosofi perusahaan, lalu membagikan 1 hal yang paling menginspirasi', 'Mengunggah atau membagikan konten positif tentang kegiatan perusahaan di media sosial internal/perusahaan (jika diperbolehkan)', '2026-04-07 09:14:32', '2026-04-07 09:14:32'),
(131, 'manage_self_staff', 'Be A Wismilak Ambassador', '4-6', 'Mengikuti aktivitas-aktivitas Wismilak', NULL, '2026-04-07 09:14:33', '2026-04-07 09:14:33'),
(132, 'manage_self_staff', 'Be A Wismilak Ambassador', '6+', 'Menjadi panitia, pelaksana, atau koordinator dalam minimal 1 event perusahaan', NULL, '2026-04-07 09:14:33', '2026-04-07 09:14:33'),
(133, 'manage_self_staff', 'Effective & Efficient', '1-3', 'Mengisi template “Visi & Misi Alignment Map”: mencocokkan pekerjaan harian dengan kontribusinya terhadap visi & misi perusahaan', 'Mengikuti pelatihan penggunaan alat kerja / SOP yang benar dan membuat laporan ringkas pemahaman', '2026-04-07 09:14:34', '2026-04-07 09:14:34'),
(134, 'manage_self_staff', 'Effective & Efficient', '4-6', 'Membuat pertimbangan cost vs benefit perusahaan dari pekerjaan yang dilakukan', 'Mengusulkan ide-ide (>1) untuk menyederhanakan pekerjaan harian atau sistem kerja & standarisasi', '2026-04-07 09:14:34', '2026-04-07 09:14:34'),
(135, 'manage_self_staff', 'Effective & Efficient', '6+', 'Melatih 1 rekan kerja tentang tips kerja efisien yang sudah ter-standard', NULL, '2026-04-07 09:14:34', '2026-04-07 09:14:34'),
(136, 'manage_self_staff', 'Speak with Data', '1-3', 'Belajar membaca atau memahami minimal 1 jenis data dasar yang digunakan di divisinya (misalnya: output, volume, target)', 'Mencatat minimal 1 hal yang bisa diukur dari pekerjaannya dan melacak progresnya', '2026-04-07 09:14:35', '2026-04-07 09:14:35'),
(137, 'manage_self_staff', 'Speak with Data', '4-6', 'Menyusun target kerja (Specific, Measurable, Achievable, Reachable, Time bound) berdasarkan data yang dikumpulkan', 'Menyusun dan presentasi laporan berbasis data dari pekerjaan yang dilakukan', '2026-04-07 09:14:35', '2026-04-07 09:14:35'),
(138, 'manage_self_staff', 'Speak with Data', '6+', 'Memberikan usulan pada proses kerja berdasarkan data yang dipresentasikan dan menunjukkan progress.', 'Studi kasus: membedakan mana data atau informasi milik Divisi dan Perusahaan yang boleh dan tidak boleh disebarluaskan', '2026-04-07 09:14:36', '2026-04-07 09:14:36'),
(139, 'manage_self_staff', 'Collaborative', '1-3', 'Identifikasi impact pekerjaan karyawan pada bagian, departemen atau divisi lain (user) untuk mengenal peran mereka dan sebaliknya', NULL, '2026-04-07 09:14:36', '2026-04-07 09:14:36'),
(140, 'manage_self_staff', 'Collaborative', '4-6', 'Ikut serta dalam 1 kegiatan atau project tim lintas fungsi', NULL, '2026-04-07 09:14:36', '2026-04-07 09:14:36'),
(141, 'manage_self_staff', 'Collaborative', '6+', 'Mengusulkan dan menjadi koordinator kolaborasi baru yang bisa menguntungkan dua tim/departemen/divisi', NULL, '2026-04-07 09:14:36', '2026-04-07 09:14:36'),
(142, 'manage_self_staff', 'Decisive', '1-3', 'Mengajukan solusi untuk penyelesaian masalah dalam pekerjaan harian, kemudian konsultasikan kepada atasan sebelum dilakukan', NULL, '2026-04-07 09:14:37', '2026-04-07 09:14:37'),
(143, 'manage_self_staff', 'Decisive', '4-6', 'Menyampaikan kepada atasan peluang atau masalah (loophole) yang dapat berdampak ke departemen, divisi, atau perusahaan', NULL, '2026-04-07 09:14:37', '2026-04-07 09:14:37'),
(144, 'manage_self_staff', 'Decisive', '6+', 'Memberi usulan strategis kepada atasan yang berdampak pada sistem/proses ke depan dan lanjutkan dengan PDCA (Plan , Do, Check, Action)', NULL, '2026-04-07 09:14:38', '2026-04-07 09:14:38'),
(145, 'manage_self_staff', 'Open Mind', '1-3', 'Meminta dan memberikan 1 feedback dari atasan, rekan kerja, atau team dan menerapkannya.', 'Menyampaikan hambatan yang dialami kepada Atasan dan menyusun Action Plan untuk mengatasinya', '2026-04-07 09:14:38', '2026-04-07 09:14:38'),
(146, 'manage_self_staff', 'Open Mind', '4-6', 'Membuat list Strength, Area for Development, dan mengikuti pelatihan yang sesuai', 'Menjadi fasilitator atau moderator dalam sesi diskusi untuk integrasi berbagai ide', '2026-04-07 09:14:38', '2026-04-07 09:14:38'),
(147, 'manage_self_staff', 'Open Mind', '6+', 'Menyampaikan ide unik/kreatif (tanpa takut salah) kepada atasan, lalu menilai bersama potensi pengembangannya.', NULL, '2026-04-07 09:14:39', '2026-04-07 09:14:39'),
(148, 'manage_others', 'Be A Wismilak Ambassador', '1', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(149, 'manage_others', 'Be A Wismilak Ambassador', '1-3', 'Mempelajari sejarah atau filosofi perusahaan, lalu membagikan 1 hal yang paling menginspirasi', 'Mengunggah atau membagikan konten positif tentang kegiatan perusahaan di media sosial internal/perusahaan (jika diperbolehkan)', '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(150, 'manage_others', 'Be A Wismilak Ambassador', '2', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(151, 'manage_others', 'Be A Wismilak Ambassador', '3', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(152, 'manage_others', 'Be A Wismilak Ambassador', '4-6', 'Mengikuti aktivitas-aktivitas Wismilak', NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(153, 'manage_others', 'Be A Wismilak Ambassador', '6+', 'Menjadi panitia, pelaksana, atau koordinator dalam minimal 1 event perusahaan', NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(154, 'manage_others', 'Collaborative', '1', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(155, 'manage_others', 'Collaborative', '1-3', 'Identifikasi impact pekerjaan karyawan pada bagian, departemen atau divisi lain (user) untuk mengenal peran mereka dan sebaliknya', NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(156, 'manage_others', 'Collaborative', '2', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(157, 'manage_others', 'Collaborative', '3', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(158, 'manage_others', 'Collaborative', '4-6', 'Ikut serta dalam 1 kegiatan atau project tim lintas fungsi', NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(159, 'manage_others', 'Collaborative', '6+', 'Mengusulkan dan menjadi koordinator kolaborasi baru yang bisa menguntungkan dua tim/departemen/divisi', NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(160, 'manage_others', 'Decisive', '1', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(161, 'manage_others', 'Decisive', '1-3', 'Mengajukan solusi untuk penyelesaian masalah dalam pekerjaan harian, kemudian konsultasikan kepada atasan sebelum dilakukan', NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(162, 'manage_others', 'Decisive', '2', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(163, 'manage_others', 'Decisive', '3', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(164, 'manage_others', 'Decisive', '4-6', 'Menyampaikan kepada atasan peluang atau masalah (loophole) yang dapat berdampak ke departemen, divisi, atau perusahaan', NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(165, 'manage_others', 'Decisive', '6+', 'Memberi usulan strategis kepada atasan yang berdampak pada sistem/proses ke depan dan lanjutkan dengan PDCA (Plan , Do, Check, Action)', NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(166, 'manage_others', 'Effective & Efficient', '1', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(167, 'manage_others', 'Effective & Efficient', '1-3', 'Mengisi template “Visi & Misi Alignment Map”: mencocokkan pekerjaan harian dengan kontribusinya terhadap visi & misi perusahaan', 'Mengikuti pelatihan penggunaan alat kerja / SOP yang benar dan membuat laporan ringkas pemahaman', '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(168, 'manage_others', 'Effective & Efficient', '2', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(169, 'manage_others', 'Effective & Efficient', '3', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(170, 'manage_others', 'Effective & Efficient', '4-6', 'Membuat pertimbangan cost vs benefit perusahaan dari pekerjaan yang dilakukan', 'Mengusulkan ide-ide (>1) untuk menyederhanakan pekerjaan harian atau sistem kerja & standarisasi', '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(171, 'manage_others', 'Effective & Efficient', '6+', 'Melatih 1 rekan kerja tentang tips kerja efisien yang sudah ter-standard', NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(172, 'manage_others', 'Empathy', '1', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(173, 'manage_others', 'Empathy', '1-3', 'Memberikan apresiasi tertulis kepada rekan-rekan kerjanya', 'Bertanya kepada rekan satu tim tentang tantangan atau kesulitan yang mereka hadapi, memberikan saran atau membantu mereka sesuai dengan wewenang dan tanggung jawab', '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(174, 'manage_others', 'Empathy', '2', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(175, 'manage_others', 'Empathy', '3', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(176, 'manage_others', 'Empathy', '4-6', 'Menginisiasi diskusi dengan rekan berbeda divisi / departemen (klien internal) atau klien eksternal untuk memahami tantangan dan membantu mereka sesuai dengan wewenang dan tanggung jawab', NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(177, 'manage_others', 'Empathy', '6+', 'Karyawan ditugaskan menjadi “buddy” untuk rekan baru atau rekan satu tim dalam project tertentu', NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(178, 'manage_others', 'Open Mind', '1', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(179, 'manage_others', 'Open Mind', '1-3', 'Meminta dan memberikan 1 feedback dari atasan, rekan kerja, atau team dan menerapkannya.', 'Menyampaikan hambatan yang dialami kepada Atasan dan menyusun Action Plan untuk mengatasinya', '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(180, 'manage_others', 'Open Mind', '2', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(181, 'manage_others', 'Open Mind', '3', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(182, 'manage_others', 'Open Mind', '4-6', 'Membuat list Strength, Area for Development, dan mengikuti pelatihan yang sesuai', 'Menjadi fasilitator atau moderator dalam sesi diskusi untuk integrasi berbagai ide', '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(183, 'manage_others', 'Open Mind', '6+', 'Menyampaikan ide unik/kreatif (tanpa takut salah) kepada atasan, lalu menilai bersama potensi pengembangannya.', NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(184, 'manage_others', 'Speak with Data', '1', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(185, 'manage_others', 'Speak with Data', '1-3', 'Belajar membaca atau memahami minimal 1 jenis data dasar yang digunakan di divisinya (misalnya: output, volume, target)', 'Mencatat minimal 1 hal yang bisa diukur dari pekerjaannya dan melacak progresnya', '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(186, 'manage_others', 'Speak with Data', '2', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(187, 'manage_others', 'Speak with Data', '3', NULL, NULL, '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(188, 'manage_others', 'Speak with Data', '4-6', 'Menyusun target kerja (Specific, Measurable, Achievable, Reachable, Time bound) berdasarkan data yang dikumpulkan', 'Menyusun dan presentasi laporan berbasis data dari pekerjaan yang dilakukan', '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(189, 'manage_others', 'Speak with Data', '6+', 'Memberikan usulan pada proses kerja berdasarkan data yang dipresentasikan dan menunjukkan progress.', 'Studi kasus: membedakan mana data atau informasi milik Divisi dan Perusahaan yang boleh dan tidak boleh disebarluaskan', '2026-04-07 09:14:46', '2026-04-07 09:14:46'),
(190, 'manage_managers', 'Be A Wismilak Ambassador', '1', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(191, 'manage_managers', 'Be A Wismilak Ambassador', '1-3', 'Mempelajari sejarah atau filosofi perusahaan, lalu membagikan 1 hal yang paling menginspirasi', 'Mengunggah atau membagikan konten positif tentang kegiatan perusahaan di media sosial internal/perusahaan (jika diperbolehkan)', '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(192, 'manage_managers', 'Be A Wismilak Ambassador', '2', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(193, 'manage_managers', 'Be A Wismilak Ambassador', '3', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(194, 'manage_managers', 'Be A Wismilak Ambassador', '4-6', 'Mengikuti aktivitas-aktivitas Wismilak', NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(195, 'manage_managers', 'Be A Wismilak Ambassador', '6+', 'Menjadi panitia, pelaksana, atau koordinator dalam minimal 1 event perusahaan', NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(196, 'manage_managers', 'Collaborative', '1', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(197, 'manage_managers', 'Collaborative', '1-3', 'Identifikasi impact pekerjaan karyawan pada bagian, departemen atau divisi lain (user) untuk mengenal peran mereka dan sebaliknya', NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(198, 'manage_managers', 'Collaborative', '2', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(199, 'manage_managers', 'Collaborative', '3', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(200, 'manage_managers', 'Collaborative', '4-6', 'Ikut serta dalam 1 kegiatan atau project tim lintas fungsi', NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(201, 'manage_managers', 'Collaborative', '6+', 'Mengusulkan dan menjadi koordinator kolaborasi baru yang bisa menguntungkan dua tim/departemen/divisi', NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(202, 'manage_managers', 'Decisive', '1', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(203, 'manage_managers', 'Decisive', '1-3', 'Mengajukan solusi untuk penyelesaian masalah dalam pekerjaan harian, kemudian konsultasikan kepada atasan sebelum dilakukan', NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(204, 'manage_managers', 'Decisive', '2', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(205, 'manage_managers', 'Decisive', '3', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(206, 'manage_managers', 'Decisive', '4-6', 'Menyampaikan kepada atasan peluang atau masalah (loophole) yang dapat berdampak ke departemen, divisi, atau perusahaan', NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(207, 'manage_managers', 'Decisive', '6+', 'Memberi usulan strategis kepada atasan yang berdampak pada sistem/proses ke depan dan lanjutkan dengan PDCA (Plan , Do, Check, Action)', NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(208, 'manage_managers', 'Effective & Efficient', '1', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(209, 'manage_managers', 'Effective & Efficient', '1-3', 'Mengisi template “Visi & Misi Alignment Map”: mencocokkan pekerjaan harian dengan kontribusinya terhadap visi & misi perusahaan', 'Mengikuti pelatihan penggunaan alat kerja / SOP yang benar dan membuat laporan ringkas pemahaman', '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(210, 'manage_managers', 'Effective & Efficient', '2', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(211, 'manage_managers', 'Effective & Efficient', '3', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(212, 'manage_managers', 'Effective & Efficient', '4-6', 'Membuat pertimbangan cost vs benefit perusahaan dari pekerjaan yang dilakukan', 'Mengusulkan ide-ide (>1) untuk menyederhanakan pekerjaan harian atau sistem kerja & standarisasi', '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(213, 'manage_managers', 'Effective & Efficient', '6+', 'Melatih 1 rekan kerja tentang tips kerja efisien yang sudah ter-standard', NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(214, 'manage_managers', 'Empathy', '1', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(215, 'manage_managers', 'Empathy', '1-3', 'Memberikan apresiasi tertulis kepada rekan-rekan kerjanya', 'Bertanya kepada rekan satu tim tentang tantangan atau kesulitan yang mereka hadapi, memberikan saran atau membantu mereka sesuai dengan wewenang dan tanggung jawab', '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(216, 'manage_managers', 'Empathy', '2', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(217, 'manage_managers', 'Empathy', '3', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(218, 'manage_managers', 'Empathy', '4-6', 'Menginisiasi diskusi dengan rekan berbeda divisi / departemen (klien internal) atau klien eksternal untuk memahami tantangan dan membantu mereka sesuai dengan wewenang dan tanggung jawab', NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(219, 'manage_managers', 'Empathy', '6+', 'Karyawan ditugaskan menjadi “buddy” untuk rekan baru atau rekan satu tim dalam project tertentu', NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(220, 'manage_managers', 'Open Mind', '1', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(221, 'manage_managers', 'Open Mind', '1-3', 'Meminta dan memberikan 1 feedback dari atasan, rekan kerja, atau team dan menerapkannya.', 'Menyampaikan hambatan yang dialami kepada Atasan dan menyusun Action Plan untuk mengatasinya', '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(222, 'manage_managers', 'Open Mind', '2', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(223, 'manage_managers', 'Open Mind', '3', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(224, 'manage_managers', 'Open Mind', '4-6', 'Membuat list Strength, Area for Development, dan mengikuti pelatihan yang sesuai', 'Menjadi fasilitator atau moderator dalam sesi diskusi untuk integrasi berbagai ide', '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(225, 'manage_managers', 'Open Mind', '6+', 'Menyampaikan ide unik/kreatif (tanpa takut salah) kepada atasan, lalu menilai bersama potensi pengembangannya.', NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(226, 'manage_managers', 'Speak with Data', '1', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(227, 'manage_managers', 'Speak with Data', '1-3', 'Belajar membaca atau memahami minimal 1 jenis data dasar yang digunakan di divisinya (misalnya: output, volume, target)', 'Mencatat minimal 1 hal yang bisa diukur dari pekerjaannya dan melacak progresnya', '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(228, 'manage_managers', 'Speak with Data', '2', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(229, 'manage_managers', 'Speak with Data', '3', NULL, NULL, '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(230, 'manage_managers', 'Speak with Data', '4-6', 'Menyusun target kerja (Specific, Measurable, Achievable, Reachable, Time bound) berdasarkan data yang dikumpulkan', 'Menyusun dan presentasi laporan berbasis data dari pekerjaan yang dilakukan', '2026-04-07 09:14:52', '2026-04-07 09:14:52'),
(231, 'manage_managers', 'Speak with Data', '6+', 'Memberikan usulan pada proses kerja berdasarkan data yang dipresentasikan dan menunjukkan progress.', 'Studi kasus: membedakan mana data atau informasi milik Divisi dan Perusahaan yang boleh dan tidak boleh disebarluaskan', '2026-04-07 09:14:52', '2026-04-07 09:14:52');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `vnb_framework_items`
--
ALTER TABLE `vnb_framework_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vnb_framework_items_career_stage_behaviour_phase_unique` (`career_stage`,`behaviour`,`phase`),
  ADD KEY `vnb_framework_items_career_stage_index` (`career_stage`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `vnb_framework_items`
--
ALTER TABLE `vnb_framework_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
