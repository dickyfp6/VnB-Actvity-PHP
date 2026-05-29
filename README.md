# WisCore
## Wismilak Core and Score

**Wismilak PT** | Built with Laravel (Mockup Mode)  
Status: 🚀 **Demo Development (No CI)** | Last Updated: March 2026

---

## 📋 Project Overview

**WisCore** adalah pusat pengelolaan nilai inti perusahaan sekaligus alat pengukuran kinerja berbasis data. Platform ini mendukung pengambilan keputusan melalui modul VnB dan STAR yang terstruktur:
- **Fase 1**: Bulan 1–3 (Stabilisasi)
- **Fase 2**: Bulan 4–6 (Pengembangan)
- **Fase 3**: Bulan 7–12 (Mastery)

### Key Features
✅ Import karyawan baru dari Excel  
✅ Automated VnB activation (post-induction)  
✅ Planning & approval workflow per fase  
✅ Evidence upload & verification  
✅ Manager approval & presentasi  
✅ Progress tracking & dashboard  
✅ Automated notifications (Email → WhatsApp → PCX)  
✅ Audit logs & compliance  
✅ Login & CRUD simulation for demo use  

---

## 🏗️ Architecture

### Stack
- **Backend**: Laravel 11 (PHP 8.2)
- **Database (Default)**: SQLite
- **Cache/Queue (Default)**: File + Sync
- **Deployment Goal**: Lightweight free hosting for demo
- **CI/CD**: Not used for mockup workflow

### Project Structure
```
vnb-employee-app/
├── app/Models/                 # Eloquent models
├── app/Http/Controllers/Api/   # API endpoints
├── app/Http/Requests/          # Form validation
├── database/migrations/        # Schema definitions
├── routes/api.php              # REST endpoints
├── resources/views/            # Blade views
└── routes/web.php              # Web routes
```

---

## 🚀 Quick Start

### Prerequisites (Mockup Workflow)
- PHP 8.2+
- Composer
- SQLite (bundled with PHP distribution in most environments)

### Setup Lokal (Recommended)
```bash
# Clone atau buat folder
cd "VnB WebApp PHP"

# Copy environment
cp .env.example .env

# Install dependencies
composer install

# Setup database
php artisan key:generate
php artisan migrate
php artisan serve --host=127.0.0.1 --port=8000

# API ready at http://127.0.0.1:8000/api
```

---

## 📊 Database Schema

Lihat file migrations di `/database/migrations/` untuk:
- employees (karyawan)
- vnb_periods (3 fase per karyawan)
- vnb_plans (planning per fase)
- vnb_plan_items (activities)
- vnb_evidences (uploads)
- notifications & activity_logs
- Master data (divisions, departments, positions)

---

## 🔌 API Endpoints

Semua endpoint prefix: `/api`

```
GET    /employees              # List employees
POST   /employees              # Create employee
GET    /employees/{id}         # Get detail
PUT    /employees/{id}         # Update
DELETE /employees/{id}         # Delete
POST   /employees/import       # Import Excel
POST   /employees/{id}/activate-vnb  # Activate VnB

POST   /vnb/plans              # Create plan
POST   /vnb/plans/{id}/approve # Manager approval
POST   /vnb/plans/{id}/items/{item}/evidence  # Upload bukti

GET    /dashboard/summary      # Progress overview
POST   /reports/export         # Export PDF/Excel
```

---

## 🧪 Testing (Optional for mockup)

```bash
# Run all tests
composer test

# With coverage
composer test:coverage

# Specific test
php artisan test tests/Feature/ImportTest.php
```

---

## 📝 Linting & Code Quality (Optional)

```bash
# Check style
composer lint

# Fix automatically
composer lint:fix

# Static analysis
composer stan
```

---

## 🚀 Deployment (Demo Only)

Gunakan hosting gratis yang mendukung Laravel untuk kebutuhan demo.
Pipeline CI/CD tidak digunakan pada mode mockup ini agar workflow tetap sederhana.

---

## 📞 Documentation

- **README.md** (ini) — Overview
- **MOCKUP_WORKFLOW.md** — Workflow resmi demo (SQLite + No CI)
- **PUBLIC_DEMO_OPTIONS.md** — Matriks opsi hosting demo + rekomendasi jalur publik
- **RUN_PUBLIC_DEMO.bat** — Script cepat untuk public demo via tunnel
- **QUICKSTART.md** — Quick reference

---

## 🤝 Contributing

1. Create feature branch: `git checkout -b feat/new-feature`
2. Write tests first (TDD)
3. Implement feature
4. Run tests: `composer test`
5. Check style: `composer lint:fix`
6. Commit & push PR

---

**Made with ❤️ for Wismilak**  
Last Updated: March 5, 2026
