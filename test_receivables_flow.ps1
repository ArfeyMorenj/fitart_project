
# ==========================================
# TEST FLOW: GET /api/receivables
# ==========================================
# Alur:
# 1. Login → dapatkan token
# 2. GET /receivables (kosong)
# 3. POST /receivables/process untuk generate data
# 4. GET /receivables (ada data)
# ==========================================

$baseUrl = "http://127.0.0.1:8000/api"
$email = "superadmin@fitart.co.id"
$password = "password"
$period = "2026-04"

Write-Host "========== STEP 1: LOGIN ==========" -ForegroundColor Cyan

$loginBody = @{
    email = $email
    password = $password
} | ConvertTo-Json

try {
    $loginResponse = Invoke-WebRequest -Uri "$baseUrl/login" `
        -Method POST `
        -ContentType "application/json" `
        -UseBasicParsing `
        -Body $loginBody

    $loginData = $loginResponse.Content | ConvertFrom-Json
    $token = $loginData.data.token
    
    Write-Host "✓ Login berhasil" -ForegroundColor Green
    Write-Host "Token: $($token.Substring(0, 20))..." -ForegroundColor Yellow
} catch {
    Write-Host "✗ Login gagal" -ForegroundColor Red
    Write-Host $_.Exception.Message
    exit 1
}

# Setup headers dengan token
$headers = @{
    "Authorization" = "Bearer $token"
    "Content-Type" = "application/json"
}

# ==========================================
Write-Host "`n========== STEP 2: GET /api/receivables (BEFORE) ==========" -ForegroundColor Cyan

try {
    $beforeResponse = Invoke-WebRequest -Uri "$baseUrl/receivables" `
        -Method GET `
        -UseBasicParsing `
        -Headers $headers

    $beforeData = $beforeResponse.Content | ConvertFrom-Json
    $count = $beforeData.data.Count
    
    Write-Host "✓ GET /api/receivables berhasil" -ForegroundColor Green
    Write-Host "Total receivables: $count" -ForegroundColor Yellow
    if ($count -eq 0) {
        Write-Host "⚠ Tabel receivables masih kosong (expected)" -ForegroundColor Yellow
    }
} catch {
    Write-Host "✗ GET /api/receivables gagal" -ForegroundColor Red
    Write-Host $_.Exception.Message
    exit 1
}

# ==========================================
Write-Host "`n========== STEP 3: POST /api/receivables/process ==========" -ForegroundColor Cyan

$processBody = @{
    period = $period
} | ConvertTo-Json

try {
    $processResponse = Invoke-WebRequest -Uri "$baseUrl/receivables/process" `
        -Method POST `
        -UseBasicParsing `
        -Headers $headers `
        -Body $processBody

    $processData = $processResponse.Content | ConvertFrom-Json
    
    Write-Host "✓ POST /api/receivables/process berhasil" -ForegroundColor Green
    Write-Host "Message: $($processData.message)" -ForegroundColor Yellow
    Write-Host "Period: $($processData.period)" -ForegroundColor Yellow
    Write-Host "Summary:" -ForegroundColor Cyan
    Write-Host "  - Total Invoices: $($processData.summary.total_invoices)" -ForegroundColor Yellow
    Write-Host "  - Created: $($processData.summary.receivables_created)" -ForegroundColor Yellow
    Write-Host "  - Updated: $($processData.summary.receivables_updated)" -ForegroundColor Yellow
    Write-Host "  - Errors: $($processData.summary.errors_count)" -ForegroundColor Yellow
} catch {
    Write-Host "✗ POST /api/receivables/process gagal" -ForegroundColor Red
    Write-Host $_.Exception.Message
    exit 1
}

# ==========================================
Write-Host "`n========== STEP 4: GET /api/receivables (AFTER) ==========" -ForegroundColor Cyan

try {
    $afterResponse = Invoke-WebRequest -Uri "$baseUrl/receivables" `
        -Method GET `
        -UseBasicParsing `
        -Headers $headers

    $afterData = $afterResponse.Content | ConvertFrom-Json
    $count = $afterData.data.Count
    
    Write-Host "✓ GET /api/receivables berhasil" -ForegroundColor Green
    Write-Host "Total receivables: $count" -ForegroundColor Yellow
    
    if ($count -gt 0) {
        Write-Host "`nData yang dikembalikan:" -ForegroundColor Cyan
        $afterData.data | ForEach-Object {
            Write-Host "  ID: $($_.id)" -ForegroundColor White
            Write-Host "  Invoice: $($_.invoice_number)" -ForegroundColor White
            Write-Host "  Client: $($_.client_name) ($($_.client_code))" -ForegroundColor White
            Write-Host "  Period: $($_.period)" -ForegroundColor White
            Write-Host "  Ending Balance: $($_.ending_balance)" -ForegroundColor White
            Write-Host "  Status: $($_.status)" -ForegroundColor White
            Write-Host ""
        }
    }
} catch {
    Write-Host "✗ GET /api/receivables gagal" -ForegroundColor Red
    Write-Host $_.Exception.Message
    exit 1
}

Write-Host "`n========== TEST SELESAI ==========" -ForegroundColor Green
Write-Host "Success! Alur GET /api/receivables sudah berhasil di-test!" -ForegroundColor Green
