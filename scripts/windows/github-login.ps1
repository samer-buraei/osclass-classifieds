Param()

$ErrorActionPreference = 'Stop'

function Info($m) { Write-Host "[>]" $m -ForegroundColor Cyan }
function Ok($m)   { Write-Host "[OK]" $m -ForegroundColor Green }
function Err($m)  { Write-Host "[ERR]" $m -ForegroundColor Red }

try {
  if (-not (Get-Command git -ErrorAction SilentlyContinue)) {
    throw "git is not installed or not in PATH. Install Git for Windows first."
  }

  Info "Configuring Git user"
  $ghUser = Read-Host "GitHub username"
  $email  = Read-Host "Email for git commits"
  if ([string]::IsNullOrWhiteSpace($ghUser) -or [string]::IsNullOrWhiteSpace($email)) {
    throw "Username and email are required."
  }
  git config --global user.name "$ghUser" | Out-Null
  git config --global user.email "$email" | Out-Null

  Info "Opening browser to create a Personal Access Token (repo scope)"
  Start-Process "https://github.com/settings/tokens/new?scopes=repo&description=Local%20push%20from%20Windows"

  $secToken = Read-Host "Paste GitHub Personal Access Token (repo scope)" -AsSecureString
  $plainToken = [Runtime.InteropServices.Marshal]::PtrToStringAuto(
                  [Runtime.InteropServices.Marshal]::SecureStringToBSTR($secToken))

  if ([string]::IsNullOrWhiteSpace($plainToken)) {
    throw "Token cannot be empty."
  }

  Info "Setting credential helper (store)"
  git config --global credential.helper store | Out-Null

  $credsPath = Join-Path $env:USERPROFILE ".git-credentials"
  if (Test-Path $credsPath) {
    Copy-Item $credsPath ($credsPath + ".bak") -Force
    Info "Backed up existing credentials to $credsPath.bak"
  }
  $line = "https://${ghUser}:${plainToken}@github.com"
  Set-Content -Path $credsPath -Value $line -Encoding ASCII

  Ok "Credentials saved to $credsPath"
  Ok "Git is now configured to push to GitHub over HTTPS."
  Write-Host ""
  Write-Host "Next steps:" -ForegroundColor Yellow
  Write-Host "  1) Ensure your repo has a remote:  git remote -v"
  Write-Host "  2) If missing, add one:            git remote add origin https://github.com/<you>/<repo>.git"
  Write-Host "  3) Push:                           git push -u origin main"
  exit 0
}
catch {
  Err $_
  exit 1
}
