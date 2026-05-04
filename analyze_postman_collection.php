<?php
// Script untuk analyze Postman collection vs Routes API

require 'vendor/autoload.php';

$collectionFile = 'FitArt_JPAS_API_Complete.postman_collection.json';
$collectionJson = json_decode(file_get_contents($collectionFile), true);

// Function untuk extract semua endpoints dari postman
function extractPostmanEndpoints($items, $path = '') {
    $endpoints = [];
    
    foreach ($items as $item) {
        // Jika punya request, ini adalah endpoint
        if (isset($item['request'])) {
            $method = $item['request']['method'] ?? 'UNKNOWN';
            $url = $item['request']['url']['raw'] ?? '';
            
            // Clean URL
            $url = str_replace('{{base_url}}/api/', '', $url);
            $url = str_replace('{{base_url}}/api', '', $url);
            
            // Check if ada response dengan save response
            $hasResponse = isset($item['response']) && !empty($item['response']);
            $responseCount = isset($item['response']) ? count($item['response']) : 0;
            
            $endpoints[] = [
                'name' => $item['name'] ?? 'Unknown',
                'method' => $method,
                'url' => $url,
                'has_response' => $hasResponse,
                'response_count' => $responseCount,
                'path' => $path ? $path . ' > ' . ($item['name'] ?? 'Unknown') : $item['name'] ?? 'Unknown'
            ];
        }
        
        // Jika ada nested items
        if (isset($item['item']) && is_array($item['item'])) {
            $newPath = $path ? $path . ' > ' . ($item['name'] ?? '') : ($item['name'] ?? '');
            $endpoints = array_merge($endpoints, extractPostmanEndpoints($item['item'], $newPath));
        }
    }
    
    return $endpoints;
}

echo "=== ANALYZING POSTMAN COLLECTION ===\n\n";

$postmanEndpoints = extractPostmanEndpoints($collectionJson['item']);

// Group by method
$byMethod = [];
foreach ($postmanEndpoints as $ep) {
    if (!isset($byMethod[$ep['method']])) {
        $byMethod[$ep['method']] = [];
    }
    $byMethod[$ep['method']][] = $ep;
}

// Summary
echo "TOTAL ENDPOINTS IN POSTMAN: " . count($postmanEndpoints) . "\n";
echo "Breakdown by Method:\n";
foreach ($byMethod as $method => $endpoints) {
    echo "  $method: " . count($endpoints) . "\n";
}

echo "\n=== ENDPOINTS WITH SAVE RESPONSE (Already Tested) ===\n";
$withResponse = array_filter($postmanEndpoints, fn($ep) => $ep['has_response']);
echo "Count: " . count($withResponse) . "\n\n";

foreach ($withResponse as $ep) {
    echo "[" . $ep['method'] . "] " . $ep['url'] . " (" . $ep['response_count'] . " responses)\n";
}

echo "\n=== ENDPOINTS WITHOUT SAVE RESPONSE (NOT Yet Tested) ===\n";
$noResponse = array_filter($postmanEndpoints, fn($ep) => !$ep['has_response']);
echo "Count: " . count($noResponse) . "\n\n";

$noResponseByMethod = [];
foreach ($noResponse as $ep) {
    if (!isset($noResponseByMethod[$ep['method']])) {
        $noResponseByMethod[$ep['method']] = [];
    }
    $noResponseByMethod[$ep['method']][] = $ep;
}

foreach ($noResponseByMethod as $method => $endpoints) {
    echo "\n[$method] - Count: " . count($endpoints) . "\n";
    foreach ($endpoints as $ep) {
        echo "  ❌ " . $ep['url'] . "\n";
    }
}

echo "\n=== ANALYSIS ===\n";
echo "Total: " . count($postmanEndpoints) . "\n";
echo "Tested (has response): " . count($withResponse) . " (" . round(count($withResponse)/count($postmanEndpoints)*100, 1) . "%)\n";
echo "Not Tested (no response): " . count($noResponse) . " (" . round(count($noResponse)/count($postmanEndpoints)*100, 1) . "%)\n";

// Detail not tested
echo "\n=== NOT TESTED BREAKDOWN ===\n";
foreach ($noResponseByMethod as $method => $endpoints) {
    $puts = array_filter($endpoints, fn($ep) => $ep['method'] === 'PUT');
    $deletes = array_filter($endpoints, fn($ep) => $ep['method'] === 'DELETE');
    
    echo "\n$method:\n";
    echo "  Total: " . count($endpoints) . "\n";
    
    // Show by CRUD type
    $create = array_filter($endpoints, fn($ep) => strpos($ep['url'], '{') === false && !preg_match('#/\d+$|/\{#', $ep['url']));
    $read = array_filter($endpoints, fn($ep) => preg_match('#/\d+$|/\{.*\}$#i', $ep['url']));
    $update = array_filter($endpoints, fn($ep) => $ep['method'] === 'PUT' || $ep['method'] === 'PATCH');
    $deleteOps = array_filter($endpoints, fn($ep) => $ep['method'] === 'DELETE');
    
    if ($method === 'GET') {
        echo "    (GET endpoints - should be tested)\n";
    } elseif ($method === 'POST') {
        echo "    (POST endpoints - should be tested)\n";
    } elseif ($method === 'PUT') {
        echo "    (PUT/UPDATE - NOT tested, needs implementation)\n";
    } elseif ($method === 'DELETE') {
        echo "    (DELETE - NOT tested, needs implementation)\n";
    }
}

// Save to file
file_put_contents('POSTMAN_ANALYSIS_CURRENT.json', json_encode([
    'total' => count($postmanEndpoints),
    'tested' => count($withResponse),
    'not_tested' => count($noResponse),
    'by_method' => $byMethod,
    'not_tested_details' => $noResponse,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "\n✓ Analysis saved to: POSTMAN_ANALYSIS_CURRENT.json\n";
