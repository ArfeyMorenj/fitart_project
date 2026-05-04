<?php
/**
 * PARSE ACTUAL POSTMAN COLLECTION
 * Cari endpoint mana yang BENAR-BENAR belum punya saved response
 */

ini_set('memory_limit', '512M');

echo "═══════════════════════════════════════════════════════════\n";
echo "ANALISIS POSTMAN COLLECTION - ENDPOINT YANG BELUM TEST\n";
echo "═══════════════════════════════════════════════════════════\n\n";

// Load Postman JSON
$postmanFile = __DIR__ . '/FitArt_JPAS_API_Complete.postman_collection.json';
$jsonStr = file_get_contents($postmanFile);

// Fix encoding issues
$jsonStr = preg_replace('/^\xEF\xBB\xBF/', '', $jsonStr);
$postman = json_decode($jsonStr, true);

if (!$postman) {
    die("ERROR: Cannot parse JSON\n");
}

// Variables untuk collect endpoints
$allEndpoints = [];
$untestedEndpoints = [];
$testedEndpoints = [];

// Function untuk extract recursively
function extractEndpoints($items, &$all, &$untested, &$tested) {
    if (!is_array($items)) return;
    
    foreach ($items as $item) {
        if (!is_array($item)) continue;
        
        // Recurse folders
        if (isset($item['item']) && is_array($item['item'])) {
            extractEndpoints($item['item'], $all, $untested, $tested);
        }
        
        // Extract endpoint
        if (isset($item['request'])) {
            $req = $item['request'];
            $method = strtoupper($req['method'] ?? 'GET');
            $url = $req['url'] ?? '';
            
            // Parse URL
            if (is_string($url)) {
                // Sometimes URL is full string like "localhost:8000/api/invoices"
                if (preg_match('/\/api(.*)/', $url, $m)) {
                    $path = '/api' . $m[1];
                } else {
                    $path = $url;
                }
            } elseif (is_array($url)) {
                // Sometimes URL is array like {"path": ["api", "invoices"], ...}
                $pathArr = $url['path'] ?? [];
                if (is_array($pathArr)) {
                    $path = '/' . implode('/', $pathArr);
                } else {
                    $path = '/' . $pathArr;
                }
            }
            
            if (!$path) continue;
            
            $key = "$method $path";
            $hasResponse = isset($item['response']) && is_array($item['response']) && count($item['response']) > 0;
            
            $all[$key] = [
                'name' => $item['name'] ?? '',
                'method' => $method,
                'path' => $path,
                'hasResponse' => $hasResponse,
                'responseCount' => is_array($item['response'] ?? null) ? count($item['response']) : 0
            ];
            
            if ($hasResponse) {
                $tested[$key] = $all[$key];
            } else {
                $untested[$key] = $all[$key];
            }
        }
    }
}

extractEndpoints($postman['item'] ?? [], $allEndpoints, $untestedEndpoints, $testedEndpoints);

echo "📊 STATISTIK POSTMAN COLLECTION:\n";
echo "   Total Endpoints: " . count($allEndpoints) . "\n";
echo "   ✅ Sudah Ada Saved Response (TESTED): " . count($testedEndpoints) . "\n";
echo "   ⚠️  BELUM Ada Saved Response (UNTESTED): " . count($untestedEndpoints) . "\n\n";

if (count($untestedEndpoints) > 0) {
    echo "═══════════════════════════════════════════════════════════\n";
    echo "ENDPOINT YANG BELUM DITEST (Tidak Ada Saved Response)\n";
    echo "═══════════════════════════════════════════════════════════\n\n";
    
    $idx = 1;
    foreach ($untestedEndpoints as $key => $ep) {
        echo "$idx. " . $key . "\n";
        echo "   Nama: " . $ep['name'] . "\n";
        echo "   Status: ⚠️  BELUM ADA SAVED RESPONSE\n";
        echo "   Action: Harus di-test, catat response-nya\n\n";
        $idx++;
    }
}

// ════════════════════════════════════════════════════════════
// NOW: Load Backend Routes
// ════════════════════════════════════════════════════════════

echo "\n═══════════════════════════════════════════════════════════\n";
echo "PARSING BACKEND ROUTES (routes/api.php)\n";
echo "═══════════════════════════════════════════════════════════\n\n";

$routesContent = file_get_contents(__DIR__ . '/routes/api.php');
$backendRoutes = [];

// Extract explicit routes
$patterns = [
    'GET' => "/Route::get\\(['\"]([^'\"]+)['\"]/",
    'POST' => "/Route::post\\(['\"]([^'\"]+)['\"]/",
    'PUT' => "/Route::put\\(['\"]([^'\"]+)['\"]/",
    'DELETE' => "/Route::delete\\(['\"]([^'\"]+)['\"]/",
    'PATCH' => "/Route::patch\\(['\"]([^'\"]+)['\"]/",
];

foreach ($patterns as $method => $pattern) {
    if (preg_match_all($pattern, $routesContent, $matches)) {
        foreach ($matches[1] as $path) {
            $fullPath = '/' . ltrim($path, '/');
            $backendRoutes[$method . ' ' . $fullPath] = true;
        }
    }
}

// Extract apiResource (each = 5 endpoints)
if (preg_match_all("/Route::apiResource\\(['\"]([^'\"]+)['\"]/", $routesContent, $matches)) {
    foreach ($matches[1] as $resource) {
        $res = '/' . ltrim($resource, '/');
        $backendRoutes['GET ' . $res] = true;
        $backendRoutes['POST ' . $res] = true;
        $backendRoutes['GET ' . $res . '/{id}'] = true;
        $backendRoutes['PUT ' . $res . '/{id}'] = true;
        $backendRoutes['DELETE ' . $res . '/{id}'] = true;
    }
}

// Sort
ksort($backendRoutes);

echo "✓ Total Backend Routes: " . count($backendRoutes) . "\n\n";

// ════════════════════════════════════════════════════════════
// CROSS-CHECK: Backend routes yang TIDAK ada di Postman
// ════════════════════════════════════════════════════════════

$notInPostman = [];
foreach ($backendRoutes as $route => $true) {
    if (!isset($allEndpoints[$route])) {
        $notInPostman[$route] = true;
    }
}

echo "═══════════════════════════════════════════════════════════\n";
echo "ENDPOINT YANG ADA DI BACKEND TAPI TIDAK ADA DI POSTMAN\n";
echo "═══════════════════════════════════════════════════════════\n\n";

if (count($notInPostman) === 0) {
    echo "✅ SEMUA backend routes sudah ada di Postman!\n\n";
} else {
    echo "🚨 TIDAK DIDOKUMENTASIKAN: " . count($notInPostman) . " endpoint\n\n";
    
    $idx = 1;
    foreach ($notInPostman as $route => $true) {
        echo "$idx. ❌ $route\n";
        echo "   Action: TAMBAHKAN KE POSTMAN\n";
        echo "\n";
        $idx++;
    }
}

// ════════════════════════════════════════════════════════════
// SUMMARY FOR ACTION
// ════════════════════════════════════════════════════════════

echo "\n═══════════════════════════════════════════════════════════\n";
echo "RANGKUMAN - APA YANG PERLU DIKERJAKAN\n";
echo "═══════════════════════════════════════════════════════════\n\n";

echo "1️⃣  ENDPOINT YANG BELUM DITEST (ada di Postman, tapi no response):\n";
echo "   Total: " . count($untestedEndpoints) . " endpoint\n";
echo "   Action: JALANKAN TEST, CATAT RESPONSE-NYA\n\n";

echo "2️⃣  ENDPOINT YANG TIDAK DIDOKUMENTASIKAN (ada di backend, tapi tidak di Postman):\n";
echo "   Total: " . count($notInPostman) . " endpoint\n";
echo "   Action: IMPORT KE POSTMAN COLLECTION\n\n";

echo "3️⃣  ENDPOINT YANG SUDAH TESTED (ada response):\n";
echo "   Total: " . count($testedEndpoints) . " endpoint ✅\n";
echo "   Action: Tidak perlu apa-apa, sudah good!\n\n";

?>
