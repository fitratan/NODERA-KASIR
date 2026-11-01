@echo off
title KASIR STANDALONE by NODERA
cd /d "%~dp0"

echo ==========================================================
echo           KASIR STANDALONE by NODERA (Landscape POS)      
echo ==========================================================
echo Menjalankan Micro-Server PHP pada port 8088...

set PHP_BIN=php
set PHP_INI=
if exist "bin\php-win\php.exe" (
    set PHP_BIN=bin\php-win\php.exe
    if exist "bin\php-win\php.ini" (
        set PHP_INI=-c bin\php-win\php.ini
    )
)

:: Multi-threaded CLI server workers (mencegah freeze saat multi-client LAN)
set PHP_CLI_SERVER_WORKERS=4

start http://localhost:8088
%PHP_BIN% %PHP_INI% -S 0.0.0.0:8088 -t public
pause
