@echo off
title Leopardo RH - Stop Services
color 0C

echo ======================================================================
echo                    LEOPARDO RH - STOP SERVICES
echo ======================================================================
echo.

set "PG_CTL=C:\laragon\bin\postgresql\pgsql\bin\pg_ctl.exe"
set "PG_DATA=C:\laragon\data\postgresql"

echo [1/4] Menghentikan Client Web Portal (Port 3000)...
for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":3000 " ^| findstr "LISTENING"') do (
    echo       Menghentikan PID %%a (Port 3000)...
    taskkill /f /pid %%a >nul 2>&1
)

echo.
echo [2/4] Menghentikan Frontend Super-Admin (Port 3001)...
for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":3001 " ^| findstr "LISTENING"') do (
    echo       Menghentikan PID %%a (Port 3001)...
    taskkill /f /pid %%a >nul 2>&1
)

echo.
echo [3/4] Menghentikan Backend Laravel API (Port 8000)...
for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":8000 " ^| findstr "LISTENING"') do (
    echo       Menghentikan PID %%a (Port 8000)...
    taskkill /f /pid %%a >nul 2>&1
)

echo.
echo [4/4] Menghentikan database PostgreSQL (Port 5432)...
if exist "%PG_CTL%" (
    "%PG_CTL%" -D "%PG_DATA%" stop -m fast
) else (
    for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":5432 " ^| findstr "LISTENING"') do (
        taskkill /f /pid %%a >nul 2>&1
    )
)

echo.
echo ======================================================================
echo                 SEMUA LAYANAN TELAH DIHENTIKAN!
echo ======================================================================
echo.
pause
