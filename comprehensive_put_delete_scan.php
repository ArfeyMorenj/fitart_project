<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n=== COMPREHENSIVE SCAN: ALL PUT/DELETE ENDPOINTS ===\n\n";

// 1. Stop Licenses
echo "1. STOP LICENSES DATA\n";
echo "======================\n";
$stopLicenses = \App\Models\StopLicense::with('workOrder', 'client', 'product', 'clientSpv')->limit(3)->get();
if ($stopLicenses->count() > 0) {
    echo "✓ Found " . $stopLicenses->count() . " records\n\n";
    foreach ($stopLicenses as $sl) {
        echo "ID: {$sl->id}\n";
        echo "  - number: {$sl->number}\n";
        echo "  - date: {$sl->date}\n";
        echo "  - stop_date: {$sl->stop_date}\n";
        echo "  - work_order_id: {$sl->work_order_id}\n";
        echo "  - client_id: {$sl->client_id}\n";
        echo "  - product_id: {$sl->product_id}\n";
        echo "  - client_spv_id: {$sl->client_spv_id}\n";
        echo "  - is_stopped: {$sl->is_stopped}\n";
        echo "  - notes: {$sl->notes}\n\n";
    }
} else {
    echo "✗ NO RECORDS FOUND - Will need to create one\n\n";
}

// 2. Check all tables for PUT/DELETE endpoints
echo "2. CHECK ALL TABLES FOR ID=1 (Testing Reference)\n";
echo "==================================================\n";

$tables = [
    'Recurring Invoices' => \App\Models\RecurringInvoice::find(1),
    'Receivables' => \App\Models\Receivable::find(1),
    'Stop Licenses' => \App\Models\StopLicense::find(1),
];

foreach ($tables as $name => $model) {
    if ($model) {
        echo "✓ {$name} ID 1 EXISTS\n";
    } else {
        echo "✗ {$name} ID 1 NOT FOUND\n";
    }
}
echo "\n";

// 3. Summary of all controllers
echo "3. CONTROLLER ANALYSIS SUMMARY\n";
echo "================================\n";

$controllers = [
    'DebitCreditNoteController' => 'Has: GET, POST, PUT, DELETE (index, show, store, update, destroy)',
    'ProtectionController' => 'Has: GET, POST, PUT, DELETE (index, show, store, update, destroy)',
    'StopLicenseController' => 'Has: GET, POST, PUT, DELETE (index, search, show, store, update, destroy)',
    'RecurringInvoiceController' => 'Need to check methods',
    'PostingPaymentController' => 'Has POST for batch, GET; NO PUT/DELETE for main posting',
    'PostingPaymentCostController' => 'Has PUT, DELETE for costs only',
];

foreach ($controllers as $controller => $status) {
    echo "- {$controller}: {$status}\n";
}

echo "\n=== END SCAN ===\n";
