@echo off
setlocal


REM Define database file and backup path
set DB_FILE=C:\Users\AboHadi\Desktop\Center\Center\database\database.sqlite
set BACKUP_PATH=C:\Users\AboHadi\Desktop\Center\Backup\database_backup.sqlite

REM Backup the SQLite database
echo Backing up the SQLite database...
copy "%DB_FILE%" "%BACKUP_PATH%"



REM Get the IPv4 address
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4"') do (
    for /f "tokens=1 delims= " %%b in ("%%a") do (
        set IP=%%b
    )
)

REM Remove any leading/trailing spaces
set IP=%IP: =%

REM Check if the IP address was set correctly
if not defined IP (
    echo Could not find the IPv4 address. Please check your network configuration.
    exit /b
)

echo Starting PHP server on %IP%:8000...

REM Start the PHP server in a new command window
start cmd /k "php artisan serve"

REM Set the URL of your localhost site
set URL=http://%IP%:8000/

REM Allow some time for the server to start
timeout /t 5 /nobreak > NUL

REM Check if the URL is already open in Chrome
start msedge --new-window --app="http://127.0.0.1:8000" || start msedge --start-fullscreen "%URL%"