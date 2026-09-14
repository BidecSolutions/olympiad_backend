@echo off
set PATH=C:\laragon\bin\php\php-8.3.33-Win32-vs16-x64;C:\laragon\bin\composer;C:\laragon\bin\git\bin;C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin;%PATH%
cd /d "%~dp0"

tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if not "%ERRORLEVEL%"=="0" (
  echo Starting MySQL...
  start "" /B "C:\laragon\bin\mysql\mysql-8.4.3-winx64\bin\mysqld.exe" --defaults-file="C:\laragon\bin\mysql\mysql-8.4.3-winx64\my.ini"
  timeout /t 3 /nobreak >nul
)

echo Starting Olympiads API at http://0.0.0.0:8000/api
echo Phone se connect: http://192.168.200.164:8000/api
echo Database: MySQL olympiads @ 127.0.0.1:3306
echo.
echo Running migrations + seed (ensures Super Admin exists)...
php artisan migrate --force
php artisan db:seed --force
php artisan config:clear
php artisan cache:clear
echo.
php artisan serve --host=0.0.0.0 --port=8000
pause
