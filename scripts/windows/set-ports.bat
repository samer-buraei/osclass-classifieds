@echo off
setlocal EnableDelayedExpansion
set DEF_FE=3000
set DEF_BE=8000

set /p FRONTEND_PORT=Enter frontend port [!DEF_FE!]: 
if "!FRONTEND_PORT!"=="" set FRONTEND_PORT=!DEF_FE!

set /p BACKEND_PORT=Enter backend port [!DEF_BE!]: 
if "!BACKEND_PORT!"=="" set BACKEND_PORT=!DEF_BE!

echo Writing ports.env.bat...
(
  echo @echo off
  echo set FRONTEND_PORT=!FRONTEND_PORT!
  echo set BACKEND_PORT=!BACKEND_PORT!
) > "%~dp0ports.env.bat"

echo Updating FirstDate\.env.local...
(
  echo REACT_APP_SERVER_URL=http://localhost:!BACKEND_PORT!/api/v1
  echo REACT_APP_SOCKET_URL=ws://localhost:!BACKEND_PORT!/api/v1/ws
) > "C:\Users\sam\Desktop\agent builder\open-agent-builder\FirstDate\.env.local"

echo Done. FRONTEND_PORT=!FRONTEND_PORT! BACKEND_PORT=!BACKEND_PORT!
echo Use start-all.bat to launch with these settings.
endlocal


