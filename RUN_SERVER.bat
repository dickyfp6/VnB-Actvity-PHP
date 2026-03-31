@echo off
REM ========================================
REM Laravel Development Server Starter
REM ========================================
chcp 65001 > nul
cd /d "%~dp0"

echo.
echo ╔════════════════════════════════════════╗
echo ║  VnB WebApp PHP - Development Server   ║
echo ╚════════════════════════════════════════╝
echo.

REM Check if vendor folder exists
if not exist vendor (
    echo 📦 Installing dependencies...
    call composer install
)

REM Check if .env exists
if not exist .env (
    echo 🔑 Setting up environment...
    if exist .env.example (
        copy .env.example .env > nul
    )
    call php artisan key:generate
)
 
REM Start Laravel server
echo.
echo ✅ Starting Laravel Development Server...
echo.
echo 🌐 Access your website at: http://127.0.0.1:8000
echo.
echo Press Ctrl+C to stop the server...
echo.

php artisan serve
