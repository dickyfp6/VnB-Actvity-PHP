# ✅ Perbaikan Sinkronisasi Employee & Manager

## 📝 Ringkasan Perubahan

Masalah yang diselesaikan:
1. ❌ Manager data di tabel employee menjadi "-" setelah sinkronisasi  
2. ❌ HrisController tidak menggunakan manager names dari `sync_source_employees`
3. ❌ Kolom Manager tidak ditampilkan di UI tabel employee

---

## 🔧 Perubahan yang Dilakukan

### 1. **SyncEmployeesSeeder.php** (Perbaikan Seeder)
   - Mengubah proses menjadi 2-pass untuk resolve manager IDs
   - Pass 1: Insert semua employee + map manager names
   - Pass 2: Update manager_functional_id & manager_operational_id berdasarkan nama
   - **Result**: Manager IDs ter-resolve dengan benar di tabel employees

### 2. **HrisController.php** (Perbaikan API Sinkronisasi)
   - Memodifikasi `mapSourceToEmployeePayload()` untuk menggunakan manager names dari source data
   - Menambahkan method `resolveManagerIdByName()` untuk mencari manager ID berdasarkan nama
   - **Result**: Saat sinkronisasi dari HRIS, manager references ter-resolve otomatis

### 3. **app/Console/Commands/SyncEmployeesFromSource.php** (Command Baru)
   - Membuat Artisan command untuk sinkronisasi update (non-destructive)
   - Command dapat dijalankan dengan `php artisan employees:sync-from-source`
   - **Result**: Bisa update manager IDs tanpa menghapus data existing

### 4. **resources/views/employees/index.blade.php** (UI Improvement)
   - Menambahkan kolom "Manager Fungsional" dan "Manager Operasional" ke tabel employee
   - Memperbarui colspan dari 13 menjadi 15
   - Menambahkan manager data ke search index
   - **Result**: Manager column terlihat di UI dengan benar

---

## 🚀 Cara Menggunakan

### Sinkronisasi Awal (Destructive - Reset All Data)
```bash
# Jalankan seeder
php artisan db:seed --class=SyncSourceEmployeesSeeder

# Atau full reset
php artisan migrate:refresh --seed
```

### Sinkronisasi Update (Safe - Recommended ✅)
```bash
# Update manager references saja
php artisan employees:sync-from-source

# Skip confirmation
php artisan employees:sync-from-source --force
```

### Sinkronisasi via HRIS API
- Buka halaman HRIS Data Sync
- Pilih employees untuk di-sync
- Manager references akan otomatis ter-resolve dari nama

---

## 📊 Data Flow Setelah Perbaikan

```
HRIS/HRMS Source
    ↓
sync_source_employees table
  (manager_functional, manager_operational = string names)
    ↓
SyncEmployeesSeeder / HrisController
  (resolve names → find employee IDs)
    ↓
employees table
  (manager_functional_id, manager_operational_id = employee IDs)
  (manager_functional, manager_operational = cached names)
    ↓
EmployeeController API
    ↓
Employee Table UI
  (displays manager names correctly)
```

---

## ✅ Verifikasi Perbaikan

### 1. Database Check
```sql
-- Cek manager IDs ter-resolve
SELECT e.employee_number, e.name, 
       e.manager_functional_id, e.manager_functional,
       e.manager_operational_id, e.manager_operational
FROM employees e
WHERE e.manager_functional_id IS NOT NULL
LIMIT 5;
```

### 2. API Test
```bash
curl -H "Authorization: Bearer YOUR_TOKEN" \
     http://localhost/api/employees \
     | jq '.data[0] | {name, manager_functional, manager_operational}'
```

### 3. UI Check
- Buka halaman Manage Employee
- Perhatikan kolom "Manager Fungsional" dan "Manager Operasional"
- Harusnya menampilkan nama manager, bukan "-"

---

## 📋 Files Modified

1. `/database/seeders/SyncEmployeesSeeder.php` - Perbaiki 2-pass sync logic
2. `/app/Http/Controllers/Api/HrisController.php` - Gunakan manager names dari source
3. `/app/Console/Commands/SyncEmployeesFromSource.php` - Command baru untuk update
4. `/resources/views/employees/index.blade.php` - Tambah kolom manager di UI

---

## 💡 Notes

- Manager yang tidak ditemukan akan bernilai NULL (bukan "-")
- Employee harus sudah terdaftar di tabel employees sebelum bisa dijadikan manager
- Observer pada Employee model otomatis update manager names ketika ID berubah
- Kolom `manager_functional` REQUIRED (NOT NULL), operational OPTIONAL

---

## 🎯 Next Steps (Optional Improvements)

1. Create UI untuk bulk assign manager
2. Add manager validation rules
3. Create dashboard untuk manager hierarchy visualization
4. Add audit log untuk manager assignment changes
