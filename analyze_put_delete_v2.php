<?php

$json_string = file_get_contents('FitArt_JPAS_API_Complete.postman_collection.json');
$collection = json_decode($json_string, true);

$putEndpoints = [];
$deleteEndpoints = [];

function scanItems($items, &$puts, &$deletes, $module = '') {
    foreach ($items as $item) {
        $currentModule = $module;
        
        // Check if this is a module folder
        if (isset($item['item']) && is_array($item['item']) && count($item['item']) > 0) {
            $currentModule = $item['name'] ?? $module;
            scanItems($item['item'], $puts, $deletes, $currentModule);
        }
        
        // Check if this is a request
        if (isset($item['request']) && is_array($item['request'])) {
            $method = $item['request']['method'] ?? null;
            $name = $item['name'] ?? 'Unknown';
            
            // Extract URL
            $url = '';
            if (isset($item['request']['url'])) {
                if (is_string($item['request']['url'])) {
                    $url = $item['request']['url'];
                } elseif (isset($item['request']['url']['raw'])) {
                    $url = $item['request']['url']['raw'];
                } elseif (isset($item['request']['url']['path'])) {
                    $url = implode('/', $item['request']['url']['path']);
                }
            }
            
            // Check if has saved response
            $hasResponse = isset($item['response']) && is_array($item['response']) && count($item['response']) > 0;
            
            $data = [
                'name' => $name,
                'url' => $url,
                'module' => $currentModule,
                'has_response' => $hasResponse
            ];
            
            if ($method === 'PUT') {
                $puts[] = $data;
            } elseif ($method === 'DELETE') {
                $deletes[] = $data;
            }
        }
    }
}

scanItems($collection['item'], $putEndpoints, $deleteEndpoints);

// Filter untested (no saved response)
$putUntested = array_filter($putEndpoints, fn($x) => !$x['has_response']);
$deleteUntested = array_filter($deleteEndpoints, fn($x) => !$x['has_response']);

echo "=== POSTMAN COLLECTION ANALYSIS ===\n\n";
echo "📊 PUT ENDPOINTS:\n";
echo "  Total: " . count($putEndpoints) . "\n";
echo "  Tested (with saved response): " . (count($putEndpoints) - count($putUntested)) . "\n";
echo "  Untested (no saved response): " . count($putUntested) . "\n\n";

echo "📊 DELETE ENDPOINTS:\n";
echo "  Total: " . count($deleteEndpoints) . "\n";
echo "  Tested (with saved response): " . (count($deleteEndpoints) - count($deleteUntested)) . "\n";
echo "  Untested (no saved response): " . count($deleteUntested) . "\n\n";

echo "🔴 TOTAL PUT + DELETE UNTESTED: " . (count($putUntested) + count($deleteUntested)) . "\n\n";

// Save JSON
$report = [
    'summary' => [
        'put_total' => count($putEndpoints),
        'put_tested' => count($putEndpoints) - count($putUntested),
        'put_untested' => count($putUntested),
        'delete_total' => count($deleteEndpoints),
        'delete_tested' => count($deleteEndpoints) - count($deleteUntested),
        'delete_untested' => count($deleteUntested),
        'grand_total_untested' => count($putUntested) + count($deleteUntested)
    ],
    'put_untested_list' => array_values($putUntested),
    'delete_untested_list' => array_values($deleteUntested)
];

file_put_contents('put_delete_detailed_analysis.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "✅ Saved to: put_delete_detailed_analysis.json\n";
