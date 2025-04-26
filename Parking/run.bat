@echo off
title Laravel Desktop App

:: Start PHP server in background
start /B php artisan serve

:: Wait a moment for the server to start
timeout /t 3 /nobreak

:: Start browser in fullscreen mode (using Chrome as an example)
start chrome --start-fullscreen http://127.0.0.1:8000

echo Server is running... Press any key to stop the server.
pause > nul

:: Kill the PHP server process
taskkill /F /IM php.exe
exit