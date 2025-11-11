@echo off
REM ============================================
REM GitHub Upload Script - Batch File Version
REM ============================================

COLOR 0B
echo ===============================================
echo    GitHub Upload Script - Osclass Project
echo ===============================================
echo.

REM Step 1: Check Git Installation
echo [Step 1/8] Checking Git Installation...
git --version >nul 2>&1
if %errorlevel% neq 0 (
    color 0C
    echo X Git is NOT installed!
    echo Please download Git from: https://git-scm.com/download/win
    pause
    exit /b 1
)
echo [OK] Git is installed
echo.

REM Step 2: Confirm Directory
echo [Step 2/8] Current Directory:
cd
echo.
set /p confirm="Is this the correct project directory? (y/n): "
if /i not "%confirm%"=="y" (
    echo X Please navigate to your project directory first
    pause
    exit /b 1
)
echo [OK] Directory confirmed
echo.

REM Step 3: Initialize Git
echo [Step 3/8] Checking Git Repository...
if exist ".git" (
    echo [OK] Git repository already initialized
    set /p reinit="Do you want to reinitialize? (y/n): "
    if /i "%reinit%"=="y" (
        git init
        echo [OK] Repository reinitialized
    )
) else (
    echo Initializing Git repository...
    git init
    if %errorlevel% neq 0 (
        echo X Failed to initialize Git repository
        pause
        exit /b 1
    )
    echo [OK] Git repository initialized
)
echo.

REM Step 4: Check Git Config
echo [Step 4/8] Checking Git Configuration...
git config user.name >nul 2>&1
if %errorlevel% neq 0 (
    set /p username="Enter your GitHub username: "
    git config user.name "!username!"
    echo [OK] Git user name set
)
git config user.email >nul 2>&1
if %errorlevel% neq 0 (
    set /p useremail="Enter your GitHub email: "
    git config user.email "!useremail!"
    echo [OK] Git email set
)
echo [OK] Git configuration complete
echo.

REM Step 5: Create .gitignore
echo [Step 5/8] Setting up .gitignore...
if not exist ".gitignore" (
    echo Creating .gitignore file...
    (
        echo # Dependencies
        echo /vendor/
        echo /node_modules/
        echo /FirstDate/node_modules/
        echo /server/node_modules/
        echo.
        echo # Environment files
        echo .env
        echo .env.local
        echo config/config.php
        echo.
        echo # User uploaded files
        echo /public/uploads/*
        echo !/public/uploads/.gitkeep
        echo.
        echo # Logs
        echo *.log
        echo /logs/
        echo.
        echo # OS files
        echo .DS_Store
        echo Thumbs.db
        echo desktop.ini
        echo.
        echo # IDE files
        echo .vscode/
        echo .idea/
        echo *.sublime-*
        echo.
        echo # Temporary files
        echo *.tmp
        echo *.temp
        echo *.cache
        echo.
        echo # Database
        echo *.sql.gz
        echo *.sql.backup
        echo.
        echo # Composer
        echo composer.phar
        echo composer.lock
    ) > .gitignore
    echo [OK] .gitignore created
) else (
    echo [OK] .gitignore already exists
)
echo.

REM Step 6: Add Files
echo [Step 6/8] Adding Files to Git...
echo Checking files to be added...
git status --short
echo.
set /p confirm="Do you want to add ALL these files? (y/n): "
if /i not "%confirm%"=="y" (
    echo X Upload cancelled by user
    pause
    exit /b 1
)
echo Adding files...
git add .
if %errorlevel% neq 0 (
    echo X Failed to add files
    pause
    exit /b 1
)
echo [OK] All files staged for commit
echo.

REM Step 7: Commit
echo [Step 7/8] Committing Changes...
set defaultMessage=Initial commit - Osclass Classifieds Platform with Halooglasi Theme
echo Default commit message: %defaultMessage%
set /p commitMsg="Press Enter to use default, or type your commit message: "
if "%commitMsg%"=="" set commitMsg=%defaultMessage%
echo Committing with message: %commitMsg%
git commit -m "%commitMsg%"
if %errorlevel% neq 0 (
    echo X Commit failed
    pause
    exit /b 1
)
echo [OK] Changes committed successfully
echo.

REM Step 8: Push to GitHub
echo [Step 8/8] Pushing to GitHub...
echo.
echo IMPORTANT: Before proceeding, make sure you have:
echo    1. Created a repository on GitHub
echo    2. Have the repository URL ready
echo    3. Set up authentication (SSH key or Personal Access Token)
echo.
set /p proceed="Ready to proceed? (y/n): "
if /i not "%proceed%"=="y" (
    echo [OK] Files are committed locally. Run this script again when ready to push.
    pause
    exit /b 0
)

REM Check if remote exists
git remote get-url origin >nul 2>&1
if %errorlevel% equ 0 (
    echo [OK] Remote 'origin' already exists
    git remote get-url origin
    set /p changeRemote="Do you want to change it? (y/n): "
    if /i "%changeRemote%"=="y" (
        set /p repoUrl="Enter your GitHub repository URL: "
        git remote set-url origin !repoUrl!
        echo [OK] Remote URL updated
    )
) else (
    echo Example: https://github.com/yourusername/your-repo.git
    echo Or SSH: git@github.com:yourusername/your-repo.git
    set /p repoUrl="Enter your GitHub repository URL: "
    git remote add origin !repoUrl!
    if %errorlevel% neq 0 (
        echo X Failed to add remote
        pause
        exit /b 1
    )
    echo [OK] Remote 'origin' added
)
echo.

REM Get current branch
for /f "tokens=*" %%a in ('git branch --show-current') do set currentBranch=%%a
echo Current branch: %currentBranch%
if not "%currentBranch%"=="main" if not "%currentBranch%"=="master" (
    set /p renameBranch="Do you want to rename to 'main'? (y/n): "
    if /i "!renameBranch!"=="y" (
        git branch -M main
        set currentBranch=main
        echo [OK] Branch renamed to 'main'
    )
)
echo.

REM Push
echo Pushing to GitHub...
echo This may take a few minutes depending on project size...
echo.
git push -u origin %currentBranch%

if %errorlevel% equ 0 (
    color 0A
    echo.
    echo ===============================================
    echo    SUCCESS! Project Uploaded to GitHub!
    echo ===============================================
    echo.
    echo Summary:
    echo    - Branch: %currentBranch%
    echo    - Remote: origin
    echo    - Status: Pushed successfully
    echo.
    echo Visit your repository on GitHub to verify!
    echo.
) else (
    color 0C
    echo.
    echo X Push failed!
    echo.
    echo Common issues:
    echo 1. Authentication failed - Set up SSH key or Personal Access Token
    echo 2. Repository doesn't exist - Create it on GitHub first
    echo 3. Wrong URL - Double-check your repository URL
    echo.
    echo Your changes are committed locally. You can try pushing again.
    echo.
)

pause



