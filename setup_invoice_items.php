<?php
// Script untuk add items ke invoice 1

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\MasterItemProduct;

echo "=== SETUP ITEMS UNTUK INVOICE 1 ===\n\n";

$invoice = Invoice::find(1);
if (!$invoice) {
    echo "Invoice 1 tidak ditemukan!\n";
    exit(1);
}

echo "Invoice: {$invoice->number}\n";
echo "Current Items Count: " . $invoice->items->count() . "\n\n";

// Cek MasterItemProduct yang tersedia
$masterProducts = MasterItemProduct::whereNotNull('acc_omzet')
    ->whereNotNull('acc_piutang')
    ->get();

echo "Available Master Item Products with account mapping:\n";
$masterProducts->each(function($p, $idx) {
    echo "  [$idx] {$p->code} - omzet={$p->acc_omzet}, piutang={$p->acc_piutang}\n";
});

// Ambil yang pertama untuk ditambahkan
$firstMasterProduct = $masterProducts->first();
if (!$firstMasterProduct) {
    echo "\nTidak ada Master Item Product dengan account mapping!\n";
    exit(1);
}

echo "\nMenambahkan item dengan Master Product: {$firstMasterProduct->code}\n";

// Tentukan nilai berdasarkan invoice total
// Asumsikan harga per unit = 100,000 (sesuai dengan invoice total 3,330,000)
$totalAmount = (float)$invoice->total; // 3,330,000
$qty = 1;
$price = $totalAmount; // Simplified: 1 item dengan harga total invoice

// Create invoice item
$invoiceItem = InvoiceItem::create([
    'invoice_id' => $invoice->id,
    'master_item_product_id' => $firstMasterProduct->id,
    'item_code' => $firstMasterProduct->code,
    'item_name' => $firstMasterProduct->name ?? $firstMasterProduct->code,
    'description' => "Item dari {$firstMasterProduct->code}",
    'qty' => $qty,
    'unit' => 'pcs',
    'price' => $price,
    'bruto' => $qty * $price,
]);

echo "\n✓ Item ditambahkan!\n";
echo "  Item ID: {$invoiceItem->id}\n";
echo "  Qty: {$invoiceItem->qty}\n";
echo "  Price: {$invoiceItem->price}\n";
echo "  Bruto: {$invoiceItem->bruto}\n";
echo "  Master Item Product: {$invoiceItem->master_item_product_id}\n";

// Verify
$invoice->refresh();
echo "\nVerification:\n";
echo "  Invoice items after insert: " . $invoice->items->count() . "\n";
$firstItem = $invoice->items->first();
if ($firstItem) {
    $mip = $firstItem->masterItemProduct;
    echo "  First item acc_omzet: " . ($mip?->acc_omzet ?? 'N/A') . "\n";
    echo "  First item acc_piutang: " . ($mip?->acc_piutang ?? 'N/A') . "\n";
}

echo "\n✓ SELESAI! Invoice 1 sudah punya items dengan account mapping.\n";
echo "  Coba posting lagi dengan endpoint /api/posting/omzet\n";
