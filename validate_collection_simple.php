<?php
// Simple validation script without encoding issues
$jsonFile = 'FitArt_JPAS_API_Complete.postman_collection.json';

if (!file_exists($jsonFile)) {
    die("ERROR: File $jsonFile not found\n");
}

$json = file_get_contents($jsonFile);
$collection = json_decode($json, true);

if (!$collection) {
    die("ERROR: Invalid JSON\n");
}

echo "=== POSTMAN COLLECTION VALIDATION ===\n";
echo "Collection: " . $collection['info']['name'] . "\n";
echo "Total Sections: " . count($collection['item']) . "\n\n";

// Extract all endpoints
$endpoints = [];
$endpointsByPath = [];

function extractEndpoints(&$endpoints, &$endpointsByPath, $items, $path = "") {
    foreach ($items as $item) {
        $currentPath = $path ? "$path > " . $item['name'] : $item['name'];
        
        if (isset($item['request']) && isset($item['request']['method'])) {
            // This is an endpoint
            $method = $item['request']['method'];
            $url = "";
            
            if (isset($item['request']['url'])) {
                if (is_array($item['request']['url']) && isset($item['request']['url']['path'])) {
                    $url = "/" . implode("/", $item['request']['url']['path']);
                } elseif (is_string($item['request']['url'])) {
                    $url = $item['request']['url'];
                }
            }
            
            $key = "$method $url";
            $endpoints[$key] = [
                'path' => $currentPath,
                'method' => $method,
                'url' => $url,
                'name' => $item['name']
            ];
            
            if (!isset($endpointsByPath[$currentPath])) {
                $endpointsByPath[$currentPath] = [];
            }
            $endpointsByPath[$currentPath][] = $key;
        }
        
        if (isset($item['item'])) {
            // This is a folder
            extractEndpoints($endpoints, $endpointsByPath, $item['item'], $currentPath);
        }
    }
}

extractEndpoints($endpoints, $endpointsByPath, $collection['item']);

echo "TOTAL ENDPOINTS: " . count($endpoints) . "\n\n";

// Check for duplicates
echo "=== CHECKING FOR DUPLICATES ===\n";
$duplicateFound = false;
$duplicates = [];

foreach ($endpoints as $key => $data) {
    foreach ($collection['item'] as $section) {
        checkDuplicates($section, $key, 0, $duplicates);
    }
}

function checkDuplicates($item, $searchKey, $depth, &$duplicates) {
    if (isset($item['request']) && isset($item['request']['method'])) {
        $method = $item['request']['method'];
        $url = "";
        
        if (isset($item['request']['url'])) {
            if (is_array($item['request']['url']) && isset($item['request']['url']['path'])) {
                $url = "/" . implode("/", $item['request']['url']['path']);
            } elseif (is_string($item['request']['url'])) {
                $url = $item['request']['url'];
            }
        }
        
        $key = "$method $url";
        if ($key === $searchKey) {
            if (!isset($duplicates[$searchKey])) {
                $duplicates[$searchKey] = 0;
            }
            $duplicates[$searchKey]++;
        }
    }
    
    if (isset($item['item'])) {
        foreach ($item['item'] as $subitem) {
            checkDuplicates($subitem, $searchKey, $depth + 1, $duplicates);
        }
    }
}

if (count($duplicates) > 0) {
    foreach ($duplicates as $key => $count) {
        if ($count > 1) {
            echo "[DUPLICATE] $key (appears $count times)\n";
            $duplicateFound = true;
        }
    }
} else {
    echo "OK - No duplicates found\n";
}

echo "\n";

// Check for specific missing endpoints
echo "=== CHECKING 6 MISSING ENDPOINTS ===\n";

$expectedEndpoints = [
    "GET /api/invoice-license" => false,
    "GET /api/invoice-license/{id}" => false,
    "GET /api/debit-credit-notes/search" => false,
    "GET /api/posting/payments" => false,
    "GET /api/posting/payments/search" => false,
    "GET /api/posting/payments/{id}" => false,
];

foreach ($endpoints as $key => $data) {
    foreach (array_keys($expectedEndpoints) as $expected) {
        if ($key === $expected) {
            $expectedEndpoints[$expected] = true;
        }
    }
}

foreach ($expectedEndpoints as $endpoint => $found) {
    $status = $found ? "FOUND" : "MISSING";
    echo "[$status] $endpoint\n";
}

echo "\n";

// Check structure per section
echo "=== SECTION STRUCTURE ===\n";
foreach ($collection['item'] as $section) {
    $count = countItems($section['item'] ?? []);
    echo "Section: " . $section['name'] . " (items: $count)\n";
}

function countItems($items) {
    $count = 0;
    foreach ($items as $item) {
        $count++;
        if (isset($item['item'])) {
            $count += countItems($item['item']);
        }
    }
    return $count;
}

echo "\n";

// Check Finance section details
echo "=== FINANCE SECTION (06) DETAILS ===\n";
foreach ($collection['item'] as $section) {
    if (strpos($section['name'], '06') !== false || strpos($section['name'], 'Finance') !== false) {
        showFolderStructure($section);
    }
}

function showFolderStructure($item, $indent = 0) {
    $prefix = str_repeat("  ", $indent);
    
    if (isset($item['request'])) {
        $method = $item['request']['method'] ?? 'UNKNOWN';
        $url = "";
        if (isset($item['request']['url'])) {
            if (is_array($item['request']['url']) && isset($item['request']['url']['path'])) {
                $url = "/" . implode("/", $item['request']['url']['path']);
            } elseif (is_string($item['request']['url'])) {
                $url = $item['request']['url'];
            }
        }
        echo "$prefix[$method] $url\n";
    } else {
        echo "$prefix[FOLDER] " . $item['name'] . "\n";
    }
    
    if (isset($item['item'])) {
        foreach ($item['item'] as $subitem) {
            showFolderStructure($subitem, $indent + 1);
        }
    }
}

echo "\n";

// Check Posting section details
echo "=== POSTING SECTION (07) DETAILS ===\n";
foreach ($collection['item'] as $section) {
    if (strpos($section['name'], '07') !== false || strpos($section['name'], 'Posting') !== false) {
        showFolderStructure($section);
    }
}

echo "\n=== ENDPOINT COUNT BY METHOD ===\n";
$methodCounts = [];
foreach ($endpoints as $key => $data) {
    $method = $data['method'];
    if (!isset($methodCounts[$method])) {
        $methodCounts[$method] = 0;
    }
    $methodCounts[$method]++;
}

foreach ($methodCounts as $method => $count) {
    echo "$method: $count\n";
}

?>
