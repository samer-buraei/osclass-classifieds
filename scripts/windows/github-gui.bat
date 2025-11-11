@echo off
setlocal
set PS=%~dp0github-gui.ps1
powershell -NoProfile -ExecutionPolicy Bypass -File "%PS%"
endlocal


