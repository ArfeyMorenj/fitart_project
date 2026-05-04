<?php
// Quick invoice checker script
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;

echo "=== INVOICE DATABASE CHECK ===\n\n";

$count = Invoice::count();
echo "Total invoices (including soft-deleted): " . Invoice::withTrashed()->count() . "\n";
echo "Active invoices (excluding soft-deleted): " . $count . "\n\n";

if ($count > 0) {
    echo "Sample invoices:\n";
    echo str_repeat("-", 80) . "\n";
    echo sprintf("%-20s %-20s %-15s %s\n", "NUMBER", "CLIENT", "DATE", "TOTAL");
    echo str_repeat("-", 80) . "\n";
    
    Invoice::limit(10)->get()->each(function($invoice) {
        echo sprintf("%-20s %-20s %-15s %.2f\n", 
            $invoice->number ?? 'N/A',
            $invoice->client->name ?? 'N/A',
            $invoice->date ?? 'N/A',
            $invoice->total ?? 0
        );
    });
    echo str_repeat("-", 80) . "\n";
} else {
    echo "❌ NO INVOICES FOUND IN DATABASE\n";
    echo "   Database mungkin kosong atau belum di-seed dengan data.\n";
}

echo "\n✅ Check complete.\n";
?>
