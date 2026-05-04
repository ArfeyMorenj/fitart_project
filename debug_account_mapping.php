<?php
// Debug script untuk cek account mapping

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;
use App\Models\Item;

echo "=== DEBUG ACCOUNT MAPPING ===\n\n";

// Cek Invoice 1
$invoice = Invoice::with(['items.masterItemProduct'])->find(1);
echo "Invoice 1:\n";
echo "  ID: " . ($invoice->id ?? 'N/A') . "\n";
echo "  Number: " . ($invoice->number ?? 'N/A') . "\n";
echo "  Total: " . ($invoice->total ?? 'N/A') . "\n";
echo "  Items Count: " . ($invoice->items?->count() ?? 0) . "\n\n";

if ($invoice && $invoice->items->count() > 0) {
    $firstItem = $invoice->items->first();
    echo "First Item:\n";
    echo "  ID: " . ($firstItem->id ?? 'N/A') . "\n";
    echo "  Item Code: " . ($firstItem->item_code ?? 'N/A') . "\n";
    echo "  Master Item Product ID: " . ($firstItem->master_item_product_id ?? 'N/A') . "\n";
    
    if ($firstItem->masterItemProduct) {
        echo "\nMaster Item Product:\n";
        echo "  ID: " . $firstItem->masterItemProduct->id . "\n";
        echo "  Code: " . ($firstItem->masterItemProduct->code ?? 'N/A') . "\n";
        echo "  acc_omzet: " . ($firstItem->masterItemProduct->acc_omzet ?? 'MISSING') . "\n";
        echo "  acc_piutang: " . ($firstItem->masterItemProduct->acc_piutang ?? 'MISSING') . "\n";
    } else {
        echo "\nNo Master Item Product linked!\n";
    }
    
    // Check items table fallback
    if ($firstItem->item_code) {
        $itemFromTable = Item::where('code', $firstItem->item_code)->first();
        echo "\nItem from Items table (fallback):\n";
        if ($itemFromTable) {
            echo "  Found!\n";
            echo "  acc_omzet: " . ($itemFromTable->acc_omzet ?? 'MISSING') . "\n";
            echo "  acc_piutang: " . ($itemFromTable->acc_piutang ?? 'MISSING') . "\n";
        } else {
            echo "  Not found in items table\n";
        }
    }
} else {
    echo "Invoice has no items!\n";
}

echo "\n=== MASTER ITEM PRODUCTS WITH ACCOUNT MAPPING ===\n";
$productsWithMapping = \App\Models\MasterItemProduct::where(function($q) {
    $q->whereNotNull('acc_omzet')
      ->orWhereNotNull('acc_piutang');
})->get();

echo "Count: " . $productsWithMapping->count() . "\n";
$productsWithMapping->each(function($p) {
    echo "  - {$p->code}: omzet={$p->acc_omzet}, piutang={$p->acc_piutang}\n";
});

echo "\n=== ITEMS WITH ACCOUNT MAPPING ===\n";
$itemsWithMapping = Item::where(function($q) {
    $q->whereNotNull('acc_omzet')
      ->orWhereNotNull('acc_piutang');
})->get();

echo "Count: " . $itemsWithMapping->count() . "\n";
$itemsWithMapping->each(function($i) {
    echo "  - {$i->code}: omzet={$i->acc_omzet}, piutang={$i->acc_piutang}\n";
});
