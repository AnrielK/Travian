@echo off
rem OpenVillage for XAMPP on Windows: starts the game with the PHP of XAMPP, see xampp\README.md.
rem   xampp\start.bat [--port=8080] [--no-browser]
rem PHP is taken from %%XAMPP_PHP%%, %%XAMPP_HOME%%\php, C:\xampp, D:\xampp, E:\xampp or the PATH.
setlocal
set "OVPHP="
if defined XAMPP_PHP set "OVPHP=%XAMPP_PHP%"
if not defined OVPHP if defined XAMPP_HOME if exist "%XAMPP_HOME%\php\php.exe" set "OVPHP=%XAMPP_HOME%\php\php.exe"
if not defined OVPHP for %%D in (C D E) do if not defined OVPHP if exist "%%D:\xampp\php\php.exe" set "OVPHP=%%D:\xampp\php\php.exe"
if not defined OVPHP for %%P in (php.exe) do if not "%%~$PATH:P"=="" set "OVPHP=%%~$PATH:P"
if not defined OVPHP (
    echo CHYBA: PHP nebylo nalezeno. Nainstaluj XAMPP do C:\xampp nebo nastav XAMPP_PHP=cesta\k\php.exe
    pause
    exit /b 1
)
title OpenVillage
"%OVPHP%" "%~dp0launcher.php" %*
pause
