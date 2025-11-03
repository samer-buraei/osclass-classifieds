$ErrorActionPreference = "Stop"

$version = "0.22.15"
$arch = if ([Environment]::Is64BitOperatingSystem) { "windows_amd64" } else { "windows_386" }
$zipName = "pocketbase_${version}_${arch}.zip"
$downloadUrl = "https://github.com/pocketbase/pocketbase/releases/download/v${version}/${zipName}"
$targetDir = Join-Path $PSScriptRoot "bin"
$zipPath = Join-Path $targetDir $zipName

if (!(Test-Path $targetDir)) { New-Item -ItemType Directory -Path $targetDir | Out-Null }

Write-Host "Downloading PocketBase $version ($arch)..."
Invoke-WebRequest -Uri $downloadUrl -OutFile $zipPath

Write-Host "Extracting..."
Expand-Archive -Path $zipPath -DestinationPath $targetDir -Force

Remove-Item $zipPath -Force

Write-Host "PocketBase downloaded to $targetDir"
Write-Host "Run:`n  .\\bin\\pocketbase.exe serve --http=0.0.0.0:8090"
Write-Host "Then configure collections for: /auth/*, /user/*, /matches, /message(s) and map WS: /chat/:sender/:receiver"
