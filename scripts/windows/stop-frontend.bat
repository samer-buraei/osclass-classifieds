@echo off
echo Stopping frontend on port 3000...
for /f "tokens=5" %%a in ('netstat -ano ^| findstr LISTENING ^| findstr :3000') do (
  echo Killing PID %%a
  taskkill /PID %%a /F >nul 2>&1
)
echo Done.


