
# ==========================================
# TEST RECEIVABLES FLOW - DEBUG VERSION
# ==========================================

$baseUrl = "http://127.0.0.1:8000/api"
$email = "superadmin@fitart.co.id"
$password = "password"
$period = "2026-04"

Write-Host "STEP 1: LOGIN" -ForegroundColor Cyan
Write-Host "URL: $baseUrl/login" -ForegroundColor Gray
Write-Host "Email: $email" -ForegroundColor Gray

$loginBody = @{
    email = $email
    password = $password
} | ConvertTo-Json

Write-Host "Request body:" -ForegroundColor Gray
Write-Host $loginBody -ForegroundColor Gray

try {
    $loginResponse = Invoke-WebRequest -Uri "$baseUrl/login" `
        -Method POST `
        -ContentType "application/json" `
        -UseBasicParsing `
        -Body $loginBody `
        -ErrorAction Stop

    Write-Host "Response status: $($loginResponse.StatusCode)" -ForegroundColor Green
    Write-Host "Response body:" -ForegroundColor Gray
    Write-Host $loginResponse.Content -ForegroundColor Gray
    
    $loginData = $loginResponse.Content | ConvertFrom-Json
    $token = $loginData.data.token
    
    Write-Host "Token extracted: $($token.Substring(0, 30))..." -ForegroundColor Green
} catch {
    Write-Host "ERROR during login:" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    exit 1
}

$headers = @{
    "Authorization" = "Bearer $token"
    "Content-Type" = "application/json"
}

Write-Host "`nSTEP 2: GET /api/receivables (BEFORE)" -ForegroundColor Cyan
Write-Host "URL: $baseUrl/receivables" -ForegroundColor Gray

try {
    $beforeResponse = Invoke-WebRequest -Uri "$baseUrl/receivables" `
        -Method GET `
        -UseBasicParsing `
        -Headers $headers `
        -ErrorAction Stop

    Write-Host "Response status: $($beforeResponse.StatusCode)" -ForegroundColor Green
    Write-Host "Response body:" -ForegroundColor Gray
    Write-Host $beforeResponse.Content -ForegroundColor Gray
    
    $beforeData = $beforeResponse.Content | ConvertFrom-Json
    $count = $beforeData.data.Count
    
    Write-Host "Total receivables: $count" -ForegroundColor Yellow
} catch {
    Write-Host "ERROR:" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
}

Write-Host "`nSTEP 3: POST /api/receivables/process" -ForegroundColor Cyan
Write-Host "URL: $baseUrl/receivables/process" -ForegroundColor Gray
Write-Host "Period: $period" -ForegroundColor Gray

$processBody = @{
    period = $period
} | ConvertTo-Json

Write-Host "Request body:" -ForegroundColor Gray
Write-Host $processBody -ForegroundColor Gray

try {
    $processResponse = Invoke-WebRequest -Uri "$baseUrl/receivables/process" `
        -Method POST `
        -UseBasicParsing `
        -Headers $headers `
        -Body $processBody `
        -ErrorAction Stop

    Write-Host "Response status: $($processResponse.StatusCode)" -ForegroundColor Green
    Write-Host "Response body:" -ForegroundColor Gray
    Write-Host $processResponse.Content -ForegroundColor Gray
    
    $processData = $processResponse.Content | ConvertFrom-Json
    Write-Host "Created: $($processData.summary.receivables_created)" -ForegroundColor Yellow
} catch {
    Write-Host "ERROR:" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
}

Write-Host "`nSTEP 4: GET /api/receivables (AFTER)" -ForegroundColor Cyan

try {
    $afterResponse = Invoke-WebRequest -Uri "$baseUrl/receivables" `
        -Method GET `
        -UseBasicParsing `
        -Headers $headers `
        -ErrorAction Stop

    Write-Host "Response status: $($afterResponse.StatusCode)" -ForegroundColor Green
    Write-Host "Response body:" -ForegroundColor Gray
    Write-Host $afterResponse.Content -ForegroundColor Gray
    
    $afterData = $afterResponse.Content | ConvertFrom-Json
    $count = $afterData.data.Count
    Write-Host "Total receivables: $count" -ForegroundColor Yellow
} catch {
    Write-Host "ERROR:" -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
}

Write-Host "`nDONE!" -ForegroundColor Green
