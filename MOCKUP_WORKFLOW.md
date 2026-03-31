# Mockup Workflow (Laravel + SQLite, No CI)

Dokumen ini adalah jalur resmi pengembangan untuk kebutuhan demo.

## Scope
- Tujuan: simulasi login + CRUD untuk demo.
- Tidak menggunakan pipeline CI/CD.
- Tidak mengejar hardening production.

## Daily Run (Local)
1. Copy env sekali saja:
   - `cp .env.example .env`
2. Generate key:
   - `php artisan key:generate`
3. Siapkan file SQLite:
   - `New-Item -ItemType File -Path database/database.sqlite -Force` (PowerShell)
4. Migrasi database:
   - `php artisan migrate`
5. Jalankan server:
   - `php artisan serve`

## Demo Script Minimum
1. Register user via `POST /api/auth/register`
2. Login via `POST /api/auth/login`
3. Ambil token, akses endpoint protected
4. Lakukan CRUD pada `employees`
5. Tunjukkan update dan delete berhasil

## Reset Data Cepat
- `php artisan migrate:fresh`

## Definition of Done (Mockup)
- Auth login/logout berjalan
- Minimal 1 modul CRUD berjalan end-to-end
- Demo script bisa diulang tanpa setup tambahan selain command di atas
- Tidak ada ketergantungan ke CI/CD
