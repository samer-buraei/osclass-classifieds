@echo off
setlocal ENABLEDELAYEDEXPANSION

set "SCRIPT_DIR=%~dp0"
for %%I in ("%SCRIPT_DIR%..\..\..") do set "REPO_ROOT=%%~fI"
set "VENV_PY=%REPO_ROOT%\.venv\Scripts\python.exe"
set "HARVESTER=%REPO_ROOT%\scripts\crawlers\halooglasi\ads_harvester.py"

if not exist "%VENV_PY%" (
  echo [warn] venv not found, creating one
  set "PY=C:\Users\sam\AppData\Local\Programs\Python\Python313\python.exe"
  if not exist "%PY%" set "PY=py -3"
  "%PY%" -m venv "%REPO_ROOT%\.venv"
)

REM Defaults: no images downloaded; include image URLs only if requested
"%VENV_PY%" "%HARVESTER%" %*
set ERR=%ERRORLEVEL%
if %ERR% NEQ 0 (
  echo [fail] Ads harvester exited with code %ERR%
  exit /b %ERR%
)

echo [ok] Ads harvesting finished. See output folder for ads.jsonl
endlocal
