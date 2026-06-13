# Create a Premium Store deposit transaction — PowerShell
$apiUrl = "https://yourdomain.com/api/midtrans-create.php"

$payload = @{
    order_id = "DEP-$([DateTimeOffset]::UtcNow.ToUnixTimeSeconds())"
    amount   = 50000
    user_id  = "firebase-uid"
    email    = "customer@example.com"
    name     = "Customer Name"
} | ConvertTo-Json

$response = Invoke-RestMethod -Uri $apiUrl -Method Post `
    -ContentType "application/json" -Body $payload

Write-Host "Snap token : $($response.token)"
Write-Host "Redirect   : $($response.redirect_url)"
