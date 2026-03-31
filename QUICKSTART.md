# QUICKSTART (Mockup Mode)

Panduan cepat untuk menjalankan demo login + CRUD dengan Laravel + SQLite.

## 1) Start Lokal

```powershell
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve --host=127.0.0.1 --port=8000
```

Aplikasi berjalan di: `http://127.0.0.1:8000`

## 2) Buat Link Publik (Opsional)

Jika `cloudflared` sudah terpasang:

```powershell
RUN_PUBLIC_DEMO.bat
```

Atau manual:

```powershell
cloudflared tunnel --url http://127.0.0.1:8000
```

## 3) Demo Script API (Minimum)

### Register
```http
POST /api/auth/register
Content-Type: application/json

{
  "name": "Demo User",
  "email": "demo@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

### Login
```http
POST /api/auth/login
Content-Type: application/json

{
  "email": "demo@example.com",
  "password": "password"
}
```

Simpan `token` dari response, lalu gunakan header:

```http
Authorization: Bearer <TOKEN>
```

### Create Employee
```http
POST /api/employees
Authorization: Bearer <TOKEN>
Content-Type: application/json

{
  "employee_number": "EMP-D01",
  "name": "John Demo",
  "email": "john.demo@example.com",
  "date_joined": "2026-03-13",
  "company": "Wismilak PT"
}
```

### Read Employees
```http
GET /api/employees
Authorization: Bearer <TOKEN>
```

### Update Employee
```http
PUT /api/employees/1
Authorization: Bearer <TOKEN>
Content-Type: application/json

{
  "name": "John Demo Updated",
  "company": "Wismilak Group"
}
```

### Delete Employee
```http
DELETE /api/employees/1
Authorization: Bearer <TOKEN>
```

## 4) Reset Data Demo

```powershell
php artisan migrate:fresh
```

## 5) Scope Reminder

- Fokus mockup/demo, bukan production hardening.
- Tanpa pipeline CI/CD.
- SQLite dipakai sebagai default DB.
