
# Comprehensive API Analysis - PowerShell Version
# Extract & Cross-check Backend Routes vs PostmanCollection

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "COMPREHENSIVE API ANALYSIS" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan

# ===== PART 1: Extract Backend Routes from routes/api.php
Write-Host "`n=== PART 1: BACKEND ROUTES (routes/api.php) ===" -ForegroundColor Green

$routesContent = Get-Content "routes/api.php" -Raw

# Extract explicit routes (Route::get, Route::post, etc)
$explicitRoutes = @()
$routesContent | Select-String "Route::(get|post|put|delete|patch)\(['\"]([^'\"]+)['\"]" -AllMatches | ForEach-Object {
    $_.Matches | ForEach-Object {
        $method = $_.Groups[1].Value
        $path = "/" + ($_.Groups[2].Value -replace '^/', '')
        $explicitRoutes += "$($method.ToUpper()) $path"
    }
}

# Extract apiResource routes (generates 5 endpoints each)
$apiResources = @()
$routesContent | Select-String "Route::apiResource\(['\"]([^'\"]+)['\"]" -AllMatches | ForEach-Object {
    $_.Matches | ForEach-Object {
        $resource = "/" + ($_.Groups[1].Value -replace '^/', '')
        $apiResources += "GET $resource"
        $apiResources += "POST $resource"
        $apiResources += "GET $resource/{id}"
        $apiResources += "PUT $resource/{id}"
        $apiResources += "DELETE $resource/{id}"
    }
}

$allBackendRoutes = ($explicitRoutes + $apiResources) | Sort-Object -Unique
Write-Host "Total Backend Routes: $($allBackendRoutes.Count)" -ForegroundColor Yellow

# ===== PART 2: Load from Postman Collection
Write-Host "`n=== PART 2: POSTMAN COLLECTION ===" -ForegroundColor Green

$postmanJson = Get-Content "FitArt_JPAS_API_Complete.postman_collection.json" | ConvertFrom-Json

# Recursive function to extract items
$postmanEndpoints = @{}
$postmanTested = @{}
$postmanUntested = @{}

function ExtractPostmanItems {
    param(
        [array]$items,
        [hashtable]$endpoints,
        [hashtable]$tested,
        [hashtable]$untested
    )
    
    foreach ($item in $items) {
        # Recursive: check subitems (folders)
        if ($item.item -and $item.item.Count -gt 0) {
            ExtractPostmanItems $item.item $endpoints $tested $untested
        }
        
        # Extract request
        if ($item.request) {
            $method = $item.request.method -or "GET"
            $url = $item.request.url
            
            # Parse URL path
            if ($url -is [string]) {
                if ($url -like "*/api*") {
                    $path = $url -replace '.*(/api.*)$', '$1'
                } else {
                    $path = "/" + ($url -replace '^/', '')
                }
            } elseif ($url.path) {
                $path = "/" + (($url.path | Where-Object {$_}) -join "/")
            } else {
                continue
            }
            
            $key = "$($method.ToUpper()) $path"
            $hasResponse = $null -ne $item.response -and $item.response.Count -gt 0
            
            $endpoints[$key] = @{
                method = $method.ToUpper()
                path = $path
                name = $item.name
                hasResponse = $hasResponse
            }
            
            if ($hasResponse) {
                $tested[$key] = $endpoints[$key]
            } else {
                $untested[$key] = $endpoints[$key]
            }
        }
    }
}

ExtractPostmanItems $postmanJson.item $postmanEndpoints $postmanTested $postmanUntested

Write-Host "Total Postman Endpoints: $($postmanEndpoints.Count)" -ForegroundColor Yellow
Write-Host "  ✅ With Response (Tested): $($postmanTested.Count)" -ForegroundColor Green
Write-Host "  ⚠️  Without Response (Untested): $($postmanUntested.Count)" -ForegroundColor Yellow

# ===== PART 3: CROSS-CHECK
Write-Host "`n=== PART 3: CROSS-CHECK ===" -ForegroundColor Green

$backendSet = @{}
foreach ($route in $allBackendRoutes) {
    $backendSet[$route] = $true
}

$postmanSet = @{}
foreach ($ep in $postmanEndpoints.Keys) {
    $postmanSet[$ep] = $true
}

# Missing from Postman
$missingFromPostman = $allBackendRoutes | Where-Object { -not $postmanSet[$_] }

# Orphan in Postman
$orphanInPostman = $postmanEndpoints.Keys | Where-Object { -not $backendSet[$_] }

# Documented
$documented = $postmanEndpoints.Keys | Where-Object { $backendSet[$_] }

Write-Host "`n📊 RESULTS:" -ForegroundColor Cyan
Write-Host "  Backend Routes: $($allBackendRoutes.Count)" -ForegroundColor White
Write-Host "  Postman Endpoints: $($postmanEndpoints.Count)" -ForegroundColor White
Write-Host "`n  ✅ Tested (in Postman + has response): $($postmanTested.Count)" -ForegroundColor Green
Write-Host "  ⚠️  Untested (in Postman + NO response): $($postmanUntested.Count)" -ForegroundColor Yellow
Write-Host "  🚨 Not Documented (in Backend, NOT in Postman): $($missingFromPostman.Count)" -ForegroundColor Red
Write-Host "  ❌ Orphan (in Postman, NOT in Backend): $($orphanInPostman.Count)" -ForegroundColor Red

if ($allBackendRoutes.Count -gt 0) {
    $coverage = [Math]::Round(($postmanTested.Count / $allBackendRoutes.Count) * 100, 1)
    Write-Host "`n  📈 Test Coverage: $coverage% ($($postmanTested.Count)/$($allBackendRoutes.Count))" -ForegroundColor Cyan
}

# ===== PART 4: COMPARISON WITH CLAUDE AI
Write-Host "`n`n" + ("="*80) -ForegroundColor Cyan
Write-Host "COMPARISON WITH CLAUDE AI ANALYSIS" -ForegroundColor Cyan
Write-Host ("="*80) -ForegroundColor Cyan

Write-Host "`nCLAUDE AI REPORTED:" -ForegroundColor Yellow
Write-Host "  Total Backend Routes: 190" -ForegroundColor White
Write-Host "  Tested: 117 (61.6%)" -ForegroundColor White
Write-Host "  Untested: 47 (24.7%)" -ForegroundColor White
Write-Host "  Not Documented: 26 (13.7%)" -ForegroundColor White
Write-Host "  Coverage: 68.8%" -ForegroundColor White

Write-Host "`nOUR ANALYSIS SHOWS:" -ForegroundColor Yellow
Write-Host "  Total Backend Routes: $($allBackendRoutes.Count)" -ForegroundColor White
Write-Host "  Tested: $($postmanTested.Count)" -ForegroundColor White
Write-Host "  Untested: $($postmanUntested.Count)" -ForegroundColor White
Write-Host "  Not Documented: $($missingFromPostman.Count)" -ForegroundColor White
if ($allBackendRoutes.Count -gt 0) {
    $ourCoverage = [Math]::Round(($postmanTested.Count / $allBackendRoutes.Count) * 100, 1)
    Write-Host "  Coverage: $ourCoverage%" -ForegroundColor White
}

# ===== PART 5: DETAILED FINDINGS
Write-Host "`n`n" + ("="*80) -ForegroundColor Cyan
Write-Host "SECTION 1: TESTED ENDPOINTS ✅ ($($postmanTested.Count))" -ForegroundColor Green
Write-Host ("="*80) -ForegroundColor Cyan

$idx = 1
foreach ($ep in $postmanTested.Keys | Select-Object -First 40) {
    Write-Host "$idx. $ep" -ForegroundColor Green
    $idx++
}
if ($postmanTested.Count -gt 40) {
    Write-Host "... dan $($postmanTested.Count - 40) endpoint lainnya" -ForegroundColor Gray
}

Write-Host "`n`n" + ("="*80) -ForegroundColor Cyan
Write-Host "SECTION 2: UNTESTED ENDPOINTS ⚠️ ($($postmanUntested.Count))" -ForegroundColor Yellow
Write-Host ("="*80) -ForegroundColor Cyan

$idx = 1
foreach ($ep in $postmanUntested.Keys | Select-Object -First 30) {
    Write-Host "$idx. $ep" -ForegroundColor Yellow
    $idx++
}
if ($postmanUntested.Count -gt 30) {
    Write-Host "... dan $($postmanUntested.Count - 30) endpoint lainnya" -ForegroundColor Gray
}

Write-Host "`n`n" + ("="*80) -ForegroundColor Cyan
Write-Host "SECTION 3: MISSING FROM POSTMAN 🚨 ($($missingFromPostman.Count))" -ForegroundColor Red
Write-Host ("="*80) -ForegroundColor Cyan

if ($missingFromPostman.Count -eq 0) {
    Write-Host "`n✅ Bagus! Semua backend routes sudah terdokumentasi di Postman." -ForegroundColor Green
} else {
    $idx = 1
    foreach ($ep in $missingFromPostman) {
        Write-Host "$idx. [MISSING] $ep" -ForegroundColor Red
        $idx++
    }
}

Write-Host "`n`n" + ("="*80) -ForegroundColor Cyan
Write-Host "SECTION 4: ORPHAN/INVALID IN POSTMAN ❌ ($($orphanInPostman.Count))" -ForegroundColor Red
Write-Host ("="*80) -ForegroundColor Cyan

if ($orphanInPostman.Count -eq 0) {
    Write-Host "`n✅ Bagus! Tidak ada endpoint Postman yang orphan/invalid." -ForegroundColor Green
} else {
    $idx = 1
    foreach ($ep in $orphanInPostman) {
        Write-Host "$idx. [ORPHAN] $ep" -ForegroundColor Red
        $idx++
    }
}

# ===== FINAL SUMMARY
Write-Host "`n`n" + ("="*80) -ForegroundColor Cyan
Write-Host "FINAL SUMMARY & RECOMMENDATIONS" -ForegroundColor Cyan
Write-Host ("="*80) -ForegroundColor Cyan

Write-Host "`n🔍 KEY FINDINGS:" -ForegroundColor Cyan

if ($orphanInPostman.Count -bg 0) {
    Write-Host "  ⚠️  Ada $($orphanInPostman.Count) orphan endpoint di Postman yang harus dihapus" -ForegroundColor Red
}

if ($missingFromPostman.Count -gt 0) {
    Write-Host "  ⚠️  Ada $($missingFromPostman.Count) backend routes yang belum di-dokumentasikan" -ForegroundColor Red
}

Write-Host "  ✓ Test Coverage: $(if ($allBackendRoutes.Count -gt 0) { [Math]::Round(($postmanTested.Count / $allBackendRoutes.Count) * 100, 1) }else{'N/A'})%" -ForegroundColor Green

Write-Host "`n✅ RECOMMENDATIONS:" -ForegroundColor Cyan
Write-Host "  1. Review dan hapus orphan endpoints dari Postman collection" -ForegroundColor White
Write-Host "  2. Dokumentasikan missing backend routes ke Postman" -ForegroundColor White
Write-Host "  3. Prioritaskan testing untuk untested endpoints" -ForegroundColor White
Write-Host "  4. Focus on Finance & Posting modules (lowest coverage)" -ForegroundColor White

Write-Host "`n✨ Analysis complete! " -ForegroundColor Cyan

