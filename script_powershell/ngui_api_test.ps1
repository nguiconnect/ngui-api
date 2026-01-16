$ErrorActionPreference = "Stop"

$BASE_URL = $env:BASE_URL
if (-not $BASE_URL) { $BASE_URL = "http://127.0.0.1:8000" }

$ADMIN_EMAIL = $env:ADMIN_EMAIL; if (-not $ADMIN_EMAIL) { $ADMIN_EMAIL = "admin@ngui.test" }
$ADMIN_PASSWORD = $env:ADMIN_PASSWORD; if (-not $ADMIN_PASSWORD) { $ADMIN_PASSWORD = "password" }

$PROVIDER_EMAIL = $env:PROVIDER_EMAIL; if (-not $PROVIDER_EMAIL) { $PROVIDER_EMAIL = "prestataire@ngui.test" }
$PROVIDER_PASSWORD = $env:PROVIDER_PASSWORD; if (-not $PROVIDER_PASSWORD) { $PROVIDER_PASSWORD = "password" }

$PROVIDER_ID = $env:PROVIDER_ID; if (-not $PROVIDER_ID) { $PROVIDER_ID = 3 }

$QUOTE_BODY = @{
  provider_id = [int]$PROVIDER_ID
  name = "Test Devis PowerShell"
  email = "client@test.com"
  phone = "+44700000000"
  message = "Bonjour, devis (PowerShell)."
}

function Step($title) {
  Write-Host ""
  Write-Host "===================="
  Write-Host $title
  Write-Host "===================="
}

Step "1) Login Provider"
$providerLogin = Invoke-RestMethod -Method Post -Uri "$BASE_URL/api/login" -Headers @{Accept="application/json"} -ContentType "application/json" -Body (@{email=$PROVIDER_EMAIL; password=$PROVIDER_PASSWORD} | ConvertTo-Json)
$providerLogin | ConvertTo-Json -Depth 10
$providerToken = $providerLogin.token
if (-not $providerToken) { throw "Provider token missing" }

Step "2) Login Admin"
$adminLogin = Invoke-RestMethod -Method Post -Uri "$BASE_URL/api/login" -Headers @{Accept="application/json"} -ContentType "application/json" -Body (@{email=$ADMIN_EMAIL; password=$ADMIN_PASSWORD} | ConvertTo-Json)
$adminLogin | ConvertTo-Json -Depth 10
$adminToken = $adminLogin.token
if (-not $adminToken) { throw "Admin token missing" }

Step "3) Create Quote (public)"
$createQuote = Invoke-RestMethod -Method Post -Uri "$BASE_URL/api/v1/quotes" -Headers @{Accept="application/json"} -ContentType "application/json" -Body ($QUOTE_BODY | ConvertTo-Json)
$createQuote | ConvertTo-Json -Depth 10
$quoteId = $createQuote.data.id
if (-not $quoteId) { throw "quote_id missing (expected data.id)" }

Step "4) Provider - List My Quotes"
$provList = Invoke-RestMethod -Method Get -Uri "$BASE_URL/api/v1/provider/quotes" -Headers @{Accept="application/json"; Authorization="Bearer $providerToken"}
$provList | ConvertTo-Json -Depth 10

Step "5) Provider - Update Quote Status"
$provUpdate = Invoke-RestMethod -Method Patch -Uri "$BASE_URL/api/v1/provider/quotes/$quoteId" -Headers @{Accept="application/json"; Authorization="Bearer $providerToken"} -ContentType "application/json" -Body (@{status="contacted"} | ConvertTo-Json)
$provUpdate | ConvertTo-Json -Depth 10

Step "6) Admin - Update Quote Status"
$adminUpdate = Invoke-RestMethod -Method Patch -Uri "$BASE_URL/api/v1/quotes/$quoteId" -Headers @{Accept="application/json"; Authorization="Bearer $adminToken"} -ContentType "application/json" -Body (@{status="done"} | ConvertTo-Json)
$adminUpdate | ConvertTo-Json -Depth 10

Step "✅ DONE"
Write-Host "quote_id=$quoteId"
