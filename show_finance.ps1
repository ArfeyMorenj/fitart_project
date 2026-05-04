function ShowItems {
    param($items, $depth)
    
    foreach ($item in $items) {
        $indent = "  " * $depth
        
        if ($null -ne $item.request) {
            $method = $item.request.method
            $url = ""
            if ($item.request.url -is [string]) {
                $url = $item.request.url
            } elseif ($item.request.url.path) {
                $url = "/" + ($item.request.url.path -join "/")
            }
            Write-Host "$indent[$method] $url"
        } else {
            Write-Host "$indent[FOLDER] $($item.name)"
            if ($item.item) {
                ShowItems $item.item ($depth + 1)
            }
        }
    }
}

$collection = Get-Content 'FitArt_JPAS_API_Complete.postman_collection.json' -Raw | ConvertFrom-Json

Write-Host "=== FINANCE SECTION (06) DETAILED STRUCTURE ===" -ForegroundColor Yellow
Write-Host ""

foreach ($section in $collection.item) {
    if ($section.name -like "*06*" -or $section.name -like "*Finance*") {
        Write-Host "[FOLDER] $($section.name)"
        ShowItems $section.item 1
    }
}
