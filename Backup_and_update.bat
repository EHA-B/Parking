@echo off
setlocal enabledelayedexpansion

:: Configuration
set DB_NAME=D:\Parking\Parking\database\database.sqlite
set BACKUP_FOLDER=backup
set GIT_REPO_PATH=https://github.com/EHA-B/Parking.git

:: Create backup folder if it doesn't exist
if not exist "%BACKUP_FOLDER%" mkdir "%BACKUP_FOLDER%"

:: Get current date and time for backup filename
for /f "tokens=1-6 delims=/ " %%a in ("%date% %time%") do (
    set year=%%c
    set month=%%a
    set day=%%b
    set hour=%%d
    set minute=%%e
    set second=%%f
)

:: Remove milliseconds from time (if present)
set hour=%hour: =0%
set minute=%minute: =0%
set second=%second: =0%

:: Format datetime as YYYY-MM-DD_HH-MM-SS
set timestamp=%year%-%month%-%day%_%hour%-%minute%-%second%

:: Backup database
echo Backing up database...
mysqldump -u %DB_USER% -p%DB_PASS% %DB_NAME% > "%BACKUP_FOLDER%\%DB_NAME%_%timestamp%.sqlite"

if errorlevel 1 (
    echo Error occurred during database backup
    exit /b 1
) else (
    echo Database backed up successfully to %BACKUP_FOLDER%\%DB_NAME%_%timestamp%.sqlite
)

:: Update from git
echo Updating from git repository...
cd /d "%GIT_REPO_PATH%"
git add .
git commit -m "Backup and update"
git pull origin saleem

if errorlevel 1 (
    echo Error occurred during git update
    exit /b 1
) else (
    echo Git update completed successfully
)

echo All operations completed
pause