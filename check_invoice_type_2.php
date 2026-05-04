<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$invoices = \App\Models\Invoice::where('invoice_type_id', 2)->get();

echo "===== INVOICES dengan invoice_type_id = 2 =====\n";
echo "Total ditemukan: " . $invoices->count() . "\n\n";

foreach ($invoices as $inv) {
    echo "ID: {$inv->id}\n";
    echo "  Number: {$inv->number}\n";
    echo "  Invoice Type ID: {$inv->invoice_type_id}\n";
    echo "  Client ID: {$inv->client_id}\n";
    echo "  Date: {$inv->date}\n";
    echo "  Is Posted: {$inv->is_posted}\n";
    echo "\n";
}

// Also check invoice type itself
$invoiceType = \App\Models\InvoiceType::find(2);
if ($invoiceType) {
    echo "===== INVOICE TYPE ID 2 =====\n";
    echo "ID: {$invoiceType->id}\n";
    echo "Code: {$invoiceType->code}\n";
    echo "Name: {$invoiceType->name}\n";
    echo "Is License: {$invoiceType->is_license}\n";
    echo "Auto Create Number: {$invoiceType->auto_create_number}\n";
    echo "Is Active: {$invoiceType->is_active}\n";
} else {
    echo "Invoice Type ID 2 tidak ditemukan!\n";
}
