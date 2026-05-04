<?php
$jsonFile = 'FitArt_JPAS_API_Complete.postman_collection.json';
$json = file_get_contents($jsonFile);
$collection = json_decode($json, true);

if (!$collection) {
    echo "JSON PARSE ERROR!\n";
    echo json_last_error_msg() . "\n";
    exit;
}

echo "✅ JSON Valid\n\n";
echo "Collection: " . $collection['info']['name'] . "\n";
echo "Total Sections: " . count($collection['item']) . "\n\n";

echo "Sections:\n";
foreach ($collection['item'] as $i => $section) {
    $itemCount = isset($section['item']) ? count($section['item']) : 0;
    echo ($i + 1) . ". {$section['name']} ($itemCount items)\n";
}
