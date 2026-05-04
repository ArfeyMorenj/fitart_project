<?php
$json_file = 'FitArt_JPAS_API_Complete.postman_collection.json';
$json_content = file_get_contents($json_file);
$collection = json_decode($json_content, true);

function extractEndpoints($items, $parentGroup = '') {
    $endpoints = [];
    
    foreach ($items as $item) {
        // If item has a request, it's an endpoint
        if (isset($item['request'])) {
            $method = $item['request']['method'] ?? 'N/A';
            $url = str_replace('{{base_url}}', '', $item['request']['url']['raw'] ?? '');
            
            // Extract parameters
            $params = [];
            if (isset($item['request']['url']['query'])) {
                foreach ($item['request']['url']['query'] as $query_param) {
                    if (isset($query_param['key'])) {
                        $params[] = $query_param['key'];
                    }
                }
            }
            
            // Extract path variables
            if (isset($item['request']['url']['variable'])) {
                foreach ($item['request']['url']['variable'] as $var) {
                    if (isset($var['key'])) {
                        $params[] = ':' . $var['key'];
                    }
                }
            }
            
            $endpoints[] = [
                'group' => $parentGroup,
                'name' => $item['name'] ?? 'N/A',
                'method' => $method,
                'url' => $url,
                'parameters' => $params
            ];
        }
        
        // If item has nested items, recurse
        if (isset($item['item'])) {
            $newGroup = $parentGroup ? $parentGroup . ' > ' . ($item['name'] ?? '') : ($item['name'] ?? '');
            $endpoints = array_merge($endpoints, extractEndpoints($item['item'], $newGroup));
        }
    }
    
    return $endpoints;
}

// Extract endpoints
$endpoints = extractEndpoints($collection['item'] ?? []);

// Organize by group
$grouped = [];
foreach ($endpoints as $endpoint) {
    $group = $endpoint['group'];
    if (!isset($grouped[$group])) {
        $grouped[$group] = [];
    }
    $grouped[$group][] = $endpoint;
}

// Print formatted output
ksort($grouped);

foreach ($grouped as $group => $groupEndpoints) {
    echo "\n" . str_repeat("=", 100) . "\n";
    echo "GROUP: " . $group . "\n";
    echo str_repeat("=", 100) . "\n";
    
    foreach ($groupEndpoints as $i => $endpoint) {
        echo "\n" . ($i + 1) . ". " . $endpoint['name'] . "\n";
        echo "   HTTP Method: " . $endpoint['method'] . "\n";
        echo "   URL Path: " . $endpoint['url'] . "\n";
        if (!empty($endpoint['parameters'])) {
            echo "   Parameters: " . implode(", ", $endpoint['parameters']) . "\n";
        } else {
            echo "   Parameters: None\n";
        }
    }
}

// Print summary
echo "\n\n" . str_repeat("=", 100) . "\n";
echo "SUMMARY\n";
echo str_repeat("=", 100) . "\n";
echo "Total Groups: " . count($grouped) . "\n";
echo "Total Endpoints: " . count($endpoints) . "\n";
echo "\nEndpoints by Group:\n";
foreach ($grouped as $group => $groupEndpoints) {
    echo "  " . $group . ": " . count($groupEndpoints) . " endpoints\n";
}
?>
