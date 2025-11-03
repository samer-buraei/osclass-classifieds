@echo off
echo Stopping backend on port 8000...
for /f "tokens=5" %%a in ('netstat -ano ^| findstr LISTENING ^| findstr :8000') do (
  echo Killing PID %%a
  taskkill /PID %%a /F >nul 2>&1
)
echo Done.


