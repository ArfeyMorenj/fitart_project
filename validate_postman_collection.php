<?php
// Parse Postman collection and analyze structure
$jsonFile = 'FitArt_JPAS_API_Complete.postman_collection.json';
$json = file_get_contents($jsonFile);
$collection = json_decode($json, true);

echo "=== VALIDASI POSTMAN COLLECTION STRUCTURE ===\n\n";

// Build tree from collection
$tree = [];
$duplicates = [];
$allEndpoints = [];

function extractEndpoints(&$item, $path = '', &$endpoints = [], &$duplicates = []) {
    if (isset($item['name'])) {
        $currentPath = trim($path . ' > ' . $item['name'], ' > ');
        
        if (isset($item['request']) && isset($item['request']['method'])) {
            // This is a request
            $method = $item['request']['method'];
            $url = '';
            
            if (isset($item['request']['url'])) {
                if (is_string($item['request']['url'])) {
                    $url = $item['request']['url'];
                } elseif (is_array($item['request']['url'])) {
                    if (isset($item['request']['url']['path'])) {
                        $url = '/' . implode('/', $item['request']['url']['path']);
                    }
                }
            }
            
            $key = "$method $url";
            $endpoints[$currentPath] = [
                'method' => $method,
                'url' => $url,
                'name' => $item['name'],
                'fullPath' => $currentPath
            ];
            
            // Check for duplicates
            if (isset($allEndpoints[$key])) {
                if (!isset($duplicates[$key])) {
                    $duplicates[$key] = [$allEndpoints[$key]['fullPath']];
                }
                $duplicates[$key][] = $currentPath;
            }
            $allEndpoints[$key] = $endpoints[$currentPath];
        } else if (isset($item['item']) && is_array($item['item'])) {
            // This is a folder
            foreach ($item['item'] as $subitem) {
                extractEndpoints($subitem, $currentPath, $endpoints, $duplicates);
            }
        }
    }
}

$collectionEndpoints = [];
foreach ($collection['item'] as $item) {
    extractEndpoints($item, '', $collectionEndpoints, $duplicates);
}

// Organize by main section
$bySection = [];
foreach ($collectionEndpoints as $endpoint) {
    $parts = explode(' > ', $endpoint['fullPath']);
    $section = $parts[0] ?? 'Unknown';
    
    if (!isset($bySection[$section])) {
        $bySection[$section] = [];
    }
    $bySection[$section][] = $endpoint;
}

// Display structure
echo "📊 CURRENT COLLECTION STRUCTURE:\n";
echo "================================\n\n";

ksort($bySection);
foreach ($bySection as $section => $endpoints) {
    echo "✅ $section (" . count($endpoints) . " items)\n";
}

echo "\n\n=== DETAILED BREAKDOWN ===\n";
echo "================================\n\n";

foreach ($bySection as $section => $endpoints) {
    echo "\n📂 $section\n";
    
    $bySubsection = [];
    foreach ($endpoints as $ep) {
        $parts = explode(' > ', $ep['fullPath']);
        $subsection = $parts[1] ?? 'Root';
        
        if (!isset($bySubsection[$subsection])) {
            $bySubsection[$subsection] = [];
        }
        $bySubsection[$subsection][] = $ep;
    }
    
    foreach ($bySubsection as $subsection => $subs) {
        echo "   📁 $subsection (" . count($subs) . ")\n";
        foreach ($subs as $ep) {
            echo "      [{$ep['method']}] {$ep['url']}\n";
        }
    }
}

// Check for duplicates
echo "\n\n=== DUPLICATE CHECK ===\n";
echo "============================\n";

if (count($duplicates) > 0) {
    echo "⚠️  DUPLICATES FOUND:\n\n";
    foreach ($duplicates as $endpoint => $locations) {
        echo "❌ $endpoint\n";
        foreach ($locations as $loc) {
            echo "   - Found in: $loc\n";
        }
        echo "\n";
    }
} else {
    echo "✅ NO DUPLICATES FOUND!\n";
}

// Validate against expected structure
echo "\n\n=== VALIDATION AGAINST EXPECTED STRUCTURE ===\n";
echo "=============================================\n\n";

$expectedStructure = [
    '01 Auth' => ['POST /api/login', 'POST /api/logout', 'GET /api/me'],
    '06 Finance' => [
        'debit-credit-notes' => ['GET /api/debit-credit-notes', 'GET /api/debit-credit-notes/{id}', 'GET /api/debit-credit-notes/search', 'POST /api/debit-credit-notes', 'PUT /api/debit-credit-notes/{id}', 'DELETE /api/debit-credit-notes/{id}'],
        'invoice-items' => ['GET /api/invoice-items', 'GET /api/invoice-items/{id}', 'POST /api/invoice-items', 'PUT /api/invoice-items/{id}', 'DELETE /api/invoice-items/{id}'],
        'invoice-license' => ['GET /api/invoice-license', 'GET /api/invoice-license/{id}'],
        'invoice-product' => ['GET /api/invoice-products', 'GET /api/invoice-products/{id}'],
        'invoices' => ['GET /api/invoices', 'GET /api/invoices/{id}', 'POST /api/invoices', 'PUT /api/invoices/{id}', 'DELETE /api/invoices/{id}'],
    ],
    '07 Posting' => [
        'posting-payments' => ['GET /api/posting/payments', 'GET /api/posting/payments/search', 'GET /api/posting/payments/{id}', 'POST /api/posting/payments'],
    ]
];

$issues = [];

// Check Finance section
if (isset($bySection['06 Finance'])) {
    echo "06 Finance Analysis:\n";
    
    // Check for invoice-license folder
    $hasInvoiceLicense = false;
    $hasInvoiceLicenseGetList = false;
    $hasInvoiceLicenseGetDetail = false;
    
    // Check for debit-credit-notes/search
    $hasDebitCreditSearch = false;
    
    // Check for posting-payments GET list/search/detail
    $hasPostingPaymentsGetList = false;
    $hasPostingPaymentsGetSearch = false;
    $hasPostingPaymentsGetDetail = false;
    
    foreach ($bySection['06 Finance'] as $ep) {
        if (strpos($ep['fullPath'], 'invoice-license') !== false) {
            $hasInvoiceLicense = true;
            if ($ep['url'] === '/api/invoice-license' && $ep['method'] === 'GET') {
                $hasInvoiceLicenseGetList = true;
                echo "   ✅ Found: {$ep['method']} {$ep['url']}\n";
            } elseif (strpos($ep['url'], '/api/invoice-license/{') !== false && $ep['method'] === 'GET') {
                $hasInvoiceLicenseGetDetail = true;
                echo "   ✅ Found: {$ep['method']} {$ep['url']}\n";
            }
        }
        
        if (strpos($ep['fullPath'], 'debit-credit-notes') !== false && $ep['url'] === '/api/debit-credit-notes/search' && $ep['method'] === 'GET') {
            $hasDebitCreditSearch = true;
            echo "   ✅ Found: {$ep['method']} {$ep['url']}\n";
        }
    }
    
    if (!$hasInvoiceLicense) {
        echo "   ❌ MISSING: invoice-license folder\n";
        $issues[] = "Missing invoice-license folder in 06 Finance";
    } elseif (!$hasInvoiceLicenseGetList) {
        echo "   ❌ MISSING: GET /api/invoice-license (list)\n";
        $issues[] = "Missing GET /api/invoice-license";
    } elseif (!$hasInvoiceLicenseGetDetail) {
        echo "   ❌ MISSING: GET /api/invoice-license/{id} (detail)\n";
        $issues[] = "Missing GET /api/invoice-license/{id}";
    }
    
    if (!$hasDebitCreditSearch) {
        echo "   ❌ MISSING: GET /api/debit-credit-notes/search\n";
        $issues[] = "Missing GET /api/debit-credit-notes/search";
    } else {
        echo "   ✅ Found: GET /api/debit-credit-notes/search\n";
    }
    
    echo "\n";
}

// Check Posting section
if (isset($bySection['07 Posting'])) {
    echo "07 Posting Analysis:\n";
    
    $hasPostingPaymentsGetList = false;
    $hasPostingPaymentsGetSearch = false;
    $hasPostingPaymentsGetDetail = false;
    
    foreach ($bySection['07 Posting'] as $ep) {
        if (strpos($ep['fullPath'], 'posting-payments') !== false) {
            if ($ep['url'] === '/api/posting/payments' && $ep['method'] === 'GET' && strpos($ep['url'], '{') === false) {
                $hasPostingPaymentsGetList = true;
                echo "   ✅ Found: {$ep['method']} {$ep['url']}\n";
            } elseif ($ep['url'] === '/api/posting/payments/search' && $ep['method'] === 'GET') {
                $hasPostingPaymentsGetSearch = true;
                echo "   ✅ Found: {$ep['method']} {$ep['url']}\n";
            } elseif (strpos($ep['url'], '/api/posting/payments/{') !== false && strpos($ep['url'], '/costs') === false && $ep['method'] === 'GET') {
                $hasPostingPaymentsGetDetail = true;
                echo "   ✅ Found: {$ep['method']} {$ep['url']}\n";
            }
        }
    }
    
    if (!$hasPostingPaymentsGetList) {
        echo "   ❌ MISSING: GET /api/posting/payments (list)\n";
        $issues[] = "Missing GET /api/posting/payments";
    }
    if (!$hasPostingPaymentsGetSearch) {
        echo "   ❌ MISSING: GET /api/posting/payments/search\n";
        $issues[] = "Missing GET /api/posting/payments/search";
    }
    if (!$hasPostingPaymentsGetDetail) {
        echo "   ❌ MISSING: GET /api/posting/payments/{id} (detail)\n";
        $issues[] = "Missing GET /api/posting/payments/{id}";
    }
    
    echo "\n";
}

// Summary
echo "\n=== VALIDATION SUMMARY ===\n";
echo "===========================\n\n";

echo "Total Endpoints: " . count($allEndpoints) . "\n";
echo "Total Sections: " . count($bySection) . "\n";
echo "Duplicate Endpoints: " . count($duplicates) . "\n";
echo "Organization Issues: " . count($issues) . "\n\n";

if (count($issues) === 0 && count($duplicates) === 0) {
    echo "✅ COLLECTION IS PERFECT! No issues found.\n";
} else {
    echo "⚠️  ISSUES FOUND:\n\n";
    foreach ($issues as $i => $issue) {
        echo ($i + 1) . ". ❌ " . $issue . "\n";
    }
}

echo "\n=== ORGANIZATION STATUS ===\n";
echo "=============================\n\n";

// Check if folders are organized properly
$financeIssues = [];
if (isset($bySection['06 Finance'])) {
    $financeEndpoints = $bySection['06 Finance'];
    
    // Count endpoints per subsection
    $subsectionCounts = [];
    foreach ($financeEndpoints as $ep) {
        $parts = explode(' > ', $ep['fullPath']);
        $subsection = $parts[1] ?? 'Root';
        $subsectionCounts[$subsection] = ($subsectionCounts[$subsection] ?? 0) + 1;
    }
    
    echo "06 Finance Subsections:\n";
    foreach ($subsectionCounts as $subsection => $count) {
        echo "   📁 $subsection: $count endpoints\n";
    }
    
    // Check if invoice-license and invoice-product are separate
    if (isset($subsectionCounts['invoice-license']) && isset($subsectionCounts['invoices'])) {
        echo "   ✅ invoice-license folder is SEPARATE from invoices\n";
    } elseif (!isset($subsectionCounts['invoice-license'])) {
        echo "   ❌ invoice-license folder MISSING or MIXED with invoices\n";
    }
    
    if (isset($subsectionCounts['invoice-product'])) {
        echo "   ✅ invoice-product folder exists\n";
    } else {
        echo "   ⚠️  invoice-product folder might be named differently or missing\n";
    }
}

echo "\n";

// Recommend cleanup
if (count($issues) > 0 || count($duplicates) > 0) {
    echo "🔧 RECOMMENDED ACTIONS:\n";
    echo "=======================\n\n";
    
    $actions = 1;
    
    if (!isset($bySection['06 Finance']) || count(preg_grep('/invoice-license/i', array_keys($bySection['06 Finance']))) === 0) {
        echo "$actions. CREATE folder '06 Finance > invoice-license' and add:\n";
        echo "   - GET /api/invoice-license\n";
        echo "   - GET /api/invoice-license/{id}\n\n";
        $actions++;
    }
    
    if (isset($bySection['06 Finance'])) {
        $search = false;
        foreach ($bySection['06 Finance'] as $ep) {
            if ($ep['url'] === '/api/debit-credit-notes/search') {
                $search = true;
                break;
            }
        }
        if (!$search) {
            echo "$actions. ADD to '06 Finance > debit-credit-notes':\n";
            echo "   - GET /api/debit-credit-notes/search\n\n";
            $actions++;
        }
    }
    
    if (!isset($bySection['07 Posting']) || count(preg_grep('/\/api\/posting\/payments\s*$/', array_keys($bySection['07 Posting']))) === 0) {
        echo "$actions. ADD to '07 Posting > posting-payments':\n";
        echo "   - GET /api/posting/payments\n";
        echo "   - GET /api/posting/payments/search\n";
        echo "   - GET /api/posting/payments/{id}\n\n";
        $actions++;
    }
}
