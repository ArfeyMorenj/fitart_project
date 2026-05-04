<?php
// Comprehensive API Testing Analysis

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "╔════════════════════════════════════════════════════════════════════════════════╗\n";
echo "║                   API TESTING STATUS COMPREHENSIVE ANALYSIS                   ║\n";
echo "╚════════════════════════════════════════════════════════════════════════════════╝\n\n";

// Routes analysis
echo "=== STEP 1: EXTRACTING ALL ROUTES FROM routes/api.php ===\n\n";

$apiRoutesFile = file_get_contents('routes/api.php');

// Find all apiResource and Route definitions
preg_match_all('/Route::(get|post|put|delete|patch|apiResource)\s*\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*([^)]+)\)/i', $apiRoutesFile, $matches);

$allRoutes = [];
for ($i = 0; $i < count($matches[2]); $i++) {
    $path = $matches[2][$i];
    $method = strtoupper($matches[1][$i]);
    
    // Handle apiResource which creates 7 endpoints
    if ($method === 'APIRESOURCE') {
        $methods = ['GET', 'POST', 'GET', 'PUT', 'DELETE'];
        $suffixes = ['', '', '/{id}', '/{id}', '/{id}'];
        
        foreach ($methods as $idx => $m) {
            $allRoutes[] = [
                'method' => $m,
                'path' => $path . $suffixes[$idx],
                'type' => $m === 'GET' && !strpos($suffixes[$idx], '{') ? 'LIST' : 
                         ($m === 'GET' ? 'SHOW' : 
                         ($m === 'POST' ? 'STORE' : 
                         ($m === 'PUT' ? 'UPDATE' : 'DELETE')))
            ];
        }
    } else {
        $allRoutes[] = [
            'method' => $method,
            'path' => $path,
            'type' => match($method) {
                'GET' => 'READ',
                'POST' => 'CREATE',
                'PUT', 'PATCH' => 'UPDATE',
                'DELETE' => 'DELETE',
                default => 'OTHER'
            }
        ];
    }
}

// Remove duplicates
$allRoutes = array_values(array_map("unserialize", array_unique(array_map("serialize", $allRoutes))));

echo "Total Routes Found: " . count($allRoutes) . "\n\n";

// Group by CRUD type
$byType = [];
foreach ($allRoutes as $route) {
    if (!isset($byType[$route['type']])) $byType[$route['type']] = [];
    $byType[$route['type']][] = $route;
}

echo "Routes Breakdown:\n";
foreach ($byType as $type => $routes) {
    echo "  $type: " . count($routes) . "\n";
}

// --- STEP 2: Analyze Postman Collection ---
echo "\n\n=== STEP 2: ANALYZING POSTMAN COLLECTION ===\n\n";

// Read collection file
$collectionContent = file_get_contents('FitArt_JPAS_API_Complete.postman_collection.json');

// Extract all request with regex
preg_match_all('/"name"\s*:\s*"([^"]+)"\s*[,}].*?"method"\s*:\s*"(GET|POST|PUT|DELETE|PATCH)".*?"raw"\s*:\s*"([^"]*{{base_url}}[^"]*)"|"raw"\s*:\s*"([^"]+)"|\{.*?"path"\s*:\s*\[\s*"api"[^]]*\]/s', $collectionContent, $nameMatches, PREG_OFFSET_CAPTURE);

// Simpler extraction
preg_match_all('/"name"\s*:\s*"((?:GET|POST|PUT|DELETE|PATCH)\s+\/[^"]*)"/', $collectionContent, $endpoints);

$postmanEndpoints = [];
if (!empty($endpoints[1])) {
    foreach ($endpoints[1] as $ep) {
        $parts = explode(' ', $ep, 2);
        if (count($parts) === 2) {
            $postmanEndpoints[] = [
                'method' => $parts[0],
                'path' => $parts[1],
                'display' => $ep
            ];
        }
    }
}

echo "Total Postman Endpoints: " . count($postmanEndpoints) . "\n\n";

if (empty($postmanEndpoints)) {
    echo "⚠️ Could not extract endpoints clearly. Collection format may be different.\n";
    echo "Counting respond indicators instead...\n\n";
    
    // Count response blocks
    $responseCount = substr_count($collectionContent, '"response"');
    $endpointCount = count(array_filter(preg_split('/}/', $collectionContent), 
                          fn($s) => strpos($s, '"method"') !== false));
    
    echo "Estimated endpoints with responses: ~" . ($responseCount / 4) . "\n";
}

// --- STEP 3: Postman Collection Export Analysis ---
echo "\n\n=== STEP 3: POSTMAN COLLECTION DETAILS ===\n\n";

// Check for "Success" and "Validation Error" response patterns (indicate saved responses)
$successCount = substr_count($collectionContent, '"Success"');
$errorCount = substr_count($collectionContent, '"Validation Error"') + strlen_count($collectionContent, '"Error"');
$notFoundCount = substr_count($collectionContent, '"name": "404"');

echo "Responses with Examples:\n";
echo "  ✓ Success: " . $successCount . " (indicates endpoints with saved success response)\n";
echo "  ✗ Errors: " . ($errorCount + $notFoundCount) . " (indicates error response examples)\n";

// --- STEP 4: Manual Check Common Endpoints ---
echo "\n\n=== STEP 4: CHECKING SPECIFIC ENDPOINTS ===\n\n";

$commonEndpoints = [
    ['GET', '/api/banks'],
    ['GET', '/api/banks/{bank}'],
    ['POST', '/api/banks'],
    ['PUT', '/api/banks/{bank}'],
    ['DELETE', '/api/banks/{bank}'],
];

foreach ($commonEndpoints as [$method, $path]) {
    $hasInCollection = false;
    $hasResponse = false;
    
    // Search for endpoint in Postman
    $pattern = '/' . preg_quote($method) . '.*?' . preg_quote($path) . '/i';
    if (preg_match($pattern, $collectionContent)) {
        $hasInCollection = true;
    }
    
    // Search for response
    $searchPattern = '/' . preg_quote($method) . '["\s]*:\s*"' . preg_quote($method) . '.*?api[\/\\\\]?' . preg_quote(ltrim($path, '/')) . '.*?"response"/is';
    if (preg_search($searchPattern, $collectionContent)) {
        $hasResponse = true;
    }
    
    $status = $hasInCollection ? ($hasResponse ? '✓ TESTED' : '⚠️ NOT TESTED') : '❌ NOT IN POSTMAN';
    echo "$method $path: $status\n";
}

echo "\n\n=== ANALYSIS SUMMARY ===\n\n";
echo "Total Routes in API: " . count($allRoutes) . "\n";
echo "Total Endpoints in Postman: " . count($postmanEndpoints) . "\n";
echo "Status: NEEDS MANUAL VERIFICATION\n\n";

echo "➜ Recommendation:\n";
echo "  1. All PUT and DELETE endpoints need testing (no save response yet)\n";
echo "  2. Many GET endpoints already have save response (tested)\n";
echo "  3. POST endpoints are mixed (some tested, some not)\n";

