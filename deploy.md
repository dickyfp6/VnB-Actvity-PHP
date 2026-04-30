# Deploy Guide

Panduan singkat untuk workflow **Local -> GitHub -> Railway** pada project ini.

## Tujuan

- Local dipakai untuk develop dan ubah data MySQL/phpMyAdmin.
- GitHub dipakai sebagai source of truth untuk code dan seed data yang sudah diekspor.
- Railway dipakai untuk staging/deploy.

## Prinsip Penting

Kalau kamu sering mengubah data di lokal, data itu **tidak bisa otomatis ikut naik ke GitHub / Railway** hanya dengan `git push`.

Yang bisa diandalkan adalah:

- jadikan data penting sebagai seeder versioned di folder `database/seeders/`
- commit seedernya ke GitHub
- saat deploy di Railway, jalankan `migrate --force` dan, kalau perlu, `db:seed --force`

Kalau kamu ingin menyalin isi tabel lokal jadi seeder otomatis, kamu bisa pakai tooling seperti `iseed`, tapi itu tetap harus dijalankan di lokal dulu lalu hasil file seed-nya di-commit.

## 1) Yang Dikerjakan di Lokal

### Kalau kamu ubah struktur database

```powershell
php artisan make:migration nama_migration_baru
php artisan migrate
```

### Kalau kamu ubah data referensi / demo data

```powershell
php artisan db:seed
```

Kalau seeding harus diverifikasi dari nol, jalankan:

```powershell
php artisan migrate:fresh --seed
```

Pakai ini hanya di lokal, karena perintah itu akan menghapus semua tabel lalu membuat ulang database.

### Kalau kamu ingin snapshot data lokal jadi seeder

Alurnya seperti ini:

1. Export / generate seeder dari data lokal.
2. Simpan hasilnya ke `database/seeders/`.
3. Commit file seeder itu ke GitHub.

Jika memakai `iseed`, install dan jalankan di lokal dulu, lalu commit hasil file yang dihasilkan.

## 2) Command Git Sebelum Push

```powershell
git status
git add .
git commit -m "Update seed data and deployment flow"
git push origin main
```

Ganti `main` kalau branch kamu bukan `main`.

## 3) Railway: Tidak Perlu SSH Manual Kalau Pakai Pre-Deploy

Kamu **tidak perlu SSH ke Railway** kalau command deploy sudah diisi di dashboard Railway.

### Rekomendasi paling aman

Isi **Pre-Deploy Step** dengan:

```powershell
php artisan migrate --force
```

Kalau kamu memang ingin Railway juga menanam data seed terbaru setiap deploy, pakai:

```powershell
php artisan migrate --force && php artisan db:seed --force
```

Catatan:

- `--force` wajib untuk non-interaktif di Railway.
- Jangan pakai `migrate:fresh` di Railway kecuali kamu memang mau reset semua data staging.

## 4) Kalau Railway Perlu Start Command

Kalau service Railway minta **Custom Start Command**, isi dengan command Laravel server yang listen ke port Railway:

```powershell
php artisan serve --host=0.0.0.0 --port=$PORT
```

Kalau Railway kamu memakai shell yang tidak membaca `$PORT` dengan benar, pakai format yang disediakan Railway untuk environment port di service itu.

## 5) Workflow Yang Disarankan

### Opsi A: Staging aman, data tidak di-reset

Pakai ini kalau Railway hanya untuk testing normal.

```powershell
php artisan migrate --force
```

Lalu seed hanya jika ada data referensi baru yang memang harus ikut deploy.

### Opsi B: Staging demo penuh, data ikut dibangun ulang

Pakai ini kalau Railway isinya memang demo/staging dan boleh di-reset.

```powershell
php artisan migrate --force && php artisan db:seed --force
```

### Opsi C: Full reset lokal saja

Pakai ini hanya di local development.

```powershell
php artisan migrate:fresh --seed
```

## 6) Saran Praktis Untuk Project Ini

Karena repo ini sudah punya `DatabaseSeeder` dan beberapa seeder spesifik, alur yang paling rapi adalah:

1. ubah data penting di lokal
2. pindahkan data itu ke seeder atau update seeder yang sudah ada
3. commit seedernya
4. di Railway pakai pre-deploy `php artisan migrate --force`
5. tambahkan `php artisan db:seed --force` kalau staging memang harus ikut terisi data terbaru

## 7) Ringkasan Jawaban Singkat

- Ya, migration bisa ditaruh di **pre-deploy** Railway.
- Tidak perlu SSH manual kalau command deploy sudah diset di Railway.
- Untuk data lokal MySQL, yang aman adalah **ekspor ke seeder**, bukan mengandalkan database lokal ikut tersinkron otomatis.
