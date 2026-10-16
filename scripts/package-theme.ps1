$ErrorActionPreference = 'Stop'
$themeRoot = [System.IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..'))
$archivePath = Join-Path $themeRoot 'motorsky-theme.zip'
Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem
$files = @()
$files += Get-ChildItem -LiteralPath $themeRoot -File | Where-Object { $_.Extension -eq '.php' -or $_.Name -in @('style.css', 'README.md', 'screenshot.png') }
$files += Get-ChildItem -LiteralPath (Join-Path $themeRoot 'assets') -Recurse -File
$files += Get-ChildItem -LiteralPath (Join-Path $themeRoot 'includes') -Recurse -File
$stream = [System.IO.File]::Open($archivePath, [System.IO.FileMode]::Create)
$archive = [System.IO.Compression.ZipArchive]::new($stream, [System.IO.Compression.ZipArchiveMode]::Create)
try {
    foreach ($themeFile in $files) {
        $relative = $themeFile.FullName.Substring($themeRoot.Length + 1).Replace('\', '/')
        [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($archive, $themeFile.FullName, "motorsky/$relative", [System.IO.Compression.CompressionLevel]::Optimal) | Out-Null
    }
} finally {
    $archive.Dispose()
    $stream.Dispose()
}
Write-Output "Theme archive: $archivePath ($($files.Count) files)"
