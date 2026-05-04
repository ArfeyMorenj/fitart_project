# COMPLETE RECEIVABLES TESTING FLOW
# ====================================

$baseUrl = "http://127.0.0.1:8000/api"
$token = "60|BoCq8AvvjU9ezh3FRm0rjB7oxBsityEiWvk80drZec13188d"
$period = "2026-04"

$headers = @{
    "Authorization" = "Bearer $token"
    "Accept" = "application/json"
    "Content-Type" = "application/json"
}

Write-Host "=====================================" -ForegroundColor Cyan
Write-Host "TESTING ALUR GET /api/receivables" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan

# SETUP: Update invoice dengan DPP dan PPN yang benar
Write-Host "`n[SETUP] Update invoice dengan nilai DPP dan PPN..." -ForegroundColor Yellow

$setupPhp = @'
<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make("Illuminate\Contracts\Console\Kernel");
$kernel->bootstrap();

$inv = \App\Models\Invoice::find(10);
if ($inv) {
    $inv->dpp = 5000000;
    $inv->ppn = 500000;
    $inv->total = 5500000;
    $inv->save();
    echo "OK: Invoice ID 10 updated";
}
?>
'@

$setupPhp | Out-File -FilePath "temp_setup.php" -Encoding UTF8
php temp_setup.php
Remove-Item "temp_setup.php"

# STEP 1
Write-Host "`n[STEP 1] GET /api/receivables BEFORE PROCESS" -ForegroundColor Yellow
Write-Host "Status: Table receivables masih kosong" -ForegroundColor Gray

try {
    $r1 = Invoke-WebRequest -Uri "$baseUrl/receivables" -Method GET -UseBasicParsing -Headers $headers
    $d1 = $r1.Content | ConvertFrom-Json
    
    Write-Host "Response: $($r1.StatusCode)" -ForegroundColor Green
    Write-Host "Data count: $($d1.data.Count)" -ForegroundColor Cyan
    Write-Host "Response body:" -ForegroundColor Gray
    Write-Host ($d1 | ConvertTo-Json -Depth 3) -ForegroundColor White
} catch {
    Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
}

# STEP 2
Write-Host "`n[STEP 2] POST /api/receivables/process" -ForegroundColor Yellow
Write-Host "Action: Process invoices untuk periode $period" -ForegroundColor Gray

$body = @{ period = $period } | ConvertTo-Json

try {
    $r2 = Invoke-WebRequest -Uri "$baseUrl/receivables/process" -Method POST -UseBasicParsing -Headers $headers -Body $body
    $d2 = $r2.Content | ConvertFrom-Json
    
    Write-Host "Response: $($r2.StatusCode)" -ForegroundColor Green
    Write-Host "Message: $($d2.message)" -ForegroundColor Cyan
    Write-Host "" -ForegroundColor Gray
    Write-Host "Summary:" -ForegroundColor Cyan
    Write-Host "  Total invoices: $($d2.summary.total_invoices)" -ForegroundColor Yellow
    Write-Host "  Created: $($d2.summary.receivables_created)" -ForegroundColor Yellow
    Write-Host "  Updated: $($d2.summary.receivables_updated)" -ForegroundColor Yellow
    Write-Host "  Errors: $($d2.summary.errors_count)" -ForegroundColor Yellow
} catch {
    Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
}

# STEP 3
Write-Host "`n[STEP 3] GET /api/receivables AFTER PROCESS" -ForegroundColor Yellow
Write-Host "Status: Table receivables sudah berisi data" -ForegroundColor Gray

try {
    $r3 = Invoke-WebRequest -Uri "$baseUrl/receivables" -Method GET -UseBasicParsing -Headers $headers
    $d3 = $r3.Content | ConvertFrom-Json
    
    Write-Host "Response: $($r3.StatusCode)" -ForegroundColor Green
    Write-Host "Data count: $($d3.data.Count)" -ForegroundColor Cyan
    
    if ($d3.data.Count -gt 0) {
        Write-Host "`nData received:" -ForegroundColor Cyan
        Write-Host "============================================================" -ForegroundColor Cyan
        $d3.data | ForEach-Object {
            Write-Host "" -ForegroundColor White
            Write-Host "  ID: $($_.id)" -ForegroundColor White
            Write-Host "  Period: $($_.period)" -ForegroundColor Gray
            Write-Host "  Invoice: $($_.invoice_number)" -ForegroundColor Cyan
            Write-Host "  Client: $($_.client_name) [$($_.client_code)]" -ForegroundColor Cyan
            Write-Host "  ---" -ForegroundColor Gray
            Write-Host "  Beginning Balance: $($_.beginning_balance)" -ForegroundColor White
            Write-Host "  Netto (DPP): $($_.netto)" -ForegroundColor White
            Write-Host "  PPN: $($_.ppn)" -ForegroundColor White
            Write-Host "  Payment: $($_.payment)" -ForegroundColor White
            Write-Host "  Ending Balance: $($_.ending_balance)" -ForegroundColor Cyan
            Write-Host "  Status: $($_.status)" -ForegroundColor White
        }
        Write-Host "" -ForegroundColor White
        Write-Host "============================================================" -ForegroundColor Cyan
    } else {
        Write-Host "No receivables found" -ForegroundColor Yellow
    }
} catch {
    Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`n=====================================" -ForegroundColor Green
Write-Host "TEST COMPLETED" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Green

Write-Host "`nALUR LENGKAP:" -ForegroundColor Cyan
Write-Host "1. GET /receivables - ambil dari tabel receivables" -ForegroundColor White
Write-Host "2. POST /receivables/process - generate data dari invoices" -ForegroundColor White
Write-Host "3. GET /receivables - return data yang sudah di-generate" -ForegroundColor White
