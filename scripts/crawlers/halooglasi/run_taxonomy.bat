@echo off
setlocal ENABLEDELAYEDEXPANSION

set "SCRIPT_DIR=%~dp0"
for %%I in ("%SCRIPT_DIR%..\..\..") do set "REPO_ROOT=%%~fI"
set "VENV_PY=%REPO_ROOT%\.venv\Scripts\python.exe"
set "BUILDER=%REPO_ROOT%\scripts\crawlers\halooglasi\taxonomy_builder.py"

if not exist "%VENV_PY%" (
  echo [warn] venv not found, creating one
  set "PY=C:\Users\sam\AppData\Local\Programs\Python\Python313\python.exe"
  if not exist "%PY%" set "PY=py -3"
  "%PY%" -m venv "%REPO_ROOT%\.venv"
)

"%VENV_PY%" "%BUILDER%" %*
set ERR=%ERRORLEVEL%
if %ERR% NEQ 0 (
  echo [fail] Taxonomy builder exited with code %ERR%
  exit /b %ERR%
)

echo [ok] Taxonomy built. See output\taxonomy.json and output\urls_by_category.jsonl
endlocal
