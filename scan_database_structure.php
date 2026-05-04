<?php
require __DIR__ . '/vendor/autoload.php';

// Set up Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Get models
$models = [
    'App\Models\User',
    'App\Models\TeamMember',
    'App\Models\ProductGroup',
    'App\Models\Product',
    'App\Models\ChartOfAccount',
    'App\Models\Invoice',
    'App\Models\InvoiceItem',
    'App\Models\JournalEntry',
];

$data = [];

foreach ($models as $modelClass) {
    if (!class_exists($modelClass)) {
        continue;
    }
    
    $model = new $modelClass();
    $tableName = $model->getTable();
    
    // Get columns
    $columns = \DB::getSchemaBuilder()->getColumnListing($tableName);
    
    // Get sample data
    $sample = $modelClass::take(3)->get();
    
    $data[$modelClass] = [
        'table' => $tableName,
        'columns' => $columns,
        'sample_count' => $modelClass::count(),
        'samples' => $sample->map(fn($item) => $item->toArray())->toArray()
    ];
}

echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
