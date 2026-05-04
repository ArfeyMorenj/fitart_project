<?php
$file = 'FitArt_JPAS_API_Complete.postman_collection.json';
$data = file_get_contents($file);
$json = json_decode($data, true);
if ($json === null) {
    echo "JSON Error: " . json_last_error_msg() . "\n";
    echo "File size: " . strlen($data) . " bytes\n";
    // Try to find the error location
    $lines = explode("\n", $data);
    echo "Total lines: " . count($lines) . "\n";
} else {
    echo "JSON loaded successfully\n";
    echo "Has 'item' key: " . (isset($json['item']) ? 'YES' : 'NO') . "\n";
    if (isset($json['item'])) {
        echo "Item count: " . count($json['item']) . "\n";
    }
}
?>
