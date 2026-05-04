<?php
$jsonFile = 'FitArt_JPAS_API_Complete.postman_collection.json';
$json = file_get_contents($jsonFile);

if ($json === false) {
    echo "File read failed\n";
} else {
    echo "File size: " . strlen($json) . " bytes\n";
    echo "First 100 chars: " . substr($json, 0, 100) . "\n";
    
    // Try to decode
    $data = json_decode($json, true);
    
    if ($data === null) {
        echo "JSON error: " . json_last_error_msg() . "\n";
    } else {
        echo "JSON valid\n";
        if (isset($data['info']['name'])) {
            echo "Collection name: " . $data['info']['name'] . "\n";
        }
        if (isset($data['item'])) {
            echo "Total sections: " . count($data['item']) . "\n";
        }
    }
}
?>
