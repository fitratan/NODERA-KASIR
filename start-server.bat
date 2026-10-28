@echo off
title KASIR STANDALONE by NODERA
cd /d "%~dp0"

echo ==========================================================
echo           KASIR STANDALONE by NODERA (Landscape POS)      
echo ==========================================================
echo Menjalankan Micro-Server PHP pada port 8088...

set PHP_BIN=php
if exist "bin\php-win\php.exe" (
    set PHP_BIN=bin\php-win\php.exe
)

start http://localhost:8088
%PHP_BIN% -S 0.0.0.0:8088 -t public
pause
