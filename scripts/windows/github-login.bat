@echo off
setlocal
set PS=%~dp0github-login.ps1
powershell -NoProfile -ExecutionPolicy Bypass -File "%PS%"
endlocal

@echo off
setlocal
set "SCRIPT_DIR=%~dp0"
powershell -NoProfile -ExecutionPolicy Bypass -File "%SCRIPT_DIR%github-login.ps1" %*
set ERR=%ERRORLEVEL%
if %ERR% NEQ 0 (
  echo [fail] GitHub login failed with exit code %ERR%
  exit /b %ERR%
)
echo [ok] GitHub login completed.
endlocal


