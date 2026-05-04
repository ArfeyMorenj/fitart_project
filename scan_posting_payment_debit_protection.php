<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SCANNING DATABASE FOR PUT/DELETE ENDPOINTS ===\n\n";

// 1. Debit Credit Notes
echo "1. DEBIT CREDIT NOTES\n";
echo "=====================\n";
$dcns = \App\Models\DebitCreditNote::with('client', 'items')->limit(3)->get();
if ($dcns->count() > 0) {
    echo "✓ Found " . $dcns->count() . " records\n\n";
    foreach ($dcns as $dcn) {
        echo "ID: {$dcn->id}\n";
        echo "  - type: {$dcn->type}\n";
        echo "  - number: {$dcn->number}\n";
        echo "  - date: {$dcn->date}\n";
        echo "  - invoice_id: {$dcn->invoice_id}\n";
        echo "  - client_id: {$dcn->client_id}\n";
        $clientName = isset($dcn->client->name) ? $dcn->client->name : 'N/A';
        echo "  - client_name: {$clientName}\n";
        echo "  - description: {$dcn->description}\n";
        echo "  - is_posted: {$dcn->is_posted}\n";
        echo "  - dpp_amount: {$dcn->dpp_amount}\n";
        echo "  - ppn_amount: {$dcn->ppn_amount}\n";
        echo "  - total_amount: {$dcn->total_amount}\n";
        echo "  - Items count: " . $dcn->items()->count() . "\n";
        if ($dcn->items()->count() > 0) {
            foreach ($dcn->items as $item) {
                echo "    * {$item->item_name} - DPP: {$item->dpp_amount}, PPN: {$item->ppn_amount}\n";
            }
        }
        echo "\n";
    }
} else {
    echo "✗ NO RECORDS FOUND\n\n";
}

// 2. Protection Periods
echo "2. PROTECTION PERIODS\n";
echo "======================\n";
$protections = \App\Models\ProtectionPeriod::all();
if ($protections->count() > 0) {
    echo "✓ Found " . $protections->count() . " records\n\n";
    foreach ($protections as $prot) {
        echo "ID: {$prot->id}\n";
        echo "  - period: {$prot->period}\n";
        echo "  - is_protected: {$prot->is_protected}\n";
        echo "  - protected_at: {$prot->protected_at}\n";
        echo "  - protected_by user_id: {$prot->protected_by}\n";
        echo "\n";
    }
} else {
    echo "✗ NO RECORDS FOUND - Will need to create one\n\n";
}

// 3. Posting Payments
echo "3. POSTING PAYMENTS\n";
echo "====================\n";
$postings = \App\Models\PostingPayment::with('bank', 'client', 'invoices')->limit(3)->get();
if ($postings->count() > 0) {
    echo "✓ Found " . $postings->count() . " records\n\n";
    foreach ($postings as $posting) {
        echo "ID: {$posting->id}\n";
        echo "  - number: {$posting->number}\n";
        echo "  - date: {$posting->date}\n";
        echo "  - bank_id: {$posting->bank_id}\n";
        $bankName = isset($posting->bank->name) ? $posting->bank->name : 'N/A';
        echo "  - bank_name: {$bankName}\n";
        echo "  - client_id: {$posting->client_id}\n";
        $clientName2 = isset($posting->client->name) ? $posting->client->name : 'N/A';
        echo "  - client_name: {$clientName2}\n";
        echo "  - description: {$posting->description}\n";
        echo "  - is_posted: {$posting->is_posted}\n";
        echo "  - total_payment: {$posting->total_payment}\n";
        echo "  - Invoices count: " . (isset($posting->invoices) ? $posting->invoices()->count() : 0) . "\n";
        echo "\n";
    }
} else {
    echo "✗ NO RECORDS FOUND\n\n";
}

// 4. Check Posting Payment Costs
echo "4. POSTING PAYMENT COSTS\n";
echo "========================\n";
$costs = \App\Models\PostingPaymentCost::first();
if ($costs) {
    echo "✓ Sample record exists\n";
    echo "ID: {$costs->id}\n";
    echo "  - posting_payment_id: {$costs->posting_payment_id}\n";
    echo "  - item_id: {$costs->item_id}\n";
    echo "  - qty: {$costs->qty}\n";
    echo "  - price: {$costs->price}\n";
} else {
    echo "✗ NO RECORDS FOUND\n";
}
echo "\n";

// Check Users for protected_by
echo "5. USERS (for protected_by)\n";
echo "============================\n";
$users = \App\Models\User::limit(5)->get(['id', 'username', 'role']);
foreach ($users as $user) {
    echo "ID: {$user->id}, Username: {$user->username}, Role: {$user->role}\n";
}
