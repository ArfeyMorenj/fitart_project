$collection = Get-Content "FitArt_JPAS_API_Complete.postman_collection.json" -Raw | ConvertFrom-Json

Write-Host "=== VALIDASI POSTMAN COLLECTION ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "Collection: $($collection.info.name)"
Write-Host "Total Sections: $($collection.item.Count)"
Write-Host ""

# Build endpoint list
$endpoints = @{}
$duplicates = @{}
$issues = @()

function Extract-Endpoints {
    param($item, $path = "")
    
    $currentPath = if ($path) { "$path > $($item.name)" } else { $item.name }
    
    if ($item.request -and $item.request.method) {
        # This is a request
        $url = ""
        if ($item.request.url) {
            if ($item.request.url.path) {
                $url = "/" + ($item.request.url.path -join "/")
            }
        }
        
        $method = $item.request.method
        $key = "$method $url"
        
        $endpoints[$currentPath] = @{
            Method = $method
            URL = $url
            Name = $item.name
            FullPath = $currentPath
        }
        
        if ($duplicates.ContainsKey($key)) {
            $duplicates[$key] += @($currentPath)
        } else {
            $duplicates[$key] = @($currentPath)
        }
    } elseif ($item.item) {
        # This is a folder
        foreach ($subitem in $item.item) {
            Extract-Endpoints -item $subitem -path $currentPath
        }
    }
}

# Extract all endpoints
foreach ($section in $collection.item) {
    Extract-Endpoints -item $section
}

Write-Host ""
Write-Host "📊 STRUCTURE PER SECTION:"
Write-Host "=========================" -ForegroundColor Green
Write-Host ""

$bySection = @{}
foreach ($path in $endpoints.Keys) {
    $parts = $path -split " > "
    $section = $parts[0]
    
    if (-not $bySection.ContainsKey($section)) {
        $bySection[$section] = @()
    }
    $bySection[$section] += $path
}

$bySection.Keys | Sort-Object | ForEach-Object {
    $section = $_
    $count = $bySection[$section].Count
    Write-Host "✅ $section ($count items)"
}

Write-Host ""
Write-Host "=== DETAILED CHECK - 06 FINANCE ===" -ForegroundColor Yellow
Write-Host "====================================="
Write-Host ""

$hasInvoiceLicense = $false
$hasInvoiceLicenseList = $false
$hasInvoiceLicenseDetail = $false
$hasDebitCreditSearch = $false

$financeEndpoints = $bySection['06 Finance']
foreach ($path in $financeEndpoints) {
    $ep = $endpoints[$path]
    
    if ($path -like "*invoice-license*") {
        $hasInvoiceLicense = $true
        if ($ep.URL -eq "/api/invoice-license" -and $ep.Method -eq "GET") {
            $hasInvoiceLicenseList = $true
            Write-Host "✅ GET /api/invoice-license (LIST)"
        }
        if ($ep.URL -like "/api/invoice-license/*" -and $ep.Method -eq "GET" -and $ep.URL -notlike "*search*" -and $ep.URL -notlike "*summary*") {
            $hasInvoiceLicenseDetail = $true
            Write-Host "✅ GET $($ep.URL) (DETAIL)"
        }
    }
    
    if ($path -like "*debit-credit-notes*" -and $ep.URL -eq "/api/debit-credit-notes/search") {
        $hasDebitCreditSearch = $true
        Write-Host "✅ GET /api/debit-credit-notes/search"
    }
}

if (-not $hasInvoiceLicense) {
    Write-Host "❌ MISSING: invoice-license folder and endpoints" -ForegroundColor Red
    $issues += "MISSING: invoice-license folder"
} else {
    if (-not $hasInvoiceLicenseList) {
        Write-Host "❌ MISSING: GET /api/invoice-license" -ForegroundColor Red
        $issues += "MISSING: GET /api/invoice-license"
    }
    if (-not $hasInvoiceLicenseDetail) {
        Write-Host "❌ MISSING: GET /api/invoice-license/{id}" -ForegroundColor Red
        $issues += "MISSING: GET /api/invoice-license/{id}"
    }
}

if (-not $hasDebitCreditSearch) {
    Write-Host "❌ MISSING: GET /api/debit-credit-notes/search" -ForegroundColor Red
    $issues += "MISSING: GET /api/debit-credit-notes/search"
}

Write-Host ""
Write-Host "=== DETAILED CHECK - 07 POSTING ===" -ForegroundColor Yellow
Write-Host "===================================="
Write-Host ""

$hasPostingPaymentsGetList = $false
$hasPostingPaymentsGetSearch = $false
$hasPostingPaymentsGetDetail = $false

$postingEndpoints = $bySection['07 Posting']
foreach ($path in $postingEndpoints) {
    $ep = $endpoints[$path]
    
    if ($path -like "*posting-payments*" -and $ep.Method -eq "GET") {
        if ($ep.URL -eq "/api/posting/payments") {
            $hasPostingPaymentsGetList = $true
            Write-Host "✅ GET /api/posting/payments (LIST)"
        } elseif ($ep.URL -eq "/api/posting/payments/search") {
            $hasPostingPaymentsGetSearch = $true
            Write-Host "✅ GET /api/posting/payments/search"
        } elseif ($ep.URL -like "/api/posting/payments/*" -and $ep.URL -notlike "*costs*" -and $ep.URL -notlike "*search*" -and $ep.URL -notlike "*summary*") {
            $hasPostingPaymentsGetDetail = $true
            Write-Host "✅ GET $($ep.URL) (DETAIL)"
        }
    }
}

if (-not $hasPostingPaymentsGetList) {
    Write-Host "❌ MISSING: GET /api/posting/payments" -ForegroundColor Red
    $issues += "MISSING: GET /api/posting/payments"
}
if (-not $hasPostingPaymentsGetSearch) {
    Write-Host "❌ MISSING: GET /api/posting/payments/search" -ForegroundColor Red
    $issues += "MISSING: GET /api/posting/payments/search"
}
if (-not $hasPostingPaymentsGetDetail) {
    Write-Host "❌ MISSING: GET /api/posting/payments/{id}" -ForegroundColor Red
    $issues += "MISSING: GET /api/posting/payments/{id}"
}

Write-Host ""
Write-Host "=== DUPLICATE CHECK ===" -ForegroundColor Yellow
Write-Host "========================"
Write-Host ""

$hasDuplicates = $false
foreach ($key in $duplicates.Keys) {
    if ($duplicates[$key].Count -gt 1) {
        $hasDuplicates = $true
        Write-Host "❌ DUPLICATE: $key" -ForegroundColor Red
        foreach ($location in $duplicates[$key]) {
            Write-Host "   Found in: $location"
        }
    }
}

if (-not $hasDuplicates) {
    Write-Host "✅ NO DUPLICATES FOUND" -ForegroundColor Green
}

Write-Host ""
Write-Host "=== SUMMARY ===" -ForegroundColor Cyan
Write-Host "==============="
Write-Host ""

Write-Host "Total Endpoints: $($endpoints.Count)"
Write-Host "Total Sections: $($bySection.Count)"
Write-Host "Missing/Broken Endpoints: $($issues.Count)"

if ($issues.Count -gt 0) {
    Write-Host ""
    Write-Host "⚠️  ISSUES FOUND:" -ForegroundColor Yellow
    $issues | ForEach-Object { Write-Host "   ❌ $_" }
    Write-Host ""
    Write-Host "👉 Status: COLLECTION INCOMPLETE - Needs fixes" -ForegroundColor Red
} else {
    Write-Host ""
    Write-Host "✅ Status: COLLECTION IS COMPLETE AND ORGANIZED" -ForegroundColor Green
}
