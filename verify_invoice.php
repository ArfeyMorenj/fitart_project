<?php
// Script untuk verify invoice balance dan test posting

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;
use App\Models\CompanySetting;

echo "=== VERIFY INVOICE BALANCE ===\n\n";

$invoice = Invoice::find(1);
echo "Invoice: {$invoice->number}\n";
echo "  Total: " . number_format($invoice->total, 2) . "\n";
echo "  DPP: " . number_format($invoice->dpp, 2) . "\n";
echo "  Other: " . number_format($invoice->other, 2) . "\n";
echo "  PPN: " . number_format($invoice->ppn, 2) . "\n";
echo "  Bruto: " . number_format($invoice->bruto, 2) . "\n";
echo "  Discount: " . number_format($invoice->discount, 2) . "\n";

$debitAR = (float)$invoice->total;
$creditRevenue = (float)$invoice->dpp + (float)$invoice->other;
$creditVat = (float)$invoice->ppn;

echo "\nBalance Check:\n";
echo "  Debit AR: " . number_format($debitAR, 2) . "\n";
echo "  Credit Revenue: " . number_format($creditRevenue, 2) . "\n";
echo "  Credit VAT: " . number_format($creditVat, 2) . "\n";
echo "  Total Credit: " . number_format($creditRevenue + $creditVat, 2) . "\n";

$isBalanced = round($debitAR, 2) === round($creditRevenue + $creditVat, 2);
echo "\n  Status: " . ($isBalanced ? "✓ BALANCED" : "✗ NOT BALANCED") . "\n";

if (!$isBalanced) {
    echo "  Difference: " . number_format($debitAR - ($creditRevenue + $creditVat), 2) . "\n";
    echo "\n  ⚠️  Invoice tidak balanced! Harus diperbaiki di database terlebih dahulu.\n";
    exit(1);
}

echo "\n=== INVOICE POSTING STATUS ===\n";
echo "  is_posted: " . ($invoice->is_posted ? "true" : "false") . "\n";
echo "  posted_date: " . ($invoice->posted_date ?? "null") . "\n";

echo "\n=== COMPANY SETTINGS ===\n";
$setting = CompanySetting::first();
echo "  acc_ppn_kes: " . ($setting?->acc_ppn_kes ?? "MISSING") . "\n";

echo "\n✓ Invoice siap untuk posting!\n";
echo "\nUntuk test posting, gunakan curl command:\n";
echo "curl -X POST http://localhost:8000/api/posting/omzet \\\n";
echo "  -H \"Authorization: Bearer TOKEN\" \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -d '{\"invoice_id\": 1}'\n";
