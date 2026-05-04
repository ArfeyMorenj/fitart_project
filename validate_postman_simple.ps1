$jsonFile = 'FitArt_JPAS_API_Complete.postman_collection.json'
$collection = Get-Content $jsonFile -Raw | ConvertFrom-Json

Write-Host "Collection: $($collection.info.name)"
Write-Host "Total Sections: $($collection.item.Count)"
Write-Host ""

# Extract endpoints
$endpoints = [ordered]@{}
$allEndpoints = [ordered]@{}

function Extract-Endpoints {
    param($item, $path = "")
    
    $currentPath = if ($path) { "$path / $($item.name)" } else { $item.name }
    
    if ($null -ne $item.request -and $null -ne $item.request.method) {
        $method = $item.request.method
        $url = ""
        
        if ($null -ne $item.request.url) {
            if ($item.request.url -is [string]) {
                $url = $item.request.url
            } elseif ($item.request.url.path) {
                $url = "/" + ($item.request.url.path -join "/")
            }
        }
        
        $key = "$method $url"
        $allEndpoints[$key] = @{
            Path = $currentPath
            Method = $method
            URL = $url
            Name = $item.name
        }
        
        if (!$endpoints.Contains($key)) {
            $endpoints[$key] = @($currentPath)
        } else {
            $endpoints[$key] += $currentPath
        }
    } elseif ($null -ne $item.item) {
        foreach ($subitem in $item.item) {
            Extract-Endpoints -item $subitem -path $currentPath
        }
    }
}

foreach ($section in $collection.item) {
    Extract-Endpoints -item $section
}

Write-Host "TOTAL ENDPOINTS: $($allEndpoints.Count)"
Write-Host ""

# Check for duplicates
Write-Host "=== DUPLICATES CHECK ==="
$foundDuplicates = 0
foreach ($endpoint in $endpoints.Keys) {
    if ($endpoints[$endpoint].Count -gt 1) {
        Write-Host "[DUPLICATE] $endpoint"
        Write-Host "  Found in:"
        foreach ($path in $endpoints[$endpoint]) {
            Write-Host "    - $path"
        }
        $foundDuplicates++
    }
}

if ($foundDuplicates -eq 0) {
    Write-Host "[OK] No duplicates found"
}

Write-Host ""

# Check for 6 missing endpoints
Write-Host "=== MISSING ENDPOINTS CHECK ==="

$expectedEndpoints = @(
    "GET /api/invoice-license",
    "GET /api/invoice-license/{id}",
    "GET /api/debit-credit-notes/search",
    "GET /api/posting/payments",
    "GET /api/posting/payments/search",
    "GET /api/posting/payments/{id}"
)

$missingCount = 0
foreach ($expected in $expectedEndpoints) {
    if ($allEndpoints.Contains($expected)) {
        Write-Host "[FOUND] $expected"
        Write-Host "  Location: $($allEndpoints[$expected].Path)"
    } else {
        Write-Host "[MISSING] $expected"
        $missingCount++
    }
}

Write-Host ""
Write-Host "Missing Count: $missingCount / 6"
Write-Host ""

# Section breakdown
Write-Host "=== SECTIONS BREAKDOWN ==="
foreach ($section in $collection.item) {
    $itemCount = Count-Items $section
    Write-Host "$($section.name): $itemCount items"
}

function Count-Items {
    param($item)
    $count = 0
    if ($item.item) {
        $count = $item.item.Count
        foreach ($subitem in $item.item) {
            if ($subitem.item) {
                $count += Count-Items $subitem
            }
        }
    }
    return $count
}

Write-Host ""

# Detailed Finance section
Write-Host "=== FINANCE SECTION (06) STRUCTURE ==="
foreach ($section in $collection.item) {
    if ($section.name -like "*06*" -or $section.name -like "*Finance*") {
        Show-Structure $section 0
    }
}

function Show-Structure {
    param($item, $depth)
    
    $indent = "  " * $depth
    
    if ($null -ne $item.request) {
        $method = $item.request.method
        $url = ""
        if ($item.request.url -is [string]) {
            $url = $item.request.url
        } elseif ($item.request.url.path) {
            $url = "/" + ($item.request.url.path -join "/")
        }
        Write-Host "$indent[$method] $url - $($item.name)"
    } else {
        Write-Host "$indent[FOLDER] $($item.name)"
    }
    
    if ($item.item) {
        foreach ($subitem in $item.item) {
            Show-Structure $subitem ($depth + 1)
        }
    }
}

Write-Host ""

# Detailed Posting section
Write-Host "=== POSTING SECTION (07) STRUCTURE ==="
foreach ($section in $collection.item) {
    if ($section.name -like "*07*" -or $section.name -like "*Posting*") {
        Show-Structure $section 0
    }
}

Write-Host ""

# Method breakdown
Write-Host "=== METHOD BREAKDOWN ==="
$methodCounts = @{}
foreach ($endpoint in $allEndpoints.Keys) {
    $method = $endpoint.split(" ")[0]
    if (!$methodCounts[$method]) {
        $methodCounts[$method] = 0
    }
    $methodCounts[$method]++
}

foreach ($method in ($methodCounts.Keys | Sort-Object)) {
    Write-Host "$method : $($methodCounts[$method])"
}
