@echo off
setlocal ENABLEDELAYEDEXPANSION

REM Move to repo root (this file lives in scripts\crawlers\halooglasi)
set "SCRIPT_DIR=%~dp0"
pushd "%SCRIPT_DIR%..\..\.." >NUL 2>&1

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
popd >NUL 2>&1
exit /b 1

:HAVE_PY
REM Create venv if missing
if not exist ".venv\Scripts\python.exe" (
  "%PY%" -m venv .venv
)

REM Install requirements inside venv
".venv\Scripts\python.exe" -m pip install -r scripts\crawlers\halooglasi\requirements.txt

REM Default args if none provided
if "%~1"=="" (
  set "ARGS=--seed https://www.halooglasi.com/ --max-pages 400 --delay-seconds 1.5 --tree-max-children 20"
) else (
  set "ARGS=%*"
)

REM Run the crawler
".venv\Scripts\python.exe" scripts\crawlers\halooglasi\halooglasi_crawler.py %ARGS%

Echo.
echo [ok] Outputs written to scripts\crawlers\halooglasi\output\

popd >NUL 2>&1
endlocal
