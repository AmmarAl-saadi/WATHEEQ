@echo off
title Watheeq ERP Server - Start

set PATH=C:\php-8.3.30;%PATH%

echo ========================================================
echo        Starting Watheeq ERP Server (وثيق ERP)
echo ========================================================
echo.

cd /d "%~dp0watheeq"

if not exist "database\database.sqlite" (
    echo [*] Initializing SQLite database...
    type nul > "database\database.sqlite"
    php artisan migrate --force
)

echo [*] Launching server on: http://127.0.0.1:8000
start http://127.0.0.1:8000

echo.
echo ========================================================
echo Watheeq ERP Server is running. To stop it, close this window or run STOP.bat
echo ========================================================
echo.

php artisan serve --host=0.0.0.0 --port=8000
pause
