@echo off
rem OpenVillage for XAMPP on Windows: starts the game with the PHP of XAMPP, see xampp\README.md.
rem   xampp\start.bat [--port=8080] [--no-browser]
rem PHP 8.2+ is taken from XAMPP_PHP, XAMPP_HOME\php, the XAMPP folder this game lies in
rem (e.g. C:\xampp8\htdocs\Travian), C:\xampp*, D:\xampp*, E:\xampp* or the PATH: the first one new enough.
setlocal
set "OVPHP="
set "OVOLD="
if defined XAMPP_PHP call :try "%XAMPP_PHP%"
if defined XAMPP_HOME call :try "%XAMPP_HOME%\php\php.exe"
for %%U in ("%~dp0.." "%~dp0..\.." "%~dp0..\..\.." "%~dp0..\..\..\..") do call :try "%%~fU\php\php.exe"
for %%R in (C D E) do for /d %%X in ("%%R:\xampp*") do call :try "%%~fX\php\php.exe"
for /f "delims=" %%P in ('where php.exe 2^>nul') do call :try "%%P"
if defined OVPHP goto run
echo CHYBA: PHP 8.2 nebo novejsi nebylo nalezeno.
if defined OVOLD echo Nalezeno jen starsi PHP: "%OVOLD%"
echo Nainstaluj XAMPP s PHP 8.2+ nebo nastav cestu: set XAMPP_PHP=C:\xampp\php\php.exe
pause
exit /b 1

:run
title OpenVillage
"%OVPHP%" "%~dp0launcher.php" %*
pause
exit /b

:try
rem Takes %1 when it is a PHP 8.2+ executable and none was found yet.
if defined OVPHP exit /b
if not exist "%~1" exit /b
"%~1" -n -r "exit(version_compare(PHP_VERSION, '8.2.0', '<') ? 1 : 0);" >nul 2>&1
if not errorlevel 1 set "OVPHP=%~1"
if not defined OVPHP if not defined OVOLD set "OVOLD=%~1"
exit /b
