# COMPLETE RECEIVABLES TESTING FLOW
# ==============================================

$baseUrl = "http://127.0.0.1:8000/api"
$token = "60|BoCq8AvvjU9ezh3FRm0rjB7oxBsityEiWvk80drZec13188d"
$period = "2026-04"

$headers = @{
    "Authorization" = "Bearer $token"
    "Accept" = "application/json"
    "Content-Type" = "application/json"
}

Write-Host "╔══════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║     TESTING ALUR GET /api/receivables                ║" -ForegroundColor Cyan
Write-Host "╚══════════════════════════════════════════════════════╝" -ForegroundColor Cyan

# SETUP: Update invoice dengan DPP dan PPN yang benar menggunakan PHP
Write-Host "`n[SETUP] Update invoice dengan nilai yang sesuai..." -ForegroundColor Yellow
Write-Host "Menjalankan: php -r 'code'" -ForegroundColor Gray

$setupPhp = @'
<?php
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$kernel = $app->make("Illuminate\Contracts\Console\Kernel");
$kernel->bootstrap();

$inv = \App\Models\Invoice::find(10);
$inv->dpp = 5000000;
$inv->ppn = 500000;
$inv->total = 5500000;
$inv->save();

echo "Invoice ID 10 updated: DPP=5000000, PPN=500000";
?>
'@

# Write to temp file and execute
$setupPhp | Out-File -FilePath "temp_setup.php" -Encoding UTF8
php temp_setup.php
Remove-Item "temp_setup.php"

# STEP 1
Write-Host "`n╔═══════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║ STEP 1: GET /api/receivables (BEFORE PROCESS)        ║" -ForegroundColor Cyan
Write-Host "╚═══════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host "Kondisi: Tabel receivables masih kosong (belum diproses)" -ForegroundColor Gray

try {
    $response1 = Invoke-WebRequest -Uri "$baseUrl/receivables" `
        -Method GET `
        -UseBasicParsing `
        -Headers $headers
    
    $data1 = $response1.Content | ConvertFrom-Json
    
    Write-Host "`n✓ Status: $($response1.StatusCode)" -ForegroundColor Green
    Write-Host "✓ Data count: $($data1.data.Count)" -ForegroundColor Yellow
    Write-Host "`nResponse:" -ForegroundColor Cyan
    Write-Host ($data1 | ConvertTo-Json -Depth 2) -ForegroundColor White
} catch {
    Write-Host "✗ ERROR: $($_.Exception.Message)" -ForegroundColor Red
}

# STEP 2
Write-Host "`n╔═══════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║ STEP 2: POST /api/receivables/process               ║" -ForegroundColor Cyan
Write-Host "╚═══════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host "Aksi: Generate receivables dari posted invoices untuk periode $period" -ForegroundColor Gray

$body = @{
    period = $period
} | ConvertTo-Json

try {
    $response2 = Invoke-WebRequest -Uri "$baseUrl/receivables/process" `
        -Method POST `
        -UseBasicParsing `
        -Headers $headers `
        -Body $body
    
    $data2 = $response2.Content | ConvertFrom-Json
    
    Write-Host "`n✓ Status: $($response2.StatusCode)" -ForegroundColor Green
    Write-Host "✓ Message: $($data2.message)" -ForegroundColor Cyan
    Write-Host "`nSummary:" -ForegroundColor Cyan
    Write-Host "  • Total invoices (is_posted=true): $($data2.summary.total_invoices)" -ForegroundColor Yellow
    Write-Host "  • Receivables created: $($data2.summary.receivables_created)" -ForegroundColor Yellow
    Write-Host "  • Receivables updated: $($data2.summary.receivables_updated)" -ForegroundColor Yellow
    Write-Host "  • Errors: $($data2.summary.errors_count)" -ForegroundColor Yellow
    
    if ($data2.errors.Count -gt 0) {
        Write-Host "`n✗ Errors occurred:" -ForegroundColor Red
        $data2.errors | ForEach-Object {
            Write-Host "  - Invoice $($_.invoice_id): $($_.error)" -ForegroundColor Red
        }
    }
} catch {
    Write-Host "✗ ERROR: $($_.Exception.Message)" -ForegroundColor Red
}

# STEP 3
Write-Host "`n╔═══════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║ STEP 3: GET /api/receivables (AFTER PROCESS)         ║" -ForegroundColor Cyan
Write-Host "╚═══════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host "Kondisi: Tabel receivables sudah berisi data dari proses" -ForegroundColor Gray

try {
    $response3 = Invoke-WebRequest -Uri "$baseUrl/receivables" `
        -Method GET `
        -UseBasicParsing `
        -Headers $headers
    
    $data3 = $response3.Content | ConvertFrom-Json
    
    Write-Host "`n✓ Status: $($response3.StatusCode)" -ForegroundColor Green
    Write-Host "✓ Data count: $($data3.data.Count)" -ForegroundColor Yellow
    
    if ($data3.data.Count -gt 0) {
        Write-Host "`nReceivables data yang dikembalikan:" -ForegroundColor Cyan
        Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Cyan
        
        $data3.data | ForEach-Object {
            Write-Host "`n  ID: $($_.id)" -ForegroundColor White
            Write-Host "  Period: $($_.period)" -ForegroundColor Gray
            Write-Host "  Invoice: $($_.invoice_number)" -ForegroundColor Cyan
            Write-Host "  Client: $($_.client_name) (Code: $($_.client_code))" -ForegroundColor Cyan
            Write-Host "  ─────────────────────────────────" -ForegroundColor Gray
            Write-Host "  Beginning Balance: Rp $('{0:N0}' -f [double]$_.beginning_balance)" -ForegroundColor White
            Write-Host "  Netto (DPP): Rp $('{0:N0}' -f [double]$_.netto)" -ForegroundColor White
            Write-Host "  PPN: Rp $('{0:N0}' -f [double]$_.ppn)" -ForegroundColor White
            Write-Host "  Biaya: Rp $('{0:N0}' -f [double]$_.biaya)" -ForegroundColor White
            Write-Host "  Nota Debet: Rp $('{0:N0}' -f [double]$_.nota_debet)" -ForegroundColor White
            Write-Host "  Payment: Rp $('{0:N0}' -f [double]$_.payment)" -ForegroundColor White
            Write-Host "  ─────────────────────────────────" -ForegroundColor Gray
            Write-Host "  Ending Balance: Rp $('{0:N0}' -f [double]$_.ending_balance)" -ForegroundColor Cyan
            Write-Host "  Status: $($_.status)" -ForegroundColor White
        }
        Write-Host "`n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Cyan
    } else {
        Write-Host "`n⚠ Tidak ada data receivables (kemungkinan: semua invoice sudah fully paid)" -ForegroundColor Yellow
    }
} catch {
    Write-Host "✗ ERROR: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`n╔══════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║              TEST COMPLETED SUCCESSFULLY             ║" -ForegroundColor Green
Write-Host "╚══════════════════════════════════════════════════════╝" -ForegroundColor Green

Write-Host "`nRINGKASAN ALUR:" -ForegroundColor Cyan
Write-Host "1. Endpoint GET /receivables mengambil data dari tabel receivables" -ForegroundColor White
Write-Host "2. Tabel kosong sampai POST /receivables/process dijalankan" -ForegroundColor White
Write-Host "3. POST process membaca invoices dengan status is_posted=true" -ForegroundColor White
Write-Host "4. Hitung outstanding balance (total - payments)" -ForegroundColor White
Write-Host "5. Insert receivables ke tabel untuk setiap invoice dengan saldo > 0" -ForegroundColor White
Write-Host "6. GET mengembalikan data receivables yang sudah di-generate" -ForegroundColor White
