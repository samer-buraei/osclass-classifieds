Add-Type -AssemblyName System.Windows.Forms
Add-Type -AssemblyName System.Drawing

function New-Button($text, $x, $y, $w=120, $h=30) {
  $b = New-Object System.Windows.Forms.Button
  $b.Text = $text; $b.Location = New-Object System.Drawing.Point($x,$y); $b.Size = New-Object System.Drawing.Size($w,$h)
  return $b
}
function New-Label($text, $x, $y, $w=120, $h=20) {
  $l = New-Object System.Windows.Forms.Label
  $l.Text = $text; $l.Location = New-Object System.Drawing.Point($x,$y); $l.Size = New-Object System.Drawing.Size($w,$h)
  return $l
}
function Append-Log($msg) {
  $LogBox.AppendText(("[{0}] {1}`r`n" -f (Get-Date -Format "HH:mm:ss"), $msg))
}
function Run-Git($args, $dir) {
  $psi = New-Object System.Diagnostics.ProcessStartInfo
  $psi.FileName = "git"
  $psi.Arguments = $args
  $psi.WorkingDirectory = $dir
  $psi.RedirectStandardOutput = $true
  $psi.RedirectStandardError = $true
  $psi.UseShellExecute = $false
  $p = New-Object System.Diagnostics.Process
  $p.StartInfo = $psi
  [void]$p.Start()
  $out = $p.StandardOutput.ReadToEnd()
  $err = $p.StandardError.ReadToEnd()
  $p.WaitForExit()
  if ($out) { Append-Log $out.Trim() }
  if ($err) { Append-Log ("ERR: " + $err.Trim()) }
  return $p.ExitCode
}
function Has-Gh() {
  try { (Get-Command gh -ErrorAction Stop) | Out-Null; return $true } catch { return $false }
}
function Run-Gh($args, $dir) {
  $psi = New-Object System.Diagnostics.ProcessStartInfo
  $psi.FileName = "gh"
  $psi.Arguments = $args
  $psi.WorkingDirectory = $dir
  $psi.RedirectStandardOutput = $true
  $psi.RedirectStandardError = $true
  $psi.UseShellExecute = $false
  $p = New-Object System.Diagnostics.Process
  $p.StartInfo = $psi
  [void]$p.Start()
  $out = $p.StandardOutput.ReadToEnd()
  $err = $p.StandardError.ReadToEnd()
  $p.WaitForExit()
  if ($out) { Append-Log $out.Trim() }
  if ($err) { Append-Log ("ERR: " + $err.Trim()) }
  return $p.ExitCode
}

$Form = New-Object System.Windows.Forms.Form
$Form.Text = "GitHub Uploader"
$Form.Size = New-Object System.Drawing.Size(820, 560)
$Form.StartPosition = "CenterScreen"

$defaultRepoPath = "C:\Users\sam\Desktop\agent builder\open-agent-builder"

$Form.Controls.Add((New-Label "Repo Path" 10 15 100 20))
$RepoPath = New-Object System.Windows.Forms.TextBox
$RepoPath.Location = New-Object System.Drawing.Point(120,10)
$RepoPath.Size = New-Object System.Drawing.Size(560,24)
$RepoPath.Text = $defaultRepoPath
$Form.Controls.Add($RepoPath)
$Browse = New-Button "Browse..." 690 8 100 28
$Browse.Add_Click({
  $dlg = New-Object System.Windows.Forms.FolderBrowserDialog
  $dlg.SelectedPath = $RepoPath.Text
  if ($dlg.ShowDialog() -eq [System.Windows.Forms.DialogResult]::OK) {
    $RepoPath.Text = $dlg.SelectedPath
  }
})
$Form.Controls.Add($Browse)

$Form.Controls.Add((New-Label "Remote URL (origin)" 10 50 120 20))
$RemoteUrl = New-Object System.Windows.Forms.TextBox
$RemoteUrl.Location = New-Object System.Drawing.Point(140,46)
$RemoteUrl.Size = New-Object System.Drawing.Size(650,24)
$Form.Controls.Add($RemoteUrl)

$StatusBtn = New-Button "Git Status" 10 80
$DetectRemoteBtn = New-Button "Detect Remote" 140 80
$SetRemoteBtn = New-Button "Set Remote" 270 80
$ForkBtn = New-Button "Fork (gh)" 400 80
$PushBtn = New-Button "Push" 510 80
$PullBtn = New-Button "Pull --rebase" 590 80 120 30
$VerifyBtn = New-Button "Verify Remote HEAD" 720 80 80 30

$Form.Controls.Add($StatusBtn)
$Form.Controls.Add($DetectRemoteBtn)
$Form.Controls.Add($SetRemoteBtn)
$Form.Controls.Add($ForkBtn)
$Form.Controls.Add($PushBtn)
$Form.Controls.Add($PullBtn)
$Form.Controls.Add($VerifyBtn)

$LogBox = New-Object System.Windows.Forms.TextBox
$LogBox.Location = New-Object System.Drawing.Point(10,120)
$LogBox.Size = New-Object System.Drawing.Size(790,390)
$LogBox.Multiline = $true
$LogBox.ScrollBars = "Vertical"
$LogBox.ReadOnly = $true
$Form.Controls.Add($LogBox)

$StatusBtn.Add_Click({
  $dir = $RepoPath.Text
  Append-Log "Running: git status -sb"
  Run-Git "status -sb" $dir | Out-Null
})

$DetectRemoteBtn.Add_Click({
  $dir = $RepoPath.Text
  Append-Log "Detecting remote: git remote -v"
  $tmpFile = [System.IO.Path]::GetTempFileName()
  $code = Run-Git "remote -v" $dir
  if ($code -eq 0) {
    $output = git -C "$dir" remote -v 2>$null
    $origin = $output | Where-Object { $_ -like "origin* (fetch)" }
    if ($origin) {
      $parts = $origin -split "\s+"
      if ($parts.Length -ge 2) { $RemoteUrl.Text = $parts[1]; Append-Log ("Found origin: " + $parts[1]) }
    } else { Append-Log "No origin remote found." }
  } else { Append-Log "git remote -v failed." }
})

$SetRemoteBtn.Add_Click({
  $dir = $RepoPath.Text
  $url = $RemoteUrl.Text
  if ([string]::IsNullOrWhiteSpace($url)) { Append-Log "Remote URL is empty."; return }
  Append-Log ("Setting remote to: " + $url)
  Run-Git "remote remove origin" $dir | Out-Null
  $exit = Run-Git ("remote add origin `"" + $url + "`"") $dir
  if ($exit -eq 0) { Append-Log "origin set." } else { Append-Log "Failed to set origin." }
})

$ForkBtn.Add_Click({
  $dir = $RepoPath.Text
  if (-not (Has-Gh)) { Append-Log "gh CLI not found. Install: winget install GitHub.cli"; return }
  Append-Log "Forking repo with gh (and adding remote)..."
  $exit = Run-Gh "repo fork --remote" $dir
  if ($exit -eq 0) { Append-Log "Forked and remote added (origin)." } else { Append-Log "Fork failed." }
})

$PushBtn.Add_Click({
  $dir = $RepoPath.Text
  Append-Log "Staging changes..."
  Run-Git "add -A" $dir | Out-Null
  $dirty = git -C "$dir" status --porcelain
  if ($dirty) {
    Append-Log "Committing..."
    Run-Git "commit -m `"chore: update`"" $dir | Out-Null
  } else {
    Append-Log "No changes to commit."
  }
  Append-Log "Ensuring main branch..."
  Run-Git "branch -M main" $dir | Out-Null
  Append-Log "Pushing to origin/main..."
  $exit = Run-Git "push -u origin main" $dir
  if ($exit -eq 0) { Append-Log "Push OK." } else { Append-Log "Push failed. Check credentials/remote." }
})

$PullBtn.Add_Click({
  $dir = $RepoPath.Text
  Append-Log "Pulling (rebase) from origin/main..."
  $exit = Run-Git "pull --rebase origin main" $dir
  if ($exit -eq 0) { Append-Log "Pull OK." } else { Append-Log "Pull failed." }
})

$VerifyBtn.Add_Click({
  $dir = $RepoPath.Text
  Append-Log "Verifying remote HEAD vs local..."
  $local = (git -C "$dir" rev-parse HEAD).Trim()
  $remoteLine = (git -C "$dir" ls-remote origin HEAD) 2>$null
  $remote = ""
  if ($remoteLine) { $remote = ($remoteLine -split "`t")[0] }
  Append-Log ("Local:  " + $local)
  Append-Log ("Remote: " + $remote)
  if ($remote -and ($remote -eq $local)) { Append-Log "MATCH: Remote HEAD matches local." } else { Append-Log "MISMATCH: Push may be needed or remote unreachable." }
})

[void]$Form.ShowDialog()


