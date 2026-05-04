<?php
/**
 * COMPREHENSIVE API ANALYSIS - CORRECTED VERSION
 * Comparing: routes/api.php vs FitArt_JPAS_API_Complete.postman_collection.json
 * 
 * Tujuan: Validate Claude AI's analysis dan beri hasil yang lebih akurat
 */

// ============================================
// PART 1: EXTRACT BACKEND ROUTES (dari routes/api.php)
// ============================================

$routesContent = file_get_contents(__DIR__ . '/routes/api.php');

// Extract semua Route:: definitions
$backendEndpoints = [];

// Pattern 1: Route::get(), Route::post(), Route::put(), Route::delete(), Route::patch()
if (preg_match_all("/Route::(get|post|put|delete|patch)\\(['\"]([^'\"]+)['\"]/", $routesContent, $matches)) {
    foreach ($matches[1] as $idx => $method) {
        $path = $matches[2][$idx];
        $backendEndpoints[] = strtoupper($method) . ' /' . ltrim($path, '/');
    }
}

// Pattern 2: apiResource() - generates 5 endpoints
// Standard patterns: GET /, GET /{id}, POST /, PUT /{id}, DELETE /{id}
if (preg_match_all("/Route::apiResource\\(['\"]([^'\"]+)['\"]/", $routesContent, $matches)) {
    foreach ($matches[1] as $resource) {
        $backendEndpoints[] = "GET /" . ltrim($resource, '/');              // index
        $backendEndpoints[] = "POST /" . ltrim($resource, '/');             // store
        $backendEndpoints[] = "GET /" . ltrim($resource, '/') . "/{id}";    // show
        $backendEndpoints[] = "PUT /" . ltrim($resource, '/') . "/{id}";    // update
        $backendEndpoints[] = "DELETE /" . ltrim($resource, '/') . "/{id}"; // destroy
    }
}

// Dedupe
$backendEndpoints = array_values(array_unique($backendEndpoints));
sort($backendEndpoints);

echo "=== BACKEND ROUTES (routes/api.php) ===\n";
echo "Total endpoints: " . count($backendEndpoints) . "\n\n";

// ============================================
// PART 2: LOAD POSTMAN COLLECTION
// ============================================

$postmanFile = __DIR__ . '/FitArt_JPAS_API_Complete.postman_collection.json';
if (!file_exists($postmanFile)) {
    die("ERROR: Postman collection file tidak ditemukan: $postmanFile\n");
}

// Increase limits for large JSON
ini_set('memory_limit', '512M');

$postmanContent = file_get_contents($postmanFile);
if ($postmanContent === false) {
    die("ERROR: Tidak bisa membaca file Postman collection\n");
}

$postmanJson = json_decode($postmanContent, true);
if (!$postmanJson) {
    $error = json_last_error_msg();
    die("ERROR: Tidak bisa parse Postman collection JSON: $error\n");
}

$postmanEndpoints = [];
$postmanWithResponse = [];
$postmanWithoutResponse = [];

// Recursive function untuk extract Postman items
function extractPostmanItems($items, &$endpoints, &$withResp, &$withoutResp) {
    if (!is_array($items)) return;
    
    foreach ($items as $item) {
        if (!is_array($item)) continue;
        
        // Jika ada subitems (folder)
        if (isset($item['item']) && is_array($item['item'])) {
            extractPostmanItems($item['item'], $endpoints, $withResp, $withoutResp);
        }
        
        // Jika ada request
        if (isset($item['request']) && is_array($item['request'])) {
            $method = $item['request']['method'] ?? 'GET';
            
            // Extract path dari URL
            $url = $item['request']['url'] ?? [];
            $path = '';
            
            if (is_string($url)) {
                // URL bisa jadi string
                if (strpos($url, '/api') !== false) {
                    $path = substr($url, strpos($url, '/api'));
                } else {
                    $path = '/' . ltrim($url, '/');
                }
            } elseif (is_array($url)) {
                // Atau array dengan path, host, dll
                if (isset($url['path']) && is_array($url['path'])) {
                    $path = '/' . implode('/', $url['path']);
                } elseif (isset($url['path']) && is_string($url['path'])) {
                    $path = '/' . ltrim($url['path'], '/');
                }
            }
            
            if (empty($path)) continue;
            
            $key = strtoupper($method) . ' ' . $path;
            $hasResp = isset($item['response']) && is_array($item['response']) && count($item['response']) > 0;
            
            $endpoints[$key] = [
                'method' => strtoupper($method),
                'path' => $path,
                'name' => $item['name'] ?? '',
                'hasResponse' => $hasResp
            ];
            
            if ($hasResp) {
                $withResp[$key] = $endpoints[$key];
            } else {
                $withoutResp[$key] = $endpoints[$key];
            }
        }
    }
}

extractPostmanItems($postmanJson['item'] ?? [], $postmanEndpoints, $postmanWithResponse, $postmanWithoutResponse);

echo "\n=== POSTMAN COLLECTION ANALYSIS ===\n";
echo "Total unique endpoints: " . count($postmanEndpoints) . "\n";
echo "Endpoints with response examples: " . count($postmanWithResponse) . "\n";
echo "Endpoints WITHOUT response: " . count($postmanWithoutResponse) . "\n";

// ============================================
// PART 3: CROSS-CHECK VALIDATION
// ============================================

$postmanKeys = array_keys($postmanEndpoints);

// Normalize backend endpoints to match format
$backendNormalized = [];
foreach ($backendEndpoints as $ep) {
    $backendNormalized[] = $ep;
}

// Endpoints di Backend tapi TIDAK di Postman (case-insensitive match)
$missingFromPostman = [];
foreach ($backendNormalized as $backendEp) {
    $found = false;
    foreach ($postmanKeys as $postmanEp) {
        if (strtolower($backendEp) === strtolower($postmanEp)) {
            $found = true;
            break;
        }
    }
    if (!$found) {
        $missingFromPostman[] = $backendEp;
    }
}

// Endpoints di Postman tapi TIDAK di Backend (invalid)
$orphanInPostman = [];
foreach ($postmanKeys as $postmanEp) {
    $found = false;
    foreach ($backendNormalized as $backendEp) {
        if (strtolower($backendEp) === strtolower($postmanEp)) {
            $found = true;
            break;
        }
    }
    if (!$found) {
        $orphanInPostman[] = $postmanEp;
    }
}

// Endpoints yang documented & tested
$documentedTested = [];
$documentedUntested = [];

foreach ($postmanWithResponse as $key => $ep) {
    $documentedTested[$key] = $ep;
}

foreach ($postmanWithoutResponse as $key => $ep) {
    $documentedUntested[$key] = $ep;
}

echo "\n=== CROSS-CHECK RESULTS ===\n";
echo "Total Backend Routes: " . count($backendNormalized) . "\n";
echo "Total Postman Endpoints: " . count($postmanKeys) . "\n";
echo "\n";
echo "✅ DOCUMENTED & TESTED (ada di Postman + ada response): " . count($documentedTested) . "\n";
echo "⚠️  DOCUMENTED BUT UNTESTED (ada di Postman, tapi NO response): " . count($documentedUntested) . "\n";
echo "🚨 NOT DOCUMENTED (ada di Backend, tapi TIDAK ada di Postman): " . count($missingFromPostman) . "\n";
echo "❌ ORPHAN/INVALID (ada di Postman, tapi TIDAK ada di Backend): " . count($orphanInPostman) . "\n";

$coverage = count($backendNormalized) > 0 ? round((count($documentedTested) / count($backendNormalized)) * 100, 1) : 0;
echo "\n📊 Test Coverage: $coverage% (" . count($documentedTested) . "/" . count($backendNormalized) . ")\n";

// ============================================
// PART 4: DETAIL OUTPUT
// ============================================

echo "\n\n" . str_repeat("=", 80) . "\n";
echo "SECTION 1: ENDPOINTS YANG SUDAH TESTED ✅ (" . count($documentedTested) . " endpoint)\n";
echo str_repeat("=", 80) . "\n\n";

$idx = 1;
foreach (array_slice($documentedTested, 0, 30) as $ep) {
    echo "$idx. [TESTED] " . $ep['method'] . "\t" . $ep['path'] . "\n";
    $idx++;
}
if (count($documentedTested) > 30) {
    echo "\n... dan " . (count($documentedTested) - 30) . " endpoint lainnya\n";
}

echo "\n\n" . str_repeat("=", 80) . "\n";
echo "SECTION 2: ENDPOINTS DOCUMENTED TAPI BELUM DITEST ⚠️ (" . count($documentedUntested) . " endpoint)\n";
echo str_repeat("=", 80) . "\n\n";

$idx = 1;
foreach ($documentedUntested as $ep) {
    echo "$idx. [UNTESTED] " . $ep['method'] . "\t" . $ep['path'] . "\n";
    $idx++;
}

echo "\n\n" . str_repeat("=", 80) . "\n";
echo "SECTION 3: NOT DOCUMENTED - Ada di Backend tapi TIDAK ada di Postman 🚨 (" . count($missingFromPostman) . " endpoint)\n";
echo str_repeat("=", 80) . "\n\n";

if (count($missingFromPostman) === 0) {
    echo "✅ Baik! Semua backend routes sudah terdokumentasi di Postman.\n";
} else {
    $idx = 1;
    foreach ($missingFromPostman as $ep) {
        echo "$idx. [MISSING] " . $ep . "\n";
        $idx++;
    }
}

echo "\n\n" . str_repeat("=", 80) . "\n";
echo "SECTION 4: ORPHAN/INVALID - Ada di Postman tapi TIDAK ada di Backend ❌ (" . count($orphanInPostman) . " endpoint)\n";
echo str_repeat("=", 80) . "\n\n";

if (count($orphanInPostman) === 0) {
    echo "✅ Bagus! Tidak ada endpoint Postman yang orphan/invalid.\n";
} else {
    $idx = 1;
    foreach ($orphanInPostman as $ep) {
        echo "$idx. [ORPHAN] " . $ep . "\n";
        $idx++;
    }
}

// ============================================
// PART 5: FINAL SUMMARY
// ============================================

echo "\n\n" . str_repeat("=", 80) . "\n";
echo "FINAL SUMMARY & CORRECTED FINDINGS\n";
echo str_repeat("=", 80) . "\n\n";

echo "📊 PERBANDINGAN DENGAN ANALISIS CLAUDE AI:\n\n";
echo "Claude AI Report:\n";
echo "  - Total Backend Routes: 190\n";
echo "  - Tested (dengan response): 117 (61.6%)\n";
echo "  - Untested (tanpa response): 47 (24.7%)\n";
echo "  - Not documented: 26 (13.7%)\n";
echo "  - Coverage: 68.8%\n\n";

echo "Hasil Analysis Kami:\n";
echo "  - Total Backend Routes: " . count($backendNormalized) . "\n";
echo "  - Tested (dengan response): " . count($documentedTested) . " (" . round((count($documentedTested)/count($backendNormalized)*100), 1) . "%)\n";
echo "  - Untested (tanpa response): " . count($documentedUntested) . " (" . round((count($documentedUntested)/(count($documentedTested)+count($documentedUntested))*100), 1) . "%)\n";  
echo "  - Not documented: " . count($missingFromPostman) . "\n";
echo "  - Orphan/invalid: " . count($orphanInPostman) . "\n";
echo "  - Coverage: $coverage%\n\n";

echo "🔍 TEMUAN & KESIMPULAN:\n\n";

if (count($backendNormalized) !== 190) {
    echo "1. ⚠️  PERBEDAAN TOTAL ENDPOINT\n";
    echo "   Claude AI: 190 endpoint\n";
    echo "   Kami: " . count($backendNormalized) . " endpoint\n";
    echo "   Kemungkinan: Claude menghitung PATCH routes yang generated otomatis oleh apiResource().\n";
    echo "   Laravel apiResource() menghasilkan PUT & PATCH ke method yang sama, tapi di\n";
    echo "   real backend, biasanya hanya PUT yang digunakan.\n\n";
}

if (count($missingFromPostman) > 0 && count($missingFromPostman) !== 26) {
    echo "2. ⚠️  PERBEDAAN ENDPOINTS NOT DOCUMENTED\n";
    echo "   Claude AI: 26 endpoint\n";
    echo "   Kami: " . count($missingFromPostman) . " endpoint\n";
    echo "   Kemungkinan: Claude menghitung custom endpoints, kami menghitung semua.\n\n";
}

echo "3. ✅ VALIDATION RESULTS:\n";
if (count($orphanInPostman) === 0) {
    echo "   ✓ Postman collection SYNC dengan backend (tidak ada ghost endpoint)\n";
} else {
    echo "   ✗ Ada " . count($orphanInPostman) . " orphan endpoint di Postman (harus dihapus)\n";
}

if (count($missingFromPostman) === 0) {
    echo "   ✓ Semua backend routes terdokumentasi di Postman\n";
} else {
    echo "   ✗ Ada " . count($missingFromPostman) . " backend routes belum di-dokumentasikan\n";
}

echo "   ✓ Coverage Test: $coverage% (" . count($documentedTested) . " dari " . count($backendNormalized) . ")\n\n";

?>
