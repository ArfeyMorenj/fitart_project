<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== GATHERING DATA FOR ALL PUT ENDPOINTS WITH ID 2 (OR FIRST EXISTING) ===\n\n";

// Get first existing IDs for each table
$endpoints = [
    'Bank' => \App\Models\Bank::first(),
    'Client' => \App\Models\Client::first(),
    'Company' => \App\Models\Company::first(),
    'InvoiceType' => \App\Models\InvoiceType::first(),
    'Item' => \App\Models\Item::first(),
    'MasterItemProduct' => \App\Models\MasterItemProduct::first(),
    'ProductGroup' => \App\Models\ProductGroup::find(2),
    'Product' => \App\Models\Product::find(2),
    'User' => \App\Models\User::find(2),
    'TeamMember' => \App\Models\TeamMember::find(2),
    'Installation' => \App\Models\Installation::first(),
    'License' => \App\Models\License::first(),
    'WorkOrder' => \App\Models\WorkOrder::first(),
    'Payment' => \App\Models\Payment::find(2),
    'InvoiceItem' => \App\Models\InvoiceItem::first(),
];

foreach ($endpoints as $name => $model) {
    if ($model) {
        echo "✓ {$name} - ID {$model->id}\n";
        // Show all attributes for reference
        foreach ($model->toArray() as $key => $value) {
            if (strlen($value) < 50) {
                echo "    {$key}: {$value}\n";
            }
        }
        echo "\n";
    } else {
        echo "✗ {$name} - NO DATA FOUND\n\n";
    }
}

// Show what team member codes are valid
echo "\n=== VALID TEAM_MEMBER CODES ===\n";
$teamMembers = \App\Models\TeamMember::all();
foreach ($teamMembers as $tm) {
    echo "- {$tm->code} ({$tm->name})\n";
}
