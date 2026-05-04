<?php

$jsonFile = 'FitArt_JPAS_API_Complete.postman_collection.json';
$collectionJson = file_get_contents($jsonFile);
$collection = json_decode($collectionJson, true);

echo "=== POSTMAN COLLECTION STRUCTURE ANALYSIS ===\n\n";

function analyzeFolder(&$folder, $level = 0, &$endpointList = []) {
    $indent = str_repeat("  ", $level);
    
    if (isset($folder['name'])) {
        echo $indent . "📁 " . $folder['name'] . "\n";
    }
    
    if (isset($folder['item']) && is_array($folder['item'])) {
        foreach ($folder['item'] as $subfolder) {
            if (isset($subfolder['name'])) {
                // Check if this is a request or a folder
                if (isset($subfolder['request'])) {
                    // This is a request
                    $method = isset($subfolder['request']['method']) ? $subfolder['request']['method'] : 'UNKNOWN';
                    $url = '';
                    if (isset($subfolder['request']['url'])) {
                        if (is_array($subfolder['request']['url'])) {
                            if (isset($subfolder['request']['url']['path'])) {
                                $url = '/' . implode('/', $subfolder['request']['url']['path']);
                            }
                        } else {
                            $url = $subfolder['request']['url'];
                        }
                    }
                    echo $indent . "  ├─ [$method] " . $subfolder['name'] . " → $url\n";
                    $endpointList[] = [
                        'method' => $method,
                        'path' => $url,
                        'name' => $subfolder['name'],
                        'folder' => $folder['name'] ?? 'Root'
                    ];
                } else {
                    // This is a folder
                    analyzeFolder($subfolder, $level + 1, $endpointList);
                }
            }
        }
    }
}

$allEndpoints = [];

echo "POSTMAN STRUCTURE:\n";
echo "==================\n\n";

foreach ($collection['item'] as $mainFolder) {
    analyzeFolder($mainFolder, 0, $allEndpoints);
    echo "\n";
}

// Count endpoints per section
echo "\n=== ENDPOINT COUNT BY SECTION ===\n";
$sectionCounts = [];
foreach ($allEndpoints as $ep) {
    $section = preg_replace('/\d+\s+/', '', $ep['folder']); // Remove "06 " prefix
    if (!isset($sectionCounts[$section])) {
        $sectionCounts[$section] = 0;
    }
    $sectionCounts[$section]++;
}

arsort($sectionCounts);
foreach ($sectionCounts as $section => $count) {
    echo "  $section: $count endpoints\n";
}

// Check for potential issues
echo "\n=== POTENTIAL ORGANIZATION ISSUES ===\n";

// 1. Check for requests directly in main folders
$directRequests = 0;
foreach ($collection['item'] as $mainFolder) {
    if (isset($mainFolder['item'])) {
        foreach ($mainFolder['item'] as $item) {
            if (isset($item['request']) && !isset($item['item'])) {
                echo "⚠️  Request not in subfolder: " . $item['name'] . " (in " . $mainFolder['name'] . ")\n";
                $directRequests++;
            }
        }
    }
}

if ($directRequests == 0) {
    echo "✅ All requests properly organized in subfolders\n";
}

// 2. Check for missing expected sections
echo "\n=== EXPECTED SECTIONS (01-11) ===\n";
$foundSections = [];
foreach ($collection['item'] as $mainFolder) {
    if (preg_match('/^(\d+)/', $mainFolder['name'], $m)) {
        $foundSections[$m[1]] = $mainFolder['name'];
    }
}

for ($i = 1; $i <= 11; $i++) {
    $num = str_pad($i, 2, '0', STR_PAD_LEFT);
    if (isset($foundSections[$i])) {
        echo "  ✅ $num: " . $foundSections[$i] . "\n";
    } else {
        echo "  ❌ $num: MISSING\n";
    }
}

// 3. Check specific sections for missing/misplaced endpoints
echo "\n=== CRITICAL ENDPOINTS CHECK ===\n";

$criticalEndpoints = [
    'Finance' => [
        'GET /api/invoice-license',
        'GET /api/invoice-license/{invoice}',
        'GET /api/debit-credit-notes/search',
    ],
    'Posting' => [
        'GET /api/posting/payments',
        'GET /api/posting/payments/search',
        'GET /api/posting/payments/{postingPayment}',
    ]
];

foreach ($allEndpoints as $ep) {
    foreach ($criticalEndpoints as $section => $expectedEp) {
        foreach ($expectedEp as $expected) {
            if (strpos($ep['name'], substr($expected, 5)) !== false || 
                $ep['path'] === str_replace('{invoice}', '{id}', preg_replace('/\{\w+\}/', '{id}', $expected))) {
                echo "✅ FOUND: $expected (in " . $ep['folder'] . ")\n";
                unset($criticalEndpoints[$section][array_search($expected, $criticalEndpoints[$section])]);
            }
        }
    }
}

echo "\n=== MISSING ENDPOINTS ===\n";
$missingCount = 0;
foreach ($criticalEndpoints as $section => $endpoints) {
    foreach ($endpoints as $ep) {
        if (!empty($ep)) {
            echo "❌ NOT FOUND: $ep\n";
            $missingCount++;
        }
    }
}

if ($missingCount == 0) {
    echo "✅ All critical endpoints found!\n";
} else {
    echo "\n⚠️  $missingCount critical endpoints still missing from collection\n";
}

echo "\n=== SUMMARY ===\n";
echo "Total endpoints in collection: " . count($allEndpoints) . "\n";
echo "Total sections: " . count($foundSections) . "/11\n";
echo "Status: ";
if ($missingCount > 0) {
    echo "⚠️  " . $missingCount . " endpoints need to be added\n";
} else {
    echo "✅ Collection is complete!\n";
}
