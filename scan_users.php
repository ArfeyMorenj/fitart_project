<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SCANNING USERS TABLE ===\n\n";

$users = \App\Models\User::all();
echo "Total Users: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "ID: {$user->id}\n";
    echo "  - username: {$user->username}\n";
    echo "  - name: {$user->name}\n";
    echo "  - email: {$user->email}\n";
    echo "  - role: {$user->role}\n";
    echo "  - is_active: " . ($user->is_active ? 'true' : 'false') . "\n";
    echo "  - created_at: {$user->created_at}\n\n";
}

echo "\n=== VALID ROLES ===\n";
echo "- super_admin\n";
echo "- finance_admin\n";
echo "- sales_operator\n";
echo "- ar_collector\n";
echo "- auditor_viewer\n";
echo "- manager\n";
