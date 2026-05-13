# Panduan Sinkronisasi Employee dari Tabel Sync Source

## 📋 Ringkasan Masalah & Solusi

### Masalah Sebelumnya
1. Manager data di tabel employee tidak tersinkronisasi dengan benar
2. Manager ID bernilai NULL atau "-" setelah sinkronisasi
3. HrisController tidak menggunakan nama manager dari `sync_source_employees`

### Solusi yang Diterapkan
1. **Perbaiki SyncEmployeesSeeder**: Sekarang melakukan 2 pass untuk resolve manager IDs
2. **Perbaiki HrisController**: Menggunakan manager names dari source data, bukan auto-resolve
3. **Buat Command Sinkronisasi**: `artisan employees:sync-from-source` untuk update existing data

---

## 🔧 Cara Melakukan Sinkronisasi

### Opsi 1: Sinkronisasi Awal via Seeder (Destructive)
Jika Anda ingin reset semua data employee dari scratch dan resync:

```bash
# Jalankan seeder yang sudah diperbaiki
php artisan db:seed --class=SyncSourceEmployeesSeeder

# Atau reset semua database
php artisan migrate:refresh --seed
```

⚠️ **PERINGATAN**: Ini akan menghapus semua data employee yang ada!

### Opsi 2: Sinkronisasi Update (Non-Destructive) ✅ RECOMMENDED
Jika Anda sudah punya data employee dan hanya ingin update manager references:

```bash
# Gunakan command baru untuk update
php artisan employees:sync-from-source

# Atau dengan --force flag untuk skip confirmation
php artisan employees:sync-from-source --force
```

✅ **SAFE**: Command ini hanya mengupdate manager references, tidak menghapus data apapun

---

## 📊 Data Flow Sinkronisasi

```
sync_source_employees
    ↓
    (berisi: manager_functional, manager_operational as strings)
    ↓
employees
    ↓
    (berisi: manager_functional_id, manager_functional - resolved from names)
    ↓
UI/API Response
    ↓
    (menampilkan nama dan ID manager dengan benar)
```

---

## 🔍 Verifikasi Sinkronisasi

### 1. Cek apakah manager sudah tersinkronisasi di tabel sync_source_employees:

```sql
SELECT employee_number, name, manager_functional, manager_operational 
FROM sync_source_employees 
LIMIT 5;
```

Expected output: manager names should be populated (not "-")

### 2. Cek apakah employee table sudah punya manager IDs:

```sql
SELECT e.employee_number, e.name, e.manager_functional_id, 
       e.manager_functional, mf.name as manager_name
FROM employees e
LEFT JOIN managers mf ON e.manager_functional_id = mf.id
LIMIT 5;
```

Expected output: manager_functional_id harus terisi, bukan NULL

### 3. Test API endpoint untuk list employees:

```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     http://localhost/api/employees \
     | jq '.data[0] | {manager_functional, manager_operational, manager_functional_id, manager_operational_id}'
```

Expected output:
```json
{
  "manager_functional": "Direktur Utama",
  "manager_operational": "Manager User",
  "manager_functional_id": 1,
  "manager_operational_id": 2
}
```

---

## 🐛 Troubleshooting

### Masalah: Manager masih "-" setelah sinkronisasi

**Kemungkinan Penyebab:**
1. Manager name di `sync_source_employees` tidak sesuai dengan nama di `employees` table
2. Manager belum dibuat sebagai employee record

**Solusi:**
```bash
# Check manager names di source
SELECT DISTINCT manager_functional, manager_operational 
FROM sync_source_employees;

# Check employee names
SELECT id, name FROM employees WHERE status = 'Aktif';

# Names harus persis sama (case-sensitive di query)
```

### Masalah: Command `employees:sync-from-source` tidak ditemukan

**Solusi:**
1. Pastikan file `app/Console/Commands/SyncEmployeesFromSource.php` sudah ada
2. Jalankan: `php artisan list` untuk melihat daftar commands
3. Jika masih tidak muncul: `php artisan cache:clear` & `composer dump-autoload`

---

## 📝 Catatan Implementasi

### Kolom yang Digunakan
- `sync_source_employees.manager_functional` (string) → nama manager fungsional
- `sync_source_employees.manager_operational` (string) → nama manager operasional
- `employees.manager_functional_id` (int) → ID reference ke employees table
- `employees.manager_operational_id` (int) → ID reference ke employees table
- `employees.manager_functional` (string) → display name (cached)
- `employees.manager_operational` (string) → display name (cached)

### Automatic Manager Sync via Observer
Employee model memiliki observer yang otomatis:
- Update `manager_functional` & `manager_operational` string values ketika ID berubah
- Trigger manager role sync ketika manager assignment berubah

---

## 🎯 Hasil Akhir

Setelah sinkronisasi berhasil:
1. ✅ Tabel employee adalah cerminan dari tabel sync_source_employees
2. ✅ Manager references ter-resolve dengan benar ke employee IDs
3. ✅ Manager names ditampilkan di UI tanpa "-"
4. ✅ Kolom manager terlihat di employee table listing (jika frontend sudah update)
