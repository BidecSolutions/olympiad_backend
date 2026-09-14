@echo off
set BASEDIR=C:\laragon\bin\mysql\mysql-8.4.3-winx64
set PATH=%BASEDIR%\bin;%PATH%

tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I /N "mysqld.exe">NUL
if "%ERRORLEVEL%"=="0" (
  echo MySQL is already running.
) else (
  echo Starting MySQL...
  start "" /B "%BASEDIR%\bin\mysqld.exe" --defaults-file="%BASEDIR%\my.ini"
  timeout /t 3 /nobreak >nul
)

echo MySQL ready on 127.0.0.1:3306
echo Database: olympiads
pause
