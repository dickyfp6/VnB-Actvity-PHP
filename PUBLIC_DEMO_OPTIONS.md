# Public Demo Options (Laravel + SQLite)

Dokumen ini memilih jalur publik paling realistis untuk mode mockup.

## Ringkasan Keputusan

**Rekomendasi utama (dipilih):**
- Jalankan aplikasi lokal (`php artisan serve`) + expose URL publik sementara memakai Cloudflare Quick Tunnel.
- SQLite tetap lokal sehingga flow login + CRUD tetap stabil selama sesi demo.

Alasan:
- Zero-cost dan cepat setup.
- Tidak perlu migrasi ulang ke DB hosted.
- Cocok untuk kebutuhan demo, bukan production.

## Matriks Opsi

| Opsi | Biaya awal | Persistensi data | Setup effort | Risiko utama | Cocok untuk |
|---|---:|---|---|---|---|
| **A1. Local + Cloudflare Quick Tunnel** | Gratis | Persisten di mesin lokal kamu | Rendah | URL sementara, uptime tergantung laptop/internet | Demo cepat live link |
| **A2. Render Free Web Service + SQLite** | Gratis | **Tidak persisten** (filesystem ephemeral) | Sedang | Data bisa hilang saat restart/redeploy/spin-down | Demo stateless singkat |
| **A3. Render Free + Render Postgres Free** | Gratis (dengan limit) | Persisten terbatas | Sedang-tinggi | DB free punya limit/masa aktif; cold start | Demo cloud jangka pendek |

## Fakta Penting (berdasarkan dokumentasi provider)

- Render Free Web Service bisa spin down saat idle (~15 menit) dan butuh waktu spin-up saat akses berikutnya.
- Render Free tidak mendukung persistent disk.
- SQLite di filesystem web service gratis akan berisiko hilang pada restart/redeploy/spin-down.
- Cloudflare Quick Tunnel memang ditujukan untuk testing/development, sesuai use case demo.

## Jalur Eksekusi yang Direkomendasikan (A1)

1. Pastikan app lokal siap:
   - `php artisan migrate`
   - `php artisan serve --host=127.0.0.1 --port=8000`
2. Jalankan tunnel:
   - `cloudflared tunnel --url http://127.0.0.1:8000`
3. Bagikan URL `https://*.trycloudflare.com` ke audience demo.

## Catatan Operasional Demo

- Gunakan data dummy saja.
- Tutup tunnel setelah demo selesai.
- Jika perlu URL stabil jangka lebih panjang, pertimbangkan Cloudflare Tunnel full setup (bukan Quick Tunnel) atau pindah ke opsi cloud DB.

## Trigger Pindah Opsi

Pindah dari A1 ke A3 bila:
- demo butuh uptime berhari-hari,
- butuh data persisten lintas sesi tanpa menyalakan laptop,
- butuh akses multi-user publik secara terus-menerus.
