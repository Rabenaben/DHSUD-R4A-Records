@echo off
echo ==========================
echo Laravel System Launcher
echo ==========================

:: Get LAN IP
for /f "tokens=14 delims= " %%i in ('ipconfig ^| findstr IPv4') do set IP=%%i

echo.
echo Local Access:
echo http://localhost/records/public
echo.
echo LAN Access:
echo http://%IP%/records/public
echo.

:: Open in browser
start http://localhost/records/public
start http://%IP%/records/public

pause