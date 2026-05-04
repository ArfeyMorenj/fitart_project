<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "===== DAFTAR SEMUA ACCOUNT CODES DI DATABASE =====\n\n";

$accounts = \App\Models\ChartOfAccount::select('code', 'name', 'type', 'is_active')->orderBy('code')->get();
echo "Total: " . $accounts->count() . " accounts\n\n";

echo "Format: CODE | NAME | TYPE | ACTIVE\n";
echo str_repeat("-", 100) . "\n";

foreach ($accounts as $acc) {
    $active = $acc->is_active ? "✓" : "✗";
    echo "{$acc->code} | {$acc->name} | {$acc->type} | {$active}\n";
}

echo "\n===== ACCOUNT CODES YANG COCOK UNTUK acc_omzet / cdf_omzet (4xxx) =====\n";
$omzetAccounts = \App\Models\ChartOfAccount::where('code', 'like', '4%')->select('code', 'name')->get();
foreach ($omzetAccounts as $acc) {
    echo "{$acc->code} - {$acc->name}\n";
}

echo "\n===== ACCOUNT CODES YANG COCOK UNTUK acc_piutang / cdf_piutang (1xxx) =====\n";
$piutangAccounts = \App\Models\ChartOfAccount::where('code', 'like', '1%')->select('code', 'name')->get();
foreach ($piutangAccounts as $acc) {
    echo "{$acc->code} - {$acc->name}\n";
}
