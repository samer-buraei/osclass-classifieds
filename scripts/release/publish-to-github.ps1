param(
    [Parameter(Mandatory = $true)] [string] $RepoUrl,
    [string] $DefaultBranch = "main",
    [string] $CommitMessage = "chore: repo sync",
    [switch] $Push = $true,
    [switch] $Force
)

$ErrorActionPreference = 'Stop'

function Write-Step($msg) {
    Write-Host "[step] $msg"
}

function Write-Ok($msg) {
    Write-Host "[ok]  $msg" -ForegroundColor Green
}

function Write-Warn($msg) {
    Write-Host "[warn] $msg" -ForegroundColor Yellow
}

function Write-Fail($msg) {
    Write-Host "[fail] $msg" -ForegroundColor Red
}

function Invoke-Git([string[]] $GitArgs) {
    git @GitArgs | Out-Host
    if ($LASTEXITCODE -ne 0) { throw "git $($GitArgs -join ' ') failed" }
}

try {
    Write-Step "Checking git availability"
    git --version | Out-Host
} catch {
    Write-Fail "Git not found. Install Git and retry."
    exit 1
}

# Resolve repo root (this script is at scripts\release)
$repoRoot = Resolve-Path (Join-Path $PSScriptRoot '..\..')
Set-Location $repoRoot
Write-Ok "Repo root: $repoRoot"

# Initialize repo if needed
if (-not (Test-Path ".git")) {
    Write-Step "Initializing git repository"
    Invoke-Git @('init')
} else {
    Write-Ok ".git directory exists"
}

# Ensure default branch
$currBranch = ''
try {
    $currBranch = (git rev-parse --abbrev-ref HEAD) 2>$null
} catch { $currBranch = '' }
if (-not $currBranch -or $currBranch.Trim().ToLower() -eq 'head') {
    # No commits yet; set branch name after first commit
    Write-Warn "No current branch; will rename after first commit"
} elseif ($currBranch.Trim() -ne $DefaultBranch) {
    Write-Step "Renaming branch $currBranch -> $DefaultBranch"
    Invoke-Git @('branch','-M',$DefaultBranch)
    $currBranch = $DefaultBranch
} else {
    Write-Ok "On branch $currBranch"
}

# Stage & commit changes
$hasHead = $false
try { git rev-parse --verify HEAD 2>$null | Out-Null; if ($LASTEXITCODE -eq 0) { $hasHead = $true } } catch {}
$changes = (git status --porcelain)
if ($changes) {
    Write-Step "Staging changes"
    Invoke-Git @('add','-A')
    Write-Step "Committing"
    Invoke-Git @('commit','-m',$CommitMessage)
} elseif (-not $hasHead) {
    Write-Step "No changes but repository is empty, creating initial empty commit"
    Invoke-Git @('commit','--allow-empty','-m','chore: initial commit')
} else {
    Write-Ok "No changes to commit"
}

# Ensure branch now
if (-not $currBranch -or $currBranch.Trim().ToLower() -eq 'head') {
    Write-Step "Setting default branch to $DefaultBranch"
    Invoke-Git @('branch','-M',$DefaultBranch)
    $currBranch = $DefaultBranch
}

# Configure remote origin
$haveOrigin = $false
$originUrl = ''
try { $originUrl = (git remote get-url origin) 2>$null; $haveOrigin = $true } catch { $haveOrigin = $false }
if (-not $haveOrigin) {
    Write-Step "Adding remote origin: $RepoUrl"
    Invoke-Git @('remote','add','origin',$RepoUrl)
} elseif ($originUrl.Trim() -ne $RepoUrl.Trim()) {
    if ($Force) {
        Write-Warn "Origin URL differs; updating to $RepoUrl"
        Invoke-Git @('remote','set-url','origin',$RepoUrl)
    } else {
        Write-Fail "Origin URL differs. Run with -Force to overwrite or update manually. Current: $originUrl"
        exit 1
    }
} else {
    Write-Ok "Remote origin already set"
}

# Show status summary
Write-Step "Status"
(git status -sb) | Out-Host
(git remote -v)  | Out-Host

# Push
if ($Push) {
    Write-Step "Pushing to $RepoUrl ($currBranch)"
    if ($Force) {
        Invoke-Git @('push','-u','--force-with-lease','origin',$currBranch)
    } else {
        Invoke-Git @('push','-u','origin',$currBranch)
    }
    # Verify remote has the branch
    Write-Step "Verifying remote branch exists"
    $headRef = (git ls-remote --heads origin $currBranch)
    if (-not $headRef) {
        Write-Fail "Remote verification failed (branch not visible)."
        exit 1
    }
    Write-Ok "Push verified: $RepoUrl ($currBranch)"
} else {
    Write-Warn "Skipped push (use -Push to enable)"
}

Write-Ok "Done."
