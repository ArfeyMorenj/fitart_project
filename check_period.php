<?php
// Script untuk cek period di database

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;

echo "=== CEK PERIOD DI DATABASE ===\n\n";

// Cek all invoices
$invoices = Invoice::select('id', 'number', 'period', 'is_posted', 'date', 'total')->get();

echo "Semua Invoices:\n";
echo str_pad("ID", 5) . " | " . str_pad("Number", 20) . " | " . str_pad("Period", 10) . " | " . str_pad("Posted", 7) . " | " . str_pad("Date", 12) . " | Total\n";
echo str_repeat("-", 100) . "\n";

$invoices->each(function($inv) {
    echo str_pad((string)$inv->id, 5) . " | " . 
         str_pad($inv->number, 20) . " | " . 
         str_pad($inv->period ?? 'NULL', 10) . " | " . 
         str_pad($inv->is_posted ? 'YES' : 'NO', 7) . " | " . 
         str_pad($inv->date->format('Y-m-d'), 12) . " | " . 
         number_format($inv->total, 0) . "\n";
});

echo "\n=== SUMMARY ===\n";
echo "Total Invoices: " . $invoices->count() . "\n";
echo "Posted Invoices: " . $invoices->where('is_posted', true)->count() . "\n";
echo "Invoices by Period:\n";
$invoices->groupBy('period')->each(function($group, $period) {
    echo "  $period: " . $group->count() . " (posted: " . $group->where('is_posted', true)->count() . ")\n";
});

echo "\n=== CORRECTED COMMAND ===\n";
$postedInvoices = $invoices->where('is_posted', true)->first();
if ($postedInvoices) {
    echo "Gunakan period: {$postedInvoices->period}\n";
    echo "curl -X POST http://localhost:8000/api/receivables/process \\\n";
    echo "  -H \"Authorization: Bearer TOKEN\" \\\n";
    echo "  -H \"Content-Type: application/json\" \\\n";
    echo "  -d '{\"period\": \"{$postedInvoices->period}\"}'\n";
} else {
    $period = $invoices->first()->period;
    echo "Tidak ada invoice yang sudah posted!\n";
    echo "Coba period yang ada di database: $period\n";
}
