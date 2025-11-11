@echo off
setlocal ENABLEDELAYEDEXPANSION

set "SCRIPT_DIR=%~dp0"
for %%I in ("%SCRIPT_DIR%..\..") do set "REPO_ROOT=%%~fI"
set "VENV_PY=%REPO_ROOT%\.venv\Scripts\python.exe"
set "GUI=%REPO_ROOT%\scripts\gui\crawler_gui.py"

if not exist "%VENV_PY%" (
  echo [info] venv not found, creating one
  set "PY=C:\Users\sam\AppData\Local\Programs\Python\Python313\python.exe"
  if not exist "%PY%" set "PY=py -3"
  "%PY%" -m venv "%REPO_ROOT%\.venv"
)

"%VENV_PY%" "%GUI%"
set ERR=%ERRORLEVEL%
if %ERR% NEQ 0 (
  echo [fail] GUI exited with code %ERR%
  exit /b %ERR%
)
endlocal
