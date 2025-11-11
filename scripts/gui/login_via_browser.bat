@echo off
setlocal ENABLEDELAYEDEXPANSION

set "SCRIPT_DIR=%~dp0"
for %%I in ("%SCRIPT_DIR%..\..") do set "REPO_ROOT=%%~fI"
set "VENV_PY=%REPO_ROOT%\.venv\Scripts\python.exe"
set "LOGIN=%REPO_ROOT%\scripts\gui\login_via_browser.py"

if not exist "%VENV_PY%" (
  echo [info] venv not found, creating one
  set "PY=C:\Users\sam\AppData\Local\Programs\Python\Python313\python.exe"
  if not exist "%PY%" set "PY=py -3"
  "%PY%" -m venv "%REPO_ROOT%\.venv"
)

"%VENV_PY%" "%LOGIN%" %*
set ERR=%ERRORLEVEL%
if %ERR% NEQ 0 (
  echo [fail] Browser login helper exited with code %ERR%
  echo If Playwright is missing, install it inside venv:
  echo   "%VENV_PY%" -m pip install playwright ^&^& "%REPO_ROOT%\.venv\Scripts\playwright.exe" install
  exit /b %ERR%
)
endlocal
