@echo off
chcp 65001 > nul
cd /d "%~dp0"

echo.
echo ╔════════════════════════════════════════════════════╗
echo ║  VnB Public Demo Mode (Local + Quick Tunnel)      ║
echo ╚════════════════════════════════════════════════════╝
echo.

if not exist vendor (
    echo Installing dependencies...
    call composer install
)

if not exist .env (
    echo Creating .env from template...
    copy .env.example .env > nul
    call php artisan key:generate
)

if not exist database\database.sqlite (
    echo Creating SQLite file...
    type nul > database\database.sqlite
)

echo Running migrations...
call php artisan migrate --force

where cloudflared >nul 2>&1
if errorlevel 1 (
    echo.
    echo cloudflared is not installed or not in PATH.
    echo Install it first, then run:
    echo   cloudflared tunnel --url http://127.0.0.1:8000
    echo.
    echo Starting local server only...
    php artisan serve --host=127.0.0.1 --port=8000
    goto :eof
)

echo.
echo Starting Laravel server in a new window...
start "Laravel Server" cmd /k php artisan serve --host=127.0.0.1 --port=8000

timeout /t 3 >nul

echo.
echo Starting Cloudflare Quick Tunnel...
echo Share the generated trycloudflare URL for your demo.
echo Press Ctrl+C to stop tunnel.
echo.
cloudflared tunnel --url http://127.0.0.1:8000
