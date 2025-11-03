Param(
  [string]$RepoName,
  [ValidateSet('public','private')][string]$Visibility = 'private',
  [string]$Description = ''
)

$ErrorActionPreference = 'Stop'

function Write-Step($msg) { Write-Host ("[>] " + $msg) -ForegroundColor Cyan }
function Write-Ok($msg)   { Write-Host ("[OK] " + $msg) -ForegroundColor Green }
function Write-Err($msg)  { Write-Host ("[ERR] " + $msg) -ForegroundColor Red }

try {
  $repoRoot = Resolve-Path (Join-Path $PSScriptRoot '..\..')
  $folderName = Split-Path $repoRoot -Leaf
  if ([string]::IsNullOrWhiteSpace($RepoName)) { $RepoName = $folderName }

  Write-Step "Repo root: $repoRoot"
  Set-Location $repoRoot

  if (-not (Get-Command git -ErrorAction SilentlyContinue)) { throw 'git is not installed or not in PATH' }
  $hasGh = $true
  try { gh --version *> $null } catch { $hasGh = $false }

  # Initialize repo if needed
  if (-not (Test-Path (Join-Path $repoRoot '.git'))) {
    Write-Step 'Initializing git repository'
    git init | Out-Null
  }

  # Ensure basic user config to avoid commit failure
  $userEmail = git config user.email 2>$null
  if (-not $userEmail) { git config user.email "you@example.com" | Out-Null }
  $userName = git config user.name 2>$null
  if (-not $userName) { git config user.name "Your Name" | Out-Null }

  Write-Step 'Staging changes'
  git add -A

  # Create commit if needed
  $dirty = git status --porcelain
  if ($dirty) {
    Write-Step 'Committing changes'
    git commit -m "chore: initial publish" | Out-Null
  } else {
    Write-Step 'No changes to commit'
  }

  # Ensure main branch
  Write-Step 'Ensuring main branch'
  git branch -M main 2>$null

  # Remote handling
  $originUrl = ''
  try { $originUrl = git remote get-url origin 2>$null } catch { $originUrl = '' }

  if (-not $originUrl) {
    if ($hasGh) {
      Write-Step "Creating GitHub repo '$RepoName' ($Visibility) via gh"
      $args = @('repo','create',$RepoName,'--source','.',"--$Visibility",'--remote','origin','--push','--description',$Description)
      gh @args | Out-Null
      $originUrl = git remote get-url origin
      Write-Ok "Created and pushed to $originUrl"
    } else {
      Write-Step 'gh CLI not found; please paste a remote HTTPS URL (e.g. https://github.com/<user>/{RepoName}.git)'
      $remote = Read-Host 'Remote URL'
      if ([string]::IsNullOrWhiteSpace($remote)) { throw 'Remote URL is required when gh is not installed' }
      git remote add origin $remote | Out-Null
      Write-Step 'Pushing to origin/main'
      git push -u origin main
      $originUrl = $remote
      Write-Ok "Pushed to $originUrl"
    }
  } else {
    Write-Step "Remote origin already set: $originUrl"
    Write-Step 'Pushing updates'
    git push -u origin main
  }

  # Verify push by comparing local and remote HEAD
  $localHead = (git rev-parse HEAD).Trim()
  $remoteHeadLine = (git ls-remote origin HEAD)
  $remoteHead = if ($remoteHeadLine) { $remoteHeadLine.Split("`t")[0] } else { '' }

  if ($remoteHead -and ($remoteHead -eq $localHead)) {
    Write-Ok ("Verified: remote HEAD matches local (" + $localHead + ")")
  } else {
    Write-Err 'Verification failed: remote HEAD did not match local commit'
  }

  if ($hasGh) {
    try { gh repo view --web $RepoName | Out-Null } catch {}
  }

  Write-Host "" -ForegroundColor Yellow
  Write-Host "Summary:" -ForegroundColor Yellow
  Write-Host ("  Repo: " + $RepoName)
  Write-Host ("  Remote: " + $originUrl)
  Write-Host "  Branch: main"
  Write-Host ("  Local: " + $localHead)
  Write-Host ("  Remote: " + $remoteHead)
  exit 0
}
catch {
  Write-Err $_
  exit 1
}


