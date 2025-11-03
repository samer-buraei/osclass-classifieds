@echo off
call "%~dp0ports.env.bat"
call "%~dp0start-backend.bat"
timeout /t 2 /nobreak >nul
call "%~dp0start-frontend.bat"

echo Both servers launching in separate windows using FRONTEND_PORT=%FRONTEND_PORT% and BACKEND_PORT=%BACKEND_PORT%.


