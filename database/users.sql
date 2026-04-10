-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Apr 2026 pada 17.30
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
(14, 'Ahnaf Fathan', 'fathan@vnb.local', NULL, '$2y$12$Zdp7TWDotEMT2Pc9TfQPXeuYTjbD8KsCiQfQy8ysu8tmrUMBqZriC', 'eyJpdiI6IlViWHBDeEp4NllzTHdLbHhpb1ZLdFE9PSIsInZhbHVlIjoiQ3lLa2FSSHBQU1NRYTFuMW9pM3JFdz09IiwibWFjIjoiNzE0Y2IyYWMwYmYxYWIzNzhiOTg2MWMzNWViNmE4NDU5OGNmNTQ1MmU4OWMyYThlZDllYTc2OTVlNDdlZDU3MyIsInRhZyI6IiJ9', '2026-04-07 09:49:34', '081234567890', NULL, 'active', 2, NULL, '2026-04-07 09:49:34', '2026-04-07 09:49:34'),
(15, 'Regina Dwi', 'rere@vnb.local', NULL, '$2y$12$wBfJRW4ceHS0ELn9H6h7Xuw6uBxSg0.NDqqn5oYc1PX6VFlgCFw7O', 'eyJpdiI6Ing3elloMDlJMWt1bS9OS2Q3N3dZTEE9PSIsInZhbHVlIjoiOGNVNzZoR1k4akczQ2ZjUVRUZlhqdz09IiwibWFjIjoiNWQzNmRkMTA1YzBiMTk0MGJkODVlNzFiODQ1Y2QxMGFiZWQ1NjA2ZDIzNmU1M2I2ZmE3ZmVkMjY0OGUxYjdiNiIsInRhZyI6IiJ9', '2026-04-07 09:49:55', '082123456788', NULL, 'active', 3, NULL, '2026-04-07 09:49:55', '2026-04-07 09:49:55');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_employee_id_foreign` (`employee_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
