@echo off
setlocal
call "%~dp0ports.env.bat"
set TITLE=FirstDate Frontend (%FRONTEND_PORT%)
set APP_DIR=C:\Users\sam\Desktop\agent builder\open-agent-builder\FirstDate

echo Starting frontend at %APP_DIR% on port %FRONTEND_PORT% ...
start "%TITLE%" cmd /k cd /d "%APP_DIR%" ^& set PORT=%FRONTEND_PORT% ^& set REACT_APP_SERVER_URL=http://localhost:%BACKEND_PORT%/api/v1 ^& set REACT_APP_SOCKET_URL=ws://localhost:%BACKEND_PORT%/api/v1/ws ^& npm start
endlocal


