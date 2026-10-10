# Start the XAMPP/PHP development server with GD enabled for image uploads.
param([int]$Port = 8080)
$ErrorActionPreference = 'Stop'
Set-Location -LiteralPath $PSScriptRoot
$phpExecutable = if (Test-Path -LiteralPath 'C:\xampp\php\php.exe') { 'C:\xampp\php\php.exe' } else { (Get-Command php).Source }
$phpArgs = @('-d', 'variables_order=EGPCS')
$uploadTemp = Join-Path $PSScriptRoot 'writable/upload-tmp'
New-Item -ItemType Directory -Path $uploadTemp -Force | Out-Null
$phpArgs += @('-d', "upload_tmp_dir=$uploadTemp")
$modules = & $phpExecutable -m
if ($modules -notcontains 'gd') { $phpArgs += @('-d', 'extension=gd') }
Write-Host "Tamaraw POS: http://localhost:$Port"
Write-Host 'Keep this window open. Press Ctrl+C to stop.'
$env:CI_ENVIRONMENT = 'development'
$env:POS_LOCAL_BASE_URL = "http://localhost:$Port/"
& $phpExecutable @phpArgs -S "localhost:$Port" -t public tools/local-router.php
