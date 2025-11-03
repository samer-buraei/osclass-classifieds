@echo off
setlocal
set PS=%~dp0publish-github.ps1
powershell -NoProfile -ExecutionPolicy Bypass -File "%PS%" %*
endlocal


