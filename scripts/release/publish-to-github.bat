@echo off
setlocal

set "SCRIPT_DIR=%~dp0"
powershell -NoProfile -ExecutionPolicy Bypass -File "%SCRIPT_DIR%publish-to-github.ps1" %*
set ERR=%ERRORLEVEL%
if %ERR% NEQ 0 (
  echo [fail] GitHub publish failed with exit code %ERR%
  exit /b %ERR%
)

echo [ok] GitHub publish completed.
endlocal
