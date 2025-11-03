@echo off
setlocal
call "%~dp0ports.env.bat"
set TITLE=FirstDate Backend (%BACKEND_PORT%)
set SRV_DIR=C:\Users\sam\Desktop\agent builder\open-agent-builder\server

echo Starting backend at %SRV_DIR% on port %BACKEND_PORT% ...
start "%TITLE%" cmd /k cd /d "%SRV_DIR%" ^& set PORT=%BACKEND_PORT% ^& npm start
endlocal


