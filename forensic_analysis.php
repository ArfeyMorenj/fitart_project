<?php
/**
 * Enterprise API Forensic Analyzer - ENHANCED VERSION
 * Complete analysis of Postman vs Laravel backend
 */

// Load Postman collection
$postmanFile = __DIR__ . '/FitArt_JPAS_API_Complete.postman_collection.json';
$postmanJson = file_get_contents($postmanFile);
$postmanData = json_decode($postmanJson, true);

if (!$postmanData || !isset($postmanData['item'])) {
    die("Failed to load Postman collection\n");
}

echo "[INFO] Postman collection loaded successfully\n";
echo "[INFO] Total items in collection: " . count($postmanData['item']) . "\n\n";

// Parse all endpoints from Postman
$postmanEndpoints = [];
$testedEndpoints = [];
$untestedEndpoints = [];

function normalizeUrl($url) {
    // Remove Postman variables
    $url = str_replace('{{base_url}}', '', $url);
    // Clean up path
    $url = preg_replace('/\s+/', '', $url);
    return $url;
}

function parsePostmanItems($items, &$postmanEndpoints, &$testedEndpoints, &$untestedEndpoints, $module = '', $parent = '') {
    if (!is_array($items)) return;
    
    foreach ($items as $item) {
        if (!is_array($item)) continue;
        
        $itemName = $item['name'] ?? 'Unknown';
        
        // Check if this is a folder (has nested items)
        if (isset($item['item']) && is_array($item['item']) && count($item['item']) > 0) {
            // This is a folder, recursively parse children
            $newModule = isset($item['name']) ? $item['name'] : $module;
            parsePostmanItems($item['item'], $postmanEndpoints, $testedEndpoints, $untestedEndpoints, $newModule, $itemName);
        } elseif (isset($item['request']) && is_array($item['request'])) {
            // This is an endpoint
            $request = $item['request'];
            $method = isset($request['method']) ? strtoupper($request['method']) : 'GET';
            
            // Extract path from URL
            $path = '';
            if (isset($request['url'])) {
                if (is_string($request['url'])) {
                    $path = normalizeUrl($request['url']);
                } elseif (is_array($request['url'])) {
                    if (isset($request['url']['raw'])) {
                        $path = normalizeUrl($request['url']['raw']);
                    } elseif (isset($request['url']['path'])) {
                        $path = '/' . implode('/', $request['url']['path']);
                    }
                }
            }
            
            // Check if response exists (test status)
            $hasResponse = false;
            if (isset($item['response'])) {
                if (is_array($item['response']) && count($item['response']) > 0) {
                    // Check if any response has actual body content
                    foreach ($item['response'] as $response) {
                        if (isset($response['body']) && !empty($response['body'])) {
                            $hasResponse = true;
                            break;
                        }
                    }
                }
            }
            
            if (!empty($path)) {
                $endpoint = [
                    'method' => $method,
                    'path' => $path,
                    'name' => $itemName,
                    'module' => $module,
                    'parent' => $parent,
                    'hasResponse' => $hasResponse,
                    'status' => $hasResponse ? 'TESTED' : 'UNTESTED',
                    'responseCount' => isset($item['response']) ? count($item['response']) : 0
                ];
                
                $key = $method . ' ' . $path;
                $postmanEndpoints[$key] = $endpoint;
                
                if ($hasResponse) {
                    $testedEndpoints[$key] = $endpoint;
                } else {
                    $untestedEndpoints[$key] = $endpoint;
                }
            }
        }
    }
}

parsePostmanItems($postmanData['item'] ?? [], $postmanEndpoints, $testedEndpoints, $untestedEndpoints);

echo "[INFO] Postman endpoints parsed:\n";
echo "  Total: " . count($postmanEndpoints) . "\n";
echo "  Tested (with response): " . count($testedEndpoints) . "\n";
echo "  Untested (no response): " . count($untestedEndpoints) . "\n\n";

// Now parse Laravel routes
echo "[INFO] Parsing Laravel routes/api.php...\n";

$routesFile = __DIR__ . '/routes/api.php';
if (!file_exists($routesFile)) {
    die("Routes file not found: $routesFile\n");
}

$routesContent = file_get_contents($routesFile);

// Extract routes using reflection and regex
$laravel_endpoints = [];

// Pattern 1: Direct route definitions Route::get('path', ...)
preg_match_all("/Route::(get|post|put|delete|patch)\s*\(\s*['\"]([^'\"]+)['\"]/i", $routesContent, $matches, PREG_SET_ORDER);
foreach ($matches as $match) {
    $method = strtoupper($match[1]);
    $path = '/' . ltrim($match[2], '/');
    $key = $method . ' ' . $path;
    if (!isset($laravel_endpoints[$key])) {
        $laravel_endpoints[$key] = [
            'method' => $method,
            'path' => $path,
            'type' => 'direct',
            'line' => 'api.php'
        ];
    }
}

// Pattern 2: apiResource
preg_match_all("/Route::apiResource\s*\(\s*['\"]([^'\"]+)['\"]/i", $routesContent, $matches, PREG_SET_ORDER);
$apiResourceNameList = [];
foreach ($matches as $match) {
    $apiResourceNameList[] = $match[1];
}

foreach ($apiResourceNameList as $resource) {
    $basePath = '/' . $resource;
    
    // Standard RESTful endpoints
    $restEndpoints = [
        'GET ' . $basePath,
        'POST ' . $basePath,
        'GET ' . $basePath . '/{id}',
        'PUT ' . $basePath . '/{id}',
        'DELETE ' . $basePath . '/{id}'
    ];
    
    // Check for ->only() constraints
    $pattern = '/Route::apiResource\s*\(\s*[\'"' . preg_quote($resource) . '[\'\"]\s*\).*?(?:->only|->except)\s*\(\s*\[([^\]]+)\]/s';
    if (preg_match($pattern, $routesContent, $onlyMatch)) {
        $onlyStr = $onlyMatch[1];
        $only = [];
        preg_match_all('/[\'"](\w+)[\'"]/', $onlyStr, $onlyMatches);
        $only = $onlyMatches[1] ?? [];
        $restEndpoints = [];
        
        if (in_array('index', $only)) $restEndpoints[] = 'GET ' . $basePath;
        if (in_array('store', $only)) $restEndpoints[] = 'POST ' . $basePath;
        if (in_array('show', $only)) $restEndpoints[] = 'GET ' . $basePath . '/{id}';
        if (in_array('update', $only)) $restEndpoints[] = 'PUT ' . $basePath . '/{id}';
        if (in_array('destroy', $only)) $restEndpoints[] = 'DELETE ' . $basePath . '/{id}';
    }
    
    foreach ($restEndpoints as $endpoint) {
        $parts = explode(' ', $endpoint);
        $method = $parts[0];
        $path = $parts[1];
        if (!isset($laravel_endpoints[$endpoint])) {
            $laravel_endpoints[$endpoint] = [
                'method' => $method,
                'path' => $path,
                'type' => 'apiResource: ' . $resource
            ];
        }
    }
}

echo "[INFO] Laravel endpoints parsed:\n";
echo "  Total: " . count($laravel_endpoints) . "\n\n";

// Statistics
$totalLaravel = count($laravel_endpoints);
$totalPostman = count($postmanEndpoints);
$totalTested = count($testedEndpoints);
$totalUntested = count($untestedEndpoints);
$coveragePercent = $totalPostman > 0 ? round(($totalTested / $totalPostman) * 100, 2) : 0;

// Cross validation
$notInPostman = [];
$invalidInPostman = [];
$matchedEndpoints = [];

foreach ($laravel_endpoints as $leKey => $leEndpoint) {
    $found = false;
    foreach ($postmanEndpoints as $pmKey => $pmEndpoint) {
        if ($leEndpoint['path'] === $pmEndpoint['path'] && $leEndpoint['method'] === $pmEndpoint['method']) {
            $found = true;
            $matchedEndpoints[$leKey] = [
                'laravel' => $leEndpoint,
                'postman' => $pmEndpoint
            ];
            break;
        }
    }
    if (!$found) {
        $notInPostman[$leKey] = $leEndpoint;
    }
}

foreach ($postmanEndpoints as $pmKey => $pmEndpoint) {
    $found = false;
    foreach ($laravel_endpoints as $leKey => $leEndpoint) {
        if ($leEndpoint['path'] === $pmEndpoint['path'] && $leEndpoint['method'] === $pmEndpoint['method']) {
            $found = true;
            break;
        }
    }
    if (!$found) {
        $invalidInPostman[$pmKey] = $pmEndpoint;
    }
}

// Output Analysis
echo "\n";
echo "╔════════════════════════════════════════════════════════════════════╗\n";
echo "║     ENTERPRISE API FORENSIC ANALYSIS - COMPREHENSIVE REPORT        ║\n";
echo "╚════════════════════════════════════════════════════════════════════╝\n\n";

echo "┌─ SYSTEM OVERVIEW ──────────────────────────────────────────────────┐\n";
echo "│\n";
printf("│  Total Laravel Backend Endpoints:    %3d\n", $totalLaravel);
printf("│  Total Postman Endpoints:            %3d\n", $totalPostman);
printf("│  Total TESTED Endpoints:             %3d\n", $totalTested);
printf("│  Total UNTESTED Endpoints:           %3d\n", $totalUntested);
printf("│  Test Coverage:                      %5.2f%%\n", $coveragePercent);
printf("│  Matched (Backend ↔ Postman):        %3d\n", count($matchedEndpoints));
printf("│  Missing from Postman:               %3d (🚨 CRITICAL)\n", count($notInPostman));
printf("│  Invalid in Postman:                 %3d (⚠️  DEPRECATED)\n", count($invalidInPostman));
echo "│\n";
echo "└────────────────────────────────────────────────────────────────────┘\n\n";

// Group by module
$testByModule = [];
foreach ($testedEndpoints as $key => $endpoint) {
    $module = $endpoint['module'] ?? 'Unknown';
    if (!isset($testByModule[$module])) {
        $testByModule[$module] = ['tested' => 0, 'untested' => 0];
    }
    $testByModule[$module]['tested']++;
}

foreach ($untestedEndpoints as $key => $endpoint) {
    $module = $endpoint['module'] ?? 'Unknown';
    if (!isset($testByModule[$module])) {
        $testByModule[$module] = ['tested' => 0, 'untested' => 0];
    }
    $testByModule[$module]['untested']++;
}

echo "┌─ TEST COVERAGE BY MODULE ──────────────────────────────────────────┐\n";
echo "│\n";
foreach ($testByModule as $module => $counts) {
    $total = $counts['tested'] + $counts['untested'];
    $pct = $total > 0 ? round(($counts['tested'] / $total) * 100, 1) : 0;
    $status = $pct == 100 ? '✅' : ($pct == 0 ? '❌' : '⚠️');
    printf("│  %-30s  %d/%d (%5.1f%%) %s\n", $module . ':', $counts['tested'], $total, $pct, $status);
}
echo "│\n";
echo "└────────────────────────────────────────────────────────────────────┘\n\n";

echo "┌─ CRITICAL: NOT TESTED ENDPOINTS ──────────────────────────────────┐\n";
echo "│\n";
echo "│ Count: " . count($untestedEndpoints) . " endpoints without response data\n";
echo "│\n";
$count = 0;
foreach ($untestedEndpoints as $key => $endpoint) {
    if ($count >= 15) {
        echo "│ ... and " . (count($untestedEndpoints) - 15) . " more\n";
        break;
    }
    printf("│  • %-8s %-40s [%s]\n", $endpoint['method'], $endpoint['path'], $endpoint['module']);
    $count++;
}
echo "│\n";
echo "└────────────────────────────────────────────────────────────────────┘\n\n";

echo "┌─ 🚨 CRITICAL: MISSING FROM POSTMAN ───────────────────────────────┐\n";
echo "│\n";
echo "│ Count: " . count($notInPostman) . " backend endpoints NOT documented/tested\n";
echo "│ Risk Level: CRITICAL - These endpoints are production-ready but untested\n";
echo "│\n";
$count = 0;
foreach ($notInPostman as $key => $endpoint) {
    if ($count >= 20) {
        echo "│ ... and " . (count($notInPostman) - 20) . " more\n";
        break;
    }
    printf("│  • %-8s %-40s [%s]\n", $endpoint['method'], $endpoint['path'], $endpoint['type']);
    $count++;
}
echo "│\n";
echo "└────────────────────────────────────────────────────────────────────┘\n\n";

echo "┌─ ⚠️  INVALID IN POSTMAN ───────────────────────────────────────────┐\n";
echo "│\n";
echo "│ Count: " . count($invalidInPostman) . " endpoints in Postman but not in backend\n";
echo "│\n";
$count = 0;
foreach ($invalidInPostman as $key => $endpoint) {
    if ($count >= 10) {
        echo "│ ... and " . (count($invalidInPostman) - 10) . " more\n";
        break;
    }
    printf("│  • %-8s %-40s [%s]\n", $endpoint['method'], $endpoint['path'], $endpoint['module']);
    $count++;
}
echo "│\n";
echo "└────────────────────────────────────────────────────────────────────┘\n\n";

// Save detailed report
$report = [
    'timestamp' => date('Y-m-d H:i:s'),
    'summary' => [
        'totalLaravel' => $totalLaravel,
        'totalPostman' => $totalPostman,
        'totalTested' => $totalTested,
        'totalUntested' => $totalUntested,
        'coveragePercent' => $coveragePercent,
        'matched' => count($matchedEndpoints),
        'notInPostman' => count($notInPostman),
        'invalidInPostman' => count($invalidInPostman)
    ],
    'byModule' => $testByModule,
    'untestedEndpoints' => $untestedEndpoints,
    'notInPostman' => $notInPostman,
    'invalidInPostman' => $invalidInPostman,
    'matched' => $matchedEndpoints
];

file_put_contents(__DIR__ . '/forensic_analysis_report.json', json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "✅ Detailed report saved to: forensic_analysis_report.json\n";
?>
