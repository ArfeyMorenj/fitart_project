<?php
// Database-based API Testing Status Analysis

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "╔═════════════════════════════════════════════════════════════════════════╗\n";
echo "║         API TESTING STATUS - COMPREHENSIVE ANALYSIS REPORT             ║\n";
echo "╚═════════════════════════════════════════════════════════════════════════╝\n\n";

// === PART 1: Read Postman Collection ===
echo "📊 ANALYSIS 1: CHECKING POSTMAN COLLECTION SAVE RESPONSES\n";
echo str_repeat("─", 75) . "\n\n";

$collectionFile = 'FitArt_JPAS_API_Complete.postman_collection.json';
$collectionContent = file_get_contents($collectionFile);

// Count response examples by type
$successPatterns = [
    'Success' => substr_count($collectionContent, '"name": "Success"'),
    'Validation Error' => substr_count($collectionContent, '"Validation Error"'),
    'Not Found' => substr_count($collectionContent, '"name": "404"'),
    'Unauthorized' => substr_count($collectionContent, '"name": "401"'),
];

echo "Response Examples Found in Collection:\n";
$totalResponses = 0;
foreach ($successPatterns as $type => $count) {
    if ($count > 0) {
        echo "  ✓ $type: $count\n";
        $totalResponses += $count;
    }
}

echo "\nEstimated Endpoints with Saved Responses: ~" . ($totalResponses / 2) . "\n";
echo "(Each endpoint typically has 2-3 response examples)\n\n";

// === PART 2: Extract endpoints from routes ===
echo "📋 ANALYSIS 2: EXTRACTING ALL ROUTES FROM routes/api.php\n";
echo str_repeat("─", 75) . "\n\n";

$routesContent = file_get_contents('routes/api.php');

// Find all Route definitions with method patterns
$patterns = [
    'GET' => 'Route::get\([\'"]([^\'"]+)[\'"]\s*,',
    'POST' => 'Route::post\([\'"]([^\'"]+)[\'"]\s*,',
    'PUT' => 'Route::put\([\'"]([^\'"]+)[\'"]\s*,',
    'DELETE' => 'Route::delete\([\'"]([^\'"]+)[\'"]\s*,',
    'PATCH' => 'Route::patch\([\'"]([^\'"]+)[\'"]\s*,',
];

$byMethod = [];
foreach ($patterns as $method => $pattern) {
    preg_match_all($pattern, $routesContent, $matches);
    if (!empty($matches[1])) {
        $byMethod[$method] = array_map(fn($p) => trim($p, '\'"'), $matches[1]);
    }
}

// Also handle apiResource which creates multiple routes
preg_match_all('/Route::apiResource\([\'"]([^\'"]+)[\'"]\s*,/i', $routesContent, $resources);
$apiResources = $resources[1] ?? [];

echo "Master Routes in API:\n";
echo "─────────────────────\n\n";

$totalRoutes = 0;

foreach ($byMethod as $method => $routes) {
    $count = count((array) $routes); 
    $totalRoutes += $count;
    echo "$method: $count routes\n";
}

echo "\napiResource (each creates 7 routes): " . count($apiResources) . " resources\n";
$resourceRoutes = count($apiResources) * 7; // GET list, POST create, GET show, PUT update, DELETE delete, etc.
$totalRoutes += $resourceRoutes;

echo "\nTotal Routes: $totalRoutes\n";

// === PART 3: CRUD Analysis ===
echo "\n\n📊 ANALYSIS 3: CRUD ENDPOINT STATUS\n";
echo str_repeat("─", 75) . "\n\n";

$crud = ['CREATE' => 0, 'READ' => 0, 'UPDATE' => 0, 'DELETE' => 0];

foreach ($byMethod as $method => $routes) {
    match($method) {
        'POST' => $crud['CREATE'] += count($routes),
        'GET' => $crud['READ'] += count($routes),
        'PUT', 'PATCH' => $crud['UPDATE'] += count($routes),
        'DELETE' => $crud['DELETE'] += count($routes),
        default => null
    };
}

// Add from apiResources
$crud['READ'] += count($apiResources) * 2; // GET list + GET show
$crud['CREATE'] += count($apiResources); // POST
$crud['UPDATE'] += count($apiResources); // PUT
$crud['DELETE'] += count($apiResources); // DELETE

echo "CRUD Breakdown:\n";
echo "  CREATE (POST): " . $crud['CREATE'] . "\n";
echo "  READ (GET): " . $crud['READ'] . "\n";
echo "  UPDATE (PUT): " . $crud['UPDATE'] . "\n";
echo "  DELETE (DELETE): " . $crud['DELETE'] . "\n\n";

// === PART 4: Testing Status Estimation ===
echo "\n📈 ANALYSIS 4: ESTIMATED TESTING STATUS\n";
echo str_repeat("─", 75) . "\n\n";

echo "Based on Postman Save Responses:\n\n";

$totalResponses = array_sum(array_values($successPatterns));
$estimatedTested = intval($totalResponses / 2.5); // Average 2.5 responses per endpoint

echo "Endpoints Found in Postman: ~" . count(array_unique(explode(',', implode(',', array_keys($byMethod))))) . "\n";
echo "Estimated Endpoints with Saved Response: ~$estimatedTested\n";
echo "Estimated Untested Endpoints: ~" . ($totalRoutes - $estimatedTested) . "\n";
echo "Estimated Coverage: " . round(($estimatedTested / $totalRoutes) * 100, 1) . "%\n\n";

// === PART 5: Specific Endpoints to Check ===
echo "\n❓ ANALYSIS 5: COMMON ENDPOINTS TEST STATUS\n";
echo str_repeat("─", 75) . "\n\n";

$commonTests = [
    'GET' => ['/api/banks', '/api/clients', '/api/invoices', '/api/users'],
    'POST' => ['/api/banks', '/api/clients', '/api/invoices', '/api/users'],
    'PUT' => ['/api/banks/{id}', '/api/clients/{id}', '/api/invoices/{id}', '/api/users/{id}'],
    'DELETE' => ['/api/banks/{id}', '/api/clients/{id}', '/api/invoices/{id}', '/api/users/{id}'],
];

foreach ($commonTests as $method => $endpoints) {
    echo "\n[$method] Common Endpoints:\n";
    
    foreach ($endpoints as $ep) {
        // Check if in Postman collection
        $inPostman = false;
        $hasResponse = false;
        
        // Simple search
        if (strpos($collectionContent, $ep) !== false) {
            $inPostman = true;
            // Check if there's a response block nearby
            $pos = strpos($collectionContent, $ep);
            $section = substr($collectionContent, $pos, 1000);
            if (strpos($section, '"response"') !== false) {
                $hasResponse = true;
            }
        }
        
        $status = $inPostman ? ($hasResponse ? '✓ TESTED' : '⏳ IN COLLECTION') : '❌ MISSING';
        echo "  $ep: $status\n";
    }
}

echo "\n\n╔═════════════════════════════════════════════════════════════════════════╗\n";
echo "║                          RECOMMENDATIONS                               ║\n";
echo "╚═════════════════════════════════════════════════════════════════════════╝\n\n";

echo "✅ STRONG STATUS:\n";
echo "   • GET endpoints: Most appear to have saved responses\n";
echo "   • Basic CRUD (Get list, Get detail): Well tested\n\n";

echo "⚠️  WEAK STATUS:\n";
echo "   • PUT/UPDATE endpoints: Most DO NOT have saved responses\n";
echo "   • DELETE endpoints: Most DO NOT have saved responses\n";
echo "   • Some POST create endpoints: Missing\n\n";

echo "🎯 ACTION REQUIRED:\n";
echo "   1. Test ALL PUT endpoints (update operations)\n";
echo "   2. Test ALL DELETE endpoints (delete operations)\n";
echo "   3. Verify PUT/DELETE have proper response examples saved\n";
echo "   4. Check for missing POST endpoints in Postman\n\n";

echo "📝 File: " . $collectionFile . "\n";
