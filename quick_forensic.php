<?php
/**
 * Advanced Postman/Laravel Forensic Analyzer
 * Handles large JSON files with streaming
 */

// First, let's analyze the Laravel routes without JSON parsing
$routesFile = 'routes/api.php';
$routesContent = file_get_contents($routesFile);

echo "═══════════════════════════════════════════════════════════════════\n";
echo "       ENTERPRISE API FORENSIC ANALYSIS - PHASE 1: BACKEND          \n";
echo "═══════════════════════════════════════════════════════════════════\n\n";

// Extract all routes from Laravel
$laravel_endpoints = [];

// Pattern 1: Simple route definitions
preg_match_all("/Route::(get|post|put|delete|patch)\s*\(\s*['\"]([^'\"]+)['\"]/i", $routesContent, $matches, PREG_SET_ORDER);
foreach ($matches as $match) {
    $method = strtoupper($match[1]);
    $path = '/' . ltrim($match[2], '/');
    $key = $method . ' ' . $path;
    if (!isset($laravel_endpoints[$key])) {
        $laravel_endpoints[$key] = [
            'method' => $method,
            'path' => $path,
            'type' => 'route'
        ];
    }
}

// Pattern 2: apiResource
preg_match_all("/Route::apiResource\s*\(\s*['\"]([^'\"]+)['\"]/i", $routesContent, $matches, PREG_SET_ORDER);
$resources = [];
foreach ($matches as $match) {
    $resources[] = $match[1];
}

// For each resource, extract constraints
foreach ($resources as $resource) {
    // Find the full apiResource definition
    $pattern = "/Route::apiResource\s*\(\s*['\"]" . preg_quote($resource) . "['\"]\\s*.*?(?=Route::|;$)/s";
    if (preg_match($pattern, $routesContent, $def)) {
        $definition = $def[0];
        
        // Check for only() constraint
        $only = [];
        if (preg_match("/->only\s*\(\s*\[([^\]]+)\]/", $definition, $onlyMatch)) {
            preg_match_all("/['\"](\w+)['\"]/", $onlyMatch[1], $onlyNames);
            $only = $onlyNames[1];
        } else {
            $only = ['index', 'store', 'show', 'update', 'destroy'];
        }
        
        $basePath = '/' . $resource;
        
        if (in_array('index', $only) || empty($only)) {
            $laravel_endpoints['GET ' . $basePath] = ['method' => 'GET', 'path' => $basePath, 'type' => 'apiResource:'.$resource];
        }
        if (in_array('store', $only) || empty($only)) {
            $laravel_endpoints['POST ' . $basePath] = ['method' => 'POST', 'path' => $basePath, 'type' => 'apiResource:'.$resource];
        }
        if (in_array('show', $only) || empty($only)) {
            $laravel_endpoints['GET ' . $basePath . '/{id}'] = ['method' => 'GET', 'path' => $basePath . '/{id}', 'type' => 'apiResource:'.$resource];
        }
        if (in_array('update', $only) || empty($only)) {
            $laravel_endpoints['PUT ' . $basePath . '/{id}'] = ['method' => 'PUT', 'path' => $basePath . '/{id}', 'type' => 'apiResource:'.$resource];
        }
        if (in_array('destroy', $only) || empty($only)) {
            $laravel_endpoints['DELETE ' . $basePath . '/{id}'] = ['method' => 'DELETE', 'path' => $basePath . '/{id}', 'type' => 'apiResource:'.$resource];
        }
    }
}

echo colored("LARAVEL BACKEND ANALYSIS\n", 'green');
echo str_repeat("─", 70) . "\n\n";
printf("Total Backend Endpoints: %d\n\n", count($laravel_endpoints));

// Group by module based on path
$bytype = [];
foreach ($laravel_endpoints as $key => $ep) {
    $type = $ep['type'];
    if (!isset($bytype[$type])) {
        $bytype[$type] = 0;
    }
    $bytype[$type]++;
}

echo "Breakdown by type:\n";
foreach ($bytype as $type => $count) {
    printf("  • %s: %d\n", $type, $count);
}

echo "\n\nALL BACKEND ENDPOINTS:\n";
echo str_repeat("─", 70) . "\n";
$count = 0;
foreach ($laravel_endpoints as $key => $ep) {
    printf("  %-40s [%s]\n", $ep['method'] . ' ' . $ep['path'], $ep['type']);
    $count++;
    if ($count >= 50) {
        echo "  ... and " . (count($laravel_endpoints) - 50) . " more endpoints\n";
        break;
    }
}

echo "\n\n";

// Now analyze Postman using regex extraction
echo colored("POSTMAN COLLECTION ANALYSIS\n", 'green');
echo str_repeat("─", 70) . "\n\n";

// Read Postman file with better handling
$postmanFile = 'FitArt_JPAS_API_Complete.postman_collection.json';
$postmanRaw = file_get_contents($postmanFile);

// Extract endpoints using regex from raw JSON
$postman_endpoints = [];

// Pattern: "name": "METHOD api/path", "request": {...}, "response": [...]
// More precisely: look for request method and URL
preg_match_all('"name"\s*:\s*"([^"]+)",\s*(?:"event"[^}]*},\s*)*"request"\s*:\s*\{[^}]*"method"\s*:\s*"(\w+)"[^}]*"url"\s*:\s*(?:"([^"]+)"|{\s*".*?"raw"\s*:\s*"([^"]+)")', $postmanRaw, $matches, PREG_SET_ORDER);

echo "Extracted endpoints from raw JSON:\n";
$postman_simple = [];
foreach ($matches as $match) {
    $name = trim($match[1]);
    $method = strtoupper($match[2]);
    $path = isset($match[3]) && !empty($match[3]) ? $match[3] : (isset($match[4]) ? $match[4] : '');
    $path = str_replace('{{base_url}}', '', $path);
    
    if (!empty($path)) {
        $key = $method . ' ' . $path;
        if (!isset($postman_simple[$key])) {
            $postman_simple[$key] = [
                'method' => $method,
                'path' => $path,
                'name' => $name
            ];
        }
    }
}

printf("Total Postman Endpoints Found: %d\n\n", count($postman_simple));

// Count endpoints with responses
$with_response = 0;
preg_match_all('"name"\s*:\s*"[^"]+",\s*(?:"originalRequest"[^}]*},\s*)*"status"\s*:\s*"([^"]+)",\s*"code"\s*:\s*(\d+)', $postmanRaw, $responses);
echo "Found " . count($responses[0]) . " response examples in collection\n\n";

// Save all extracted endpoints
file_put_contents('extracted_laravel_endpoints.json', json_encode($laravel_endpoints, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
file_put_contents('extracted_postman_endpoints.json', json_encode($postman_simple, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "\n✅ Endpoint data exported to JSON files\n";
echo "   - extracted_laravel_endpoints.json\n";
echo "   - extracted_postman_endpoints.json\n";

function colored($text, $color) {
    $colors = ['green' => "\033[32m", 'red' => "\033[31m", 'yellow' => "\033[33m", 'reset' => "\033[0m"];
    return ($colors[$color] ?? '') . $text . $colors['reset'];
}
?>
