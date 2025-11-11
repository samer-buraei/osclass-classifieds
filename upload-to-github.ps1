# ============================================
# GitHub Upload Script with Progress Tracking
# ============================================

$ErrorActionPreference = "Continue"

Write-Host "===============================================" -ForegroundColor Cyan
Write-Host "   GitHub Upload Script - Osclass Project      " -ForegroundColor Cyan
Write-Host "===============================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: Check if Git is installed
Write-Host "[Step 1/8] Checking Git Installation..." -ForegroundColor Yellow
$gitVersion = git --version 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Git is installed: $gitVersion" -ForegroundColor Green
} else {
    Write-Host "❌ Git is NOT installed!" -ForegroundColor Red
    Write-Host "Please download Git from: https://git-scm.com/download/win" -ForegroundColor Yellow
    Read-Host "Press Enter to exit"
    exit
}
Write-Host ""

# Step 2: Check if we're in the right directory
Write-Host "[Step 2/8] Checking Current Directory..." -ForegroundColor Yellow
$currentDir = Get-Location
Write-Host "Current directory: $currentDir" -ForegroundColor Cyan
$confirm = Read-Host "Is this the correct project directory? (y/n)"
if ($confirm -ne "y" -and $confirm -ne "Y") {
    Write-Host "❌ Please navigate to your project directory first" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit
}
Write-Host "✅ Directory confirmed" -ForegroundColor Green
Write-Host ""

# Step 3: Check Git initialization
Write-Host "[Step 3/8] Checking Git Repository..." -ForegroundColor Yellow
if (Test-Path ".git") {
    Write-Host "✅ Git repository already initialized" -ForegroundColor Green
    $reinit = Read-Host "Do you want to reinitialize? (y/n)"
    if ($reinit -eq "y" -or $reinit -eq "Y") {
        git init
        Write-Host "✅ Repository reinitialized" -ForegroundColor Green
    }
} else {
    Write-Host "📦 Initializing Git repository..." -ForegroundColor Cyan
    git init
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Git repository initialized" -ForegroundColor Green
    } else {
        Write-Host "❌ Failed to initialize Git repository" -ForegroundColor Red
        Read-Host "Press Enter to exit"
        exit
    }
}
Write-Host ""

# Step 4: Configure Git (if needed)
Write-Host "[Step 4/8] Checking Git Configuration..." -ForegroundColor Yellow
$gitUser = git config user.name 2>&1
$gitEmail = git config user.email 2>&1

if ([string]::IsNullOrWhiteSpace($gitUser)) {
    Write-Host "⚠️  Git user name not set" -ForegroundColor Yellow
    $userName = Read-Host "Enter your GitHub username"
    git config user.name "$userName"
    Write-Host "✅ Git user name set to: $userName" -ForegroundColor Green
} else {
    Write-Host "✅ Git user: $gitUser" -ForegroundColor Green
}

if ([string]::IsNullOrWhiteSpace($gitEmail)) {
    Write-Host "⚠️  Git email not set" -ForegroundColor Yellow
    $userEmail = Read-Host "Enter your GitHub email"
    git config user.email "$userEmail"
    Write-Host "✅ Git email set to: $userEmail" -ForegroundColor Green
} else {
    Write-Host "✅ Git email: $gitEmail" -ForegroundColor Green
}
Write-Host ""

# Step 5: Create/Update .gitignore
Write-Host "[Step 5/8] Setting up .gitignore..." -ForegroundColor Yellow
if (!(Test-Path ".gitignore")) {
    Write-Host "📝 Creating .gitignore file..." -ForegroundColor Cyan
    @"
# Dependencies
/vendor/
/node_modules/
/FirstDate/node_modules/
/server/node_modules/

# Environment files
.env
.env.local
config/config.php

# User uploaded files
/public/uploads/*
!/public/uploads/.gitkeep

# Logs
*.log
/logs/

# OS files
.DS_Store
Thumbs.db
desktop.ini

# IDE files
.vscode/
.idea/
*.sublime-*

# Temporary files
*.tmp
*.temp
*.cache

# Database
*.sql.gz
*.sql.backup

# Composer
composer.phar
composer.lock

# Package lock files (optional)
package-lock.json
"@ | Out-File -FilePath ".gitignore" -Encoding UTF8
    Write-Host "✅ .gitignore created" -ForegroundColor Green
} else {
    Write-Host "✅ .gitignore already exists" -ForegroundColor Green
}
Write-Host ""

# Step 6: Add files to Git
Write-Host "[Step 6/8] Adding Files to Git..." -ForegroundColor Yellow
Write-Host "📊 Checking files to be added..." -ForegroundColor Cyan

# Show what will be added
$statusOutput = git status --short
Write-Host $statusOutput
Write-Host ""

$filesCount = ($statusOutput | Measure-Object -Line).Lines
Write-Host "Total files to add: $filesCount" -ForegroundColor Cyan

$confirm = Read-Host "Do you want to add ALL these files? (y/n)"
if ($confirm -ne "y" -and $confirm -ne "Y") {
    Write-Host "❌ Upload cancelled by user" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit
}

Write-Host "📦 Adding files..." -ForegroundColor Cyan
git add .

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ All files staged for commit" -ForegroundColor Green
} else {
    Write-Host "❌ Failed to add files" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit
}
Write-Host ""

# Step 7: Commit changes
Write-Host "[Step 7/8] Committing Changes..." -ForegroundColor Yellow
$defaultMessage = "Initial commit - Osclass Classifieds Platform with Halooglasi Theme"
Write-Host "Default commit message: $defaultMessage" -ForegroundColor Cyan
$customMessage = Read-Host "Press Enter to use default, or type your commit message"

if ([string]::IsNullOrWhiteSpace($customMessage)) {
    $commitMessage = $defaultMessage
} else {
    $commitMessage = $customMessage
}

Write-Host "📝 Committing with message: $commitMessage" -ForegroundColor Cyan
git commit -m "$commitMessage"

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Changes committed successfully" -ForegroundColor Green
} else {
    Write-Host "❌ Commit failed" -ForegroundColor Red
    Read-Host "Press Enter to exit"
    exit
}
Write-Host ""

# Step 8: Add remote and push
Write-Host "[Step 8/8] Pushing to GitHub..." -ForegroundColor Yellow
Write-Host ""
Write-Host "⚠️  IMPORTANT: Before proceeding, make sure you have:" -ForegroundColor Yellow
Write-Host "   1. Created a repository on GitHub" -ForegroundColor White
Write-Host "   2. Have the repository URL ready" -ForegroundColor White
Write-Host "   3. Set up authentication (SSH key or Personal Access Token)" -ForegroundColor White
Write-Host ""

$proceed = Read-Host "Ready to proceed? (y/n)"
if ($proceed -ne "y" -and $proceed -ne "Y") {
    Write-Host "✅ Files are committed locally. Run this script again when ready to push." -ForegroundColor Green
    Read-Host "Press Enter to exit"
    exit
}

# Check if remote already exists
$remoteExists = git remote get-url origin 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ Remote 'origin' already set to: $remoteExists" -ForegroundColor Green
    $changeRemote = Read-Host "Do you want to change it? (y/n)"
    if ($changeRemote -eq "y" -or $changeRemote -eq "Y") {
        $repoUrl = Read-Host "Enter your GitHub repository URL"
        git remote set-url origin $repoUrl
        Write-Host "✅ Remote URL updated" -ForegroundColor Green
    }
} else {
    Write-Host "Example: https://github.com/yourusername/your-repo.git" -ForegroundColor Cyan
    Write-Host "Or SSH: git@github.com:yourusername/your-repo.git" -ForegroundColor Cyan
    $repoUrl = Read-Host "Enter your GitHub repository URL"
    
    git remote add origin $repoUrl
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Remote 'origin' added" -ForegroundColor Green
    } else {
        Write-Host "❌ Failed to add remote" -ForegroundColor Red
        Read-Host "Press Enter to exit"
        exit
    }
}
Write-Host ""

# Check default branch name
$currentBranch = git branch --show-current
Write-Host "Current branch: $currentBranch" -ForegroundColor Cyan
if ($currentBranch -ne "main" -and $currentBranch -ne "master") {
    $renameBranch = Read-Host "Do you want to rename to 'main'? (y/n)"
    if ($renameBranch -eq "y" -or $renameBranch -eq "Y") {
        git branch -M main
        $currentBranch = "main"
        Write-Host "✅ Branch renamed to 'main'" -ForegroundColor Green
    }
}
Write-Host ""

# Push to GitHub
Write-Host "🚀 Pushing to GitHub..." -ForegroundColor Cyan
Write-Host "This may take a few minutes depending on project size..." -ForegroundColor Yellow
Write-Host ""

git push -u origin $currentBranch

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "===============================================" -ForegroundColor Green
    Write-Host "   ✅ SUCCESS! Project Uploaded to GitHub!    " -ForegroundColor Green
    Write-Host "===============================================" -ForegroundColor Green
    Write-Host ""
    Write-Host "📊 Summary:" -ForegroundColor Cyan
    Write-Host "   - Branch: $currentBranch" -ForegroundColor White
    Write-Host "   - Remote: origin" -ForegroundColor White
    Write-Host "   - Status: Pushed successfully" -ForegroundColor White
    Write-Host ""
    Write-Host "🌐 Visit your repository on GitHub to verify!" -ForegroundColor Yellow
    Write-Host ""
} else {
    Write-Host ""
    Write-Host "❌ Push failed!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Common issues:" -ForegroundColor Yellow
    Write-Host "1. Authentication failed - Set up SSH key or Personal Access Token" -ForegroundColor White
    Write-Host "2. Repository doesn't exist - Create it on GitHub first" -ForegroundColor White
    Write-Host "3. Wrong URL - Double-check your repository URL" -ForegroundColor White
    Write-Host ""
    Write-Host "Your changes are committed locally. You can try pushing again." -ForegroundColor Cyan
    Write-Host ""
}

Read-Host "Press Enter to exit"



