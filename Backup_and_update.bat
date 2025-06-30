@echo off
setlocal enabledelayedexpansion

:: Configuration - UPDATE THESE VALUES
set DB_PATH=D:\Parking\Parking\database\database.sqlite
set BACKUP_ROOT=D:\Parking\backups
set GIT_REPO_PATH=D:\Parking\Parking
set GIT_REMOTE=origin
set GIT_BRANCH=ehab

:: Create backup directory with year-month-day structure
for /f "tokens=1-3 delims=/ " %%a in ("%date%") do (
    set day=%%a
    set month=%%b
    set year=%%c
)

:: Format time to HH-MM-SS (24-hour format)
for /f "tokens=1-3 delims=:." %%a in ("%time%") do (
    set hour=%%a
    set minute=%%b
    set second=%%c
)

:: Add leading zeros to single-digit values
if %hour% lss 10 set hour=0%hour%
if %minute% lss 10 set minute=0%minute%
if %second% lss 10 set second=0%second%

:: Remove any spaces in hour (for AM times)
set hour=%hour: =0%

:: Create timestamp and paths
set timestamp=%year%-%month%-%day%_%hour%-%minute%-%second%
set BACKUP_FOLDER=%BACKUP_ROOT%\%year%-%month%\%year%-%month%-%day%
set BACKUP_FILE=database_%timestamp%.sqlite

:: Create backup folder structure
if not exist "%BACKUP_FOLDER%" mkdir "%BACKUP_FOLDER%"

:: Backup SQLite database by copying the file
echo [%time%] Backing up database %DB_PATH%...
copy "%DB_PATH%" "%BACKUP_FOLDER%\%BACKUP_FILE%"

if errorlevel 1 (
    echo [%time%] ERROR: Database backup failed!
    exit /b 1
) else (
    echo [%time%] Success: Database backed up to %BACKUP_FOLDER%\%BACKUP_FILE%
)

:: Update from git repository - More robust git operations
echo [%time%] Checking Git repository status...
cd /d "%GIT_REPO_PATH%"

:: Fetch all changes from remote
echo [%time%] Fetching latest changes from %GIT_REMOTE%...
git fetch %GIT_REMOTE%

if errorlevel 1 (
    echo [%time%] ERROR: Git fetch failed!
    exit /b 1
)

:: Check if we're behind remote
git rev-list --count %GIT_REMOTE%/%GIT_BRANCH..HEAD >nul 2>&1
set behind=%errorlevel%

if %behind% equ 0 (
    echo [%time%] Your branch is up to date with %GIT_REMOTE%/%GIT_BRANCH%
) else (
    echo [%time%] New updates available, pulling changes...
    git pull %GIT_REMOTE% %GIT_BRANCH%
    
    if errorlevel 1 (
        echo [%time%] ERROR: Git pull failed!
        exit /b 1
    )
    
    echo [%time%] Success: Repository updated to latest version
)

:: Verify current commit hash
echo [%time%] Current commit: 
git rev-parse --short HEAD

echo [%time%] All operations completed successfully
pause