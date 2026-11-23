@echo off
title Watheeq ERP Server - Stop

echo ========================================================
echo        Stopping Watheeq ERP Server (وثيق ERP)
echo ========================================================
echo.

set FOUND=0
for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":8000" ^| findstr "LISTENING"') do (
    echo [*] Found server process PID: %%a
    taskkill /F /PID %%a >nul 2>&1
    set FOUND=1
)

if "%FOUND%"=="1" (
    echo [OK] Watheeq ERP server stopped successfully.
) else (
    echo [!] Server is not running on port 8000.
)

echo.
timeout /t 2 >nul
