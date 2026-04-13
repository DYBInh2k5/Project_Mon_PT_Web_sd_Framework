$ErrorActionPreference = 'Stop'

$projectRoot = Split-Path -Parent $PSScriptRoot
Set-Location $projectRoot

Write-Host 'Stopping old Laravel server on port 8000 if it exists...'
$serverPids = @()
try {
    $serverPids = netstat -ano -p tcp |
        Select-String ':8000' |
        ForEach-Object {
            $parts = ($_ -replace '\s+', ' ').Trim().Split(' ')
            $pid = $parts[-1]
            if ($pid -match '^\d+$') { [int]$pid }
        } |
        Sort-Object -Unique
} catch {
    $serverPids = @()
}

foreach ($serverPid in $serverPids) {
    try {
        Stop-Process -Id $serverPid -Force -ErrorAction Stop
    } catch {
        Write-Warning "Could not stop process $serverPid"
    }
}

Write-Host 'Cleaning Laravel caches...'
php artisan optimize:clear | Out-Host

$dbFile = Join-Path $projectRoot 'database\database.sqlite'
$journalFile = Join-Path $projectRoot 'database\database.sqlite-journal'
$logFile = Join-Path $projectRoot 'storage\logs\laravel.log'

if (-not (Test-Path $dbFile)) {
    Write-Host 'Creating SQLite database file...'
    New-Item -ItemType File -Path $dbFile -Force | Out-Null
}

if (Test-Path $journalFile) {
    Write-Host 'Removing stale SQLite journal file...'
    attrib -R $journalFile 2>$null
    Remove-Item -LiteralPath $journalFile -Force
}

$dbInfo = Get-Item $dbFile
if ($dbInfo.Length -eq 0) {
    Write-Warning 'SQLite database file is empty. The script will stop here to protect your data.'
    Write-Warning 'If you really want to rebuild demo data manually, run: php artisan migrate:fresh --seed'
    exit 1
}
else {
    Write-Host 'Checking migrations...'
    php artisan migrate --force | Out-Host
}

if (Test-Path $logFile) {
    Write-Host 'Clearing old Laravel log...'
    Clear-Content $logFile
}

Write-Host 'Starting Laravel server at http://127.0.0.1:8000 ...'
Start-Process -FilePath powershell -ArgumentList '-NoProfile','-ExecutionPolicy','Bypass','-Command',"Set-Location '$projectRoot'; php artisan serve --host=127.0.0.1 --port=8000" -WindowStyle Hidden

Start-Sleep -Seconds 2

try {
    $status = (Invoke-WebRequest -UseBasicParsing http://127.0.0.1:8000).StatusCode
    Write-Host "Server is ready. HTTP $status"
}
catch {
    Write-Warning "Server start check failed: $($_.Exception.Message)"
}
