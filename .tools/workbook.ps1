# Input bindings are passed in via param block.
param($Timer)

# Get the current universal time in the default string format.
$currentUTCtime = (Get-Date).ToUniversalTime()

# The 'IsPastDue' property is 'true' when the current function invocation is later than scheduled.
if ($Timer.IsPastDue) {
    Write-Host "PowerShell timer is running late!"
}

# Define the Azure Automation webhook URL
$webhookUrl = "https://ce6ef2d6-d759-420f-a083-35cd61ba4ab6.webhook.we.azure-automation.net/webhooks?token=e9fZqt5huS0R%2bPEJoIt7FO8sRFKV5ih4h5c7%2baB8zn8%3d"

# Create a payload if required
$payload = @{} # Add any required payload data here

# Make an HTTP POST request to trigger the Azure Automation webhook
Invoke-RestMethod -Uri $webhookUrl -Method Post -Body ($payload | ConvertTo-Json) -ContentType 'application/json'

# Write an information log with the current time.
Write-Host "PowerShell timer trigger function ran! TIME: $currentUTCtime"
