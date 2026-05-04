# RECEIVABLES ENDPOINT TESTING
# ========================================

$baseUrl = "http://127.0.0.1:8000/api"
$token = "60|BoCq8AvvjU9ezh3FRm0rjB7oxBsityEiWvk80drZec13188d"
$period = "2026-04"

$headers = @{
    "Authorization" = "Bearer $token"
    "Accept" = "application/json"
    "Content-Type" = "application/json"
}

Write-Host "=====================================" -ForegroundColor Cyan
Write-Host "ALUR TESTING GET /api/receivables" -ForegroundColor Cyan
Write-Host "=====================================" -ForegroundColor Cyan

# STEP 1
Write-Host "`n[STEP 1] GET /api/receivables (SEBELUM PROCESS)" -ForegroundColor Yellow
Write-Host "Status: Tabel receivables masih kosong" -ForegroundColor Gray

try {
    $response1 = Invoke-WebRequest -Uri "$baseUrl/receivables" `
        -Method GET `
        -UseBasicParsing `
        -Headers $headers
    
    $data1 = $response1.Content | ConvertFrom-Json
    
    Write-Host "Response status: $($response1.StatusCode)" -ForegroundColor Green
    Write-Host "Data count: $($data1.data.Count)" -ForegroundColor Cyan
    Write-Host "`nResponse JSON:" -ForegroundColor Cyan
    Write-Host ($data1 | ConvertTo-Json -Depth 2) -ForegroundColor White
} catch {
    Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
}

# STEP 2
Write-Host "`n`n[STEP 2] POST /api/receivables/process (GENERATE DATA)" -ForegroundColor Yellow
Write-Host "Action: Process invoices untuk periode $period" -ForegroundColor Gray

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
    
    Write-Host "Response status: $($response2.StatusCode)" -ForegroundColor Green
    Write-Host "Message: $($data2.message)" -ForegroundColor Cyan
    Write-Host "Summary:" -ForegroundColor Cyan
    Write-Host "  - Total invoices: $($data2.summary.total_invoices)" -ForegroundColor Yellow
    Write-Host "  - Created: $($data2.summary.receivables_created)" -ForegroundColor Yellow
    Write-Host "  - Updated: $($data2.summary.receivables_updated)" -ForegroundColor Yellow
    Write-Host "  - Errors: $($data2.summary.errors_count)" -ForegroundColor Yellow
    
    if ($data2.errors.Count -gt 0) {
        Write-Host "`nErrors occurred:" -ForegroundColor Red
        $data2.errors | ForEach-Object {
            Write-Host "  - Invoice $($_.invoice_id): $($_.error)" -ForegroundColor Red
        }
    }
} catch {
    Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
}

# STEP 3
Write-Host "`n`n[STEP 3] GET /api/receivables (SETELAH PROCESS)" -ForegroundColor Yellow
Write-Host "Status: Sekarang tabel sudah berisi data" -ForegroundColor Gray

try {
    $response3 = Invoke-WebRequest -Uri "$baseUrl/receivables" `
        -Method GET `
        -UseBasicParsing `
        -Headers $headers
    
    $data3 = $response3.Content | ConvertFrom-Json
    
    Write-Host "Response status: $($response3.StatusCode)" -ForegroundColor Green
    Write-Host "Data count: $($data3.data.Count)" -ForegroundColor Cyan
    
    if ($data3.data.Count -gt 0) {
        Write-Host "`nData yang dikembalikan:" -ForegroundColor Cyan
        $data3.data | ForEach-Object {
            Write-Host "`n  ID: $($_.id)" -ForegroundColor White
            Write-Host "  Period: $($_.period)" -ForegroundColor White
            Write-Host "  Invoice: $($_.invoice_number)" -ForegroundColor White
            Write-Host "  Client: $($_.client_name) ($($_.client_code))" -ForegroundColor White
            Write-Host "  Ending Balance: $($_.ending_balance)" -ForegroundColor Cyan
            Write-Host "  Status: $($_.status)" -ForegroundColor White
        }
    }
} catch {
    Write-Host "ERROR: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "`n`n=====================================" -ForegroundColor Cyan
Write-Host "TEST SELESAI" -ForegroundColor Green
Write-Host "=====================================" -ForegroundColor Cyan
