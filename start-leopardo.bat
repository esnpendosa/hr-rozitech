@echo off
title Leopardo RH - Startup
color 0A

echo ======================================================================
echo                     LEOPARDO RH - LOCAL RUNNER
echo ======================================================================
echo.

set "ROOT_DIR=%~dp0"
set "PHP_BIN=C:\laragon\bin\php\php-8.4.25-Win32-vs17-x64\php.exe"
set "PG_CTL=C:\laragon\bin\postgresql\pgsql\bin\pg_ctl.exe"
set "PG_DATA=C:\laragon\data\postgresql"
set "PG_LOG=C:\laragon\data\postgresql\logfile.log"

echo [1/4] Memeriksa & Menjalankan PostgreSQL...
if exist "%PG_CTL%" (
    "%PG_CTL%" -D "%PG_DATA%" status >nul 2>&1
    if errorlevel 1 (
        echo       Menyalakan service PostgreSQL...
        "%PG_CTL%" -D "%PG_DATA%" -l "%PG_LOG%" start
    ) else (
        echo       PostgreSQL sudah aktif.
    )
) else (
    echo [!] PERINGATAN: Binary PostgreSQL tidak ditemukan di %PG_CTL%
)

echo.
echo [2/4] Menjalankan Backend Laravel API (Port 8000)...
start "Leopardo Backend API (Port 8000)" /min cmd /c "cd /d "%ROOT_DIR%api" && "%PHP_BIN%" -S 127.0.0.1:8000 server.php"

echo.
echo [3/4] Menjalankan Frontend Platform Super-Admin (Port 3001)...
start "Leopardo Admin Dashboard (Port 3001)" /min cmd /c "cd /d "%ROOT_DIR%front\admin-dashboard" && npm run dev"

echo.
echo [4/4] Menjalankan Client Web Portal / HR & Absensi (Port 3000)...
start "Leopardo Client Web Portal (Port 3000)" /min cmd /c "cd /d "%ROOT_DIR%front\web" && npm run dev -- -p 3000"

echo.
echo ======================================================================
echo                    SEMUA LAYANAN SEDANG BERJALAN!
echo ======================================================================
echo.
echo  1. Portal Client / HR / Karyawan (Fitur Absensi & Tugas):
echo     URL      : http://localhost:3000/
echo     Login    : http://localhost:3000/auth/login
echo     Akun HR  : fatima.meziane@techcorp-algerie.dz
echo     Akun Dir : ahmed.benali@techcorp-algerie.dz
echo     Password : password123
echo.
echo  2. Platform Super-Admin Dashboard (Manajemen SaaS):
echo     URL      : http://localhost:3001/
echo     Akun     : admin@leopardo-rh.com
echo     Password : password123
echo.
echo  3. Backend Laravel API : http://localhost:8000/api/v1/health
echo.
echo  Untuk mematikan semua layanan, jalankan file stop-leopardo.bat
echo ======================================================================
echo.

timeout /t 3 /nobreak >nul
start http://localhost:3000/
start http://localhost:3001/

echo Browser telah dibuka.
echo Tekan sembarang tombol untuk menutup jendela ini (layanan tetap berjalan di background).
pause >nul

