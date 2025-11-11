param(
    [Parameter(Mandatory = $true)] [string] $RepoName,
    [ValidateSet('public','private','internal')] [string] $Visibility = 'public',
    [string] $User = '',
    [string] $DefaultBranch = 'main',
    [string] $CommitMessage = 'chore: publish',
    [switch] $Force,
    [switch] $NoCreateRemote
)

$ErrorActionPreference = 'Stop'

function Write-Step($msg) { Write-Host "[step] $msg" }
function Write-Ok($msg)   { Write-Host "[ok]  $msg" -ForegroundColor Green }
function Write-Warn($msg) { Write-Host "[warn] $msg" -ForegroundColor Yellow }
function Write-Fail($msg) { Write-Host "[fail] $msg" -ForegroundColor Red }

# Resolve repo root (this file is in scripts\windows)
$repoRoot = Resolve-Path (Join-Path $PSScriptRoot '..\..')
Set-Location $repoRoot
Write-Ok "Repo root: $repoRoot"

# Ensure git
try { git --version | Out-Host } catch { Write-Fail "Git not found"; exit 1 }

# Determine GitHub user
if (-not $User) {
    $ghOk = $false
    try { gh --version | Out-Null; $ghOk = $true } catch { $ghOk = $false }
    if ($ghOk) {
        Write-Step "Fetching GitHub username via gh"
        try {
            $me = gh api user | ConvertFrom-Json
            if ($me -and $me.login) { $User = $me.login; Write-Ok "GitHub user: $User" }
        } catch { Write-Warn "gh present but cannot read user; make sure 'gh auth login' is done" }
    }
}

if (-not $User) {
    Write-Fail "Cannot resolve GitHub username. Provide -User USERNAME or login with 'gh auth login'."
    exit 1
}

# Optionally create repo with gh
if (-not $NoCreateRemote) {
    $ghOk = $false
    try { gh --version | Out-Null; $ghOk = $true } catch { $ghOk = $false }
    if ($ghOk) {
        Write-Step "Checking if repo exists: $User/$RepoName"
        $exists = $true
        try { gh repo view "$User/$RepoName" 1>$null 2>$null } catch { $exists = $false }
        if (-not $exists) {
            Write-Step "Creating repo via gh: $Visibility"
            $visFlag = if ($Visibility -eq 'public') { '--public' } elseif ($Visibility -eq 'private') { '--private' } else { '--internal' }
            gh repo create "$User/$RepoName" $visFlag --confirm | Out-Host
            Write-Ok "Repo created"
        } else {
            Write-Ok "Repo already exists"
        }
    } else {
        Write-Warn "gh not found; skipping auto-create. Make sure the repo exists on GitHub."
    }
}

# Build RepoUrl
$RepoUrl = "https://github.com/$User/$RepoName.git"
Write-Step "RepoUrl: $RepoUrl"

# Delegate to release publisher
$publisher = Resolve-Path (Join-Path $PSScriptRoot '..\release\publish-to-github.ps1')
& $publisher -RepoUrl $RepoUrl -DefaultBranch $DefaultBranch -CommitMessage $CommitMessage -Push -Force:$Force.IsPresent
exit $LASTEXITCODE


