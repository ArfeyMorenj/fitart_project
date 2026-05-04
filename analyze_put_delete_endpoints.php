<?php

$collectionFile = 'FitArt_JPAS_API_Complete.postman_collection.json';
$collection = json_decode(file_get_contents($collectionFile), true);

$putEndpoints = [];
$deleteEndpoints = [];

function extractEndpoints($items, &$putList, &$deleteList) {
    if (!is_array($items)) return;
    
    foreach ($items as $item) {
        if (isset($item['item']) && is_array($item['item'])) {
            // Recursive untuk nested items
            extractEndpoints($item['item'], $putList, $deleteList);
        }
        
        if (isset($item['request'])) {
            $method = $item['request']['method'] ?? null;
            $url = $item['request']['url']['raw'] ?? $item['request']['url']['path'][0] ?? null;
            $name = $item['name'] ?? null;
            $hasResponse = isset($item['response']) && !empty($item['response']);
            
            if ($method === 'PUT') {
                $putList[] = [
                    'name' => $name,
                    'method' => 'PUT',
                    'url' => $url,
                    'has_saved_response' => $hasResponse
                ];
            } elseif ($method === 'DELETE') {
                $deleteList[] = [
                    'name' => $name,
                    'method' => 'DELETE',
                    'url' => $url,
                    'has_saved_response' => $hasResponse
                ];
            }
        }
    }
}

extractEndpoints($collection['item'], $putEndpoints, $deleteEndpoints);

// Filter yang belum ada saved response (untested)
$putUntested = array_filter($putEndpoints, fn($x) => !$x['has_saved_response']);
$deleteUntested = array_filter($deleteEndpoints, fn($x) => !$x['has_saved_response']);

echo "===== PUT ENDPOINTS ANALYSIS =====\n";
echo "Total PUT: " . count($putEndpoints) . "\n";
echo "PUT Untested (no saved response): " . count($putUntested) . "\n\n";

echo "===== DELETE ENDPOINTS ANALYSIS =====\n";
echo "Total DELETE: " . count($deleteEndpoints) . "\n";
echo "DELETE Untested (no saved response): " . count($deleteUntested) . "\n\n";

echo "===== TOTAL PUT + DELETE UNTESTED =====\n";
echo "Grand Total: " . (count($putUntested) + count($deleteUntested)) . "\n\n";

echo "===== DETAIL PUT ENDPOINTS (UNTESTED) =====\n";
foreach ($putUntested as $idx => $ep) {
    echo ($idx + 1) . ". " . $ep['name'] . "\n";
    echo "   URL: " . $ep['url'] . "\n";
    echo "   Method: " . $ep['method'] . "\n";
    echo "   Status: ❌ UNTESTED\n\n";
}

echo "===== DETAIL DELETE ENDPOINTS (UNTESTED) =====\n";
foreach ($deleteUntested as $idx => $ep) {
    echo ($idx + 1) . ". " . $ep['name'] . "\n";
    echo "   URL: " . $ep['url'] . "\n";
    echo "   Method: " . $ep['method'] . "\n";
    echo "   Status: ❌ UNTESTED\n\n";
}

// Save detailed list to file
$report = [
    'put_total' => count($putEndpoints),
    'put_untested' => count($putUntested),
    'delete_total' => count($deleteEndpoints),
    'delete_untested' => count($deleteUntested),
    'grand_total_untested' => count($putUntested) + count($deleteUntested),
    'put_endpoints' => $putUntested,
    'delete_endpoints' => $deleteUntested
];

file_put_contents('put_delete_analysis.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "\n✅ Saved to: put_delete_analysis.json\n";
