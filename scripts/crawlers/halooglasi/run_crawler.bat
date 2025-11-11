@echo off
setlocal ENABLEDELAYEDEXPANSION

REM Determine absolute repo root from this script's directory
set "SCRIPT_DIR=%~dp0"
for %%I in ("%SCRIPT_DIR%..\..\..") do set "REPO_ROOT=%%~fI"
set "VENV_PY=%REPO_ROOT%\.venv\Scripts\python.exe"
set "REQ_FILE=%REPO_ROOT%\scripts\crawlers\halooglasi\requirements.txt"
set "CRAWLER=%REPO_ROOT%\scripts\crawlers\halooglasi\halooglasi_crawler.py"
set "OUTPUT_DIR=%REPO_ROOT%\scripts\crawlers\halooglasi\output"

REM Prefer known Python 3.13 path (avoids Windows Store alias issues)
set "PY=C:\Users\sam\AppData\Local\Programs\Python\Python313\python.exe"
if exist "%PY%" goto HAVE_PY

REM Try 'py -3'
py -3 -V >NUL 2>&1
if %ERRORLEVEL%==0 (
  set "PY=py -3"
  goto HAVE_PY
)

REM Try 'python'
python -V >NUL 2>&1
if %ERRORLEVEL%==0 (
  set "PY=python"
  goto HAVE_PY
)

echo [error] No Python interpreter found. Install Python 3.12+ and retry.
exit /b 1

:HAVE_PY
REM Create venv if missing (absolute path)
if not exist "%VENV_PY%" (
  "%PY%" -m venv "%REPO_ROOT%\.venv"
)

REM Install requirements inside venv (absolute paths)
"%VENV_PY%" -m pip install -r "%REQ_FILE%"

REM Default args if none provided
if "%~1"=="" (
  set "ARGS=--seed https://www.halooglasi.com/ --max-pages 400 --delay-seconds 1.5 --tree-max-children 20 --progress-every 25"
) else (
  set "ARGS=%*"
)

REM Run the crawler (absolute path)
"%VENV_PY%" "%CRAWLER%" %ARGS%
set ERR=%ERRORLEVEL%
if %ERR% NEQ 0 (
  echo [fail] Crawler exited with code %ERR%
  exit /b %ERR%
)

echo.
echo [ok] Outputs written to "%OUTPUT_DIR%"
endlocal
