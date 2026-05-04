<?php
/**
 * Comprehensive audit untuk SEMUA PUT/DELETE endpoints dalam Postman collection
 * Output: Endpoint mana saja yang tidak memiliki saved responses
 */

// Load Postman collection
$collection_file = __DIR__ . '/FitArt_JPAS_API_Complete.postman_collection.json';
$collection_json = file_get_contents($collection_file);

if (!$collection_json) {
    die("❌ File tidak ditemukan: $collection_file\n");
}

$collection = json_decode($collection_json, true);

if (!$collection) {
    die("❌ JSON tidak valid!\n");
}

// Array untuk menampung hasil
$put_delete_endpoints = [
    'PUT' => [],
    'DELETE' => []
];

/**
 * Recursive function untuk search items dalam collection structure
 * Postman bisa nested dengan folder & subfolder
 */
function search_items(&$items, &$results) {
    if (!is_array($items)) return;
    
    foreach ($items as $item) {
        // Jika ada folder (item di dalam folder)
        if (isset($item['item']) && is_array($item['item'])) {
            search_items($item['item'], $results);
        }
        
        // Jika ada request dengan method
        if (isset($item['request'])) {
            $method = $item['request']['method'] ?? null;
            $name = $item['name'] ?? 'Unknown';
            $url = $item['request']['url'];
            
            // Convert URL format untuk standardisasi
            if (is_array($url)) {
                $url_string = $url['raw'] ?? '';
            } else {
                $url_string = $url ?? '';
            }
            
            // Filter hanya PUT dan DELETE
            if (in_array($method, ['PUT', 'DELETE'])) {
                // Check responses
                $responses = $item['response'] ?? [];
                $has_responses = count($responses) > 0;
                
                $results[$method][] = [
                    'name' => $name,
                    'method' => $method,
                    'url' => $url_string,
                    'has_responses' => $has_responses,
                    'response_count' => count($responses)
                ];
            }
        }
    }
}

// Start searching
search_items($collection['item'] ?? [], $put_delete_endpoints);

echo "╔════════════════════════════════════════════════════════════════════════════════╗\n";
echo "║  COMPREHENSIVE PUT/DELETE ENDPOINT AUDIT - FITART POSTMAN COLLECTION           ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════════╝\n\n";

// Summary
$total_put = count($put_delete_endpoints['PUT']);
$total_delete = count($put_delete_endpoints['DELETE']);
$total_all = $total_put + $total_delete;

echo "📊 SUMMARY:\n";
echo "   Total PUT endpoints: " . $total_put . "\n";
echo "   Total DELETE endpoints: " . $total_delete . "\n";
echo "   TOTAL PUT/DELETE: " . $total_all . "\n";
echo "───────────────────────────────────────────────────────────────────────────────\n\n";

// Display PUT endpoints
echo "═══ PUT ENDPOINTS (" . $total_put . " total) ═══\n";
if ($total_put > 0) {
    $without_responses = array_filter($put_delete_endpoints['PUT'], fn($e) => !$e['has_responses']);
    
    echo "   ✅ WITH saved responses: " . ($total_put - count($without_responses)) . "\n";
    echo "   ❌ WITHOUT saved responses: " . count($without_responses) . "\n\n";
    
    foreach ($put_delete_endpoints['PUT'] as $idx => $endpoint) {
        $status = $endpoint['has_responses'] ? '✅' : '❌';
        $resp_count = $endpoint['has_responses'] ? "[" . $endpoint['response_count'] . " response]" : "[NO RESPONSE]";
        echo sprintf("%d. %s %s %s\n", $idx + 1, $status, $endpoint['name'], $resp_count);
        echo "   URL: " . $endpoint['url'] . "\n";
        if ($endpoint['response_count'] > 0) {
            echo "   Responses: " . $endpoint['response_count'] . "\n";
        }
        echo "\n";
    }
} else {
    echo "   (None found)\n\n";
}

// Display DELETE endpoints
echo "═══ DELETE ENDPOINTS (" . $total_delete . " total) ═══\n";
if ($total_delete > 0) {
    $without_responses = array_filter($put_delete_endpoints['DELETE'], fn($e) => !$e['has_responses']);
    
    echo "   ✅ WITH saved responses: " . ($total_delete - count($without_responses)) . "\n";
    echo "   ❌ WITHOUT saved responses: " . count($without_responses) . "\n\n";
    
    foreach ($put_delete_endpoints['DELETE'] as $idx => $endpoint) {
        $status = $endpoint['has_responses'] ? '✅' : '❌';
        $resp_count = $endpoint['has_responses'] ? "[" . $endpoint['response_count'] . " response]" : "[NO RESPONSE]";
        echo sprintf("%d. %s %s %s\n", $idx + 1, $status, $endpoint['name'], $resp_count);
        echo "   URL: " . $endpoint['url'] . "\n";
        if ($endpoint['response_count'] > 0) {
            echo "   Responses: " . $endpoint['response_count'] . "\n";
        }
        echo "\n";
    }
} else {
    echo "   (None found)\n\n";
}

// List all UNTESTED endpoints (no responses)
echo "═══ SUMMARY: UNTESTED ENDPOINTS (No Saved Responses) ═══\n";
$untested_put = array_filter($put_delete_endpoints['PUT'], fn($e) => !$e['has_responses']);
$untested_delete = array_filter($put_delete_endpoints['DELETE'], fn($e) => !$e['has_responses']);
$total_untested = count($untested_put) + count($untested_delete);

echo "   PUT (untested): " . count($untested_put) . "\n";
echo "   DELETE (untested): " . count($untested_delete) . "\n";
echo "   TOTAL UNTESTED: " . $total_untested . "\n\n";

if ($total_untested > 0) {
    echo "🔴 ENDPOINTS TO TEST:\n\n";
    
    foreach ($untested_put as $endpoint) {
        echo "PUT: " . $endpoint['name'] . "\n";
        echo "     " . $endpoint['url'] . "\n\n";
    }
    
    foreach ($untested_delete as $endpoint) {
        echo "DELETE: " . $endpoint['name'] . "\n";
        echo "        " . $endpoint['url'] . "\n\n";
    }
}

echo "═════════════════════════════════════════════════════════════════════════════\n";
echo "Scan selesai!\n";
?>
