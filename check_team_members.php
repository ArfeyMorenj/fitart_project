<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VALID TEAM_MEMBERS CODES FROM DATABASE ===\n\n";

// Get all team members
$teamMembers = \App\Models\TeamMember::all(['id', 'code', 'name', 'position', 'status']);

if ($teamMembers->count() > 0) {
    foreach ($teamMembers as $tm) {
        echo "{$tm->id} | {$tm->code} | {$tm->name} | Position: {$tm->position}\n";
    }
    echo "\nTOTAL: " . $teamMembers->count() . " team members\n";
} else {
    echo "NO TEAM MEMBERS FOUND\n";
}

// Check product id 2
echo "\n=== CHECK PRODUCT ID 2 ===\n";
$product = \App\Models\Product::find(2);
if ($product) {
    echo "Found Product: {$product->code} - {$product->name}\n";
    $author_code = isset($product->author_code) ? $product->author_code : 'NULL';
    echo "Current author_code: {$author_code}\n";
    echo "Current product_group_id: {$product->product_group_id}\n";
} else {
    echo "Product ID 2 not found - will need to create or use different id\n";
}

// Check other models with id=2 for reference
echo "\n=== CHECK OTHER TABLES WITH ID 2 ===\n";

$models = [
    'Bank' => \App\Models\Bank::find(2),
    'Client' => \App\Models\Client::find(2),
    'Company' => \App\Models\Company::find(2),
    'InvoiceType' => \App\Models\InvoiceType::find(2),
    'Item' => \App\Models\Item::find(2),
    'MasterItemProduct' => \App\Models\MasterItemProduct::find(2),
    'ProductGroup' => \App\Models\ProductGroup::find(2),
    'User' => \App\Models\User::find(2),
    'TeamMember' => \App\Models\TeamMember::find(2),
    'Installation' => \App\Models\Installation::find(2),
    'License' => \App\Models\License::find(2),
    'WorkOrder' => \App\Models\WorkOrder::find(2),
    'Payment' => \App\Models\Payment::find(2),
    'InvoiceItem' => \App\Models\InvoiceItem::find(2),
];

foreach ($models as $modelName => $model) {
    if ($model) {
        $displayValue = isset($model->code) ? $model->code : (isset($model->name) ? $model->name : (isset($model->username) ? $model->username : 'N/A'));
        echo "✓ {$modelName} ID 2 EXISTS - {$displayValue}\n";
    } else {
        echo "✗ {$modelName} ID 2 NOT FOUND\n";
    }
}
