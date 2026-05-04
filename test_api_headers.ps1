
$baseUrl = "http://127.0.0.1:8000/api"
$email = "superadmin@fitart.co.id"
$password = "password"

$headers = @{
    "Accept" = "application/json"
    "Content-Type" = "application/json"
}

$loginBody = @{
    email = $email
    password = $password
} | ConvertTo-Json

Write-Host "Attempt login with Accept: application/json header" -ForegroundColor Cyan

try {
    $response = Invoke-WebRequest -Uri "$baseUrl/login" `
        -Method POST `
        -UseBasicParsing `
        -Headers $headers `
        -Body $loginBody `
        -ErrorAction Stop

    Write-Host "Status: $($response.StatusCode)" -ForegroundColor Green
    Write-Host "Content-Type: $($response.Headers['Content-Type'])" -ForegroundColor Gray
    Write-Host "Response:" -ForegroundColor Cyan
    Write-Host $response.Content
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}
