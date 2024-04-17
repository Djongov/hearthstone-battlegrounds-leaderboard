$region = "EU"
$regionApi = $region.ToLower()
$season = 6

# Define the base API endpoint URL
$baseApiUrl = "https://hearthstone.blizzard.com/en-us/api/community/leaderboardsData?region=$region&leaderboardId=battlegrounds&seasonId=11&page="

if ($PSEdition -eq 'Desktop') {
    class TrustAllCertsPolicy : System.Net.ICertificatePolicy {
        [bool] CheckValidationResult (
            [System.Net.ServicePoint]$srvPoint,
            [System.Security.Cryptography.X509Certificates.X509Certificate]$certificate,
            [System.Net.WebRequest]$request,
            [int]$certificateProblem
        ) {
            return $true
        }
    }

    [System.Net.ServicePointManager]::CertificatePolicy = New-Object -TypeName TrustAllCertsPolicy
}

$headers = @{
    "X-API-KEY" = "f55b772090315f22133483f0d3d1a6cbbbe375aa0034f9b034a2d5a81a85cf16"
    "secretheader" = "badass"  # Add your additional header here
}

# Define the page size (number of results per page)
$pageSize = 25

# Start with page 1
$page = 1

# Loop to fetch and process pages until there are no more pages left
while ($true) {
    # Construct the API endpoint URL for the current page
    $url = $baseApiUrl + $page

    # Fetch data from the API endpoint
    $response = Invoke-RestMethod -Uri $url -Method Get

    Write-Host "Calling API on url $url" -ForegroundColor Yellow

    # Check if the response contains the 'leaderboard' key
    if ($response.PSObject.Properties.Name -contains "leaderboard") {
        # Access the 'leaderboard' key
        $leaderboard = $response.leaderboard

        # Check if the 'leaderboard' key contains the 'rows' key
        if ($leaderboard.PSObject.Properties.Name -contains "rows") {
            # Access the 'rows' key
            $rows = $leaderboard.rows

            Write-Host "Processing page $page" -ForegroundColor Yellow

            foreach ($row in $rows) {
                $rank = $row.rank
                $accountid = $row.accountid
                $rating = $row.rating
                # Encode Chinese characters properly
                $encodedAccountId = [System.Web.HttpUtility]::UrlEncode($accountId)
                # Run the PHP script using php.exe
                #Invoke-RestMethod -Uri "https://template-api.gamerz-bg.com/v1/hs-battlegrounds" -Headers @{"secretheader"="badass"} -Method POST -Body "rank=$rank&accountid=$encodedAccountId&rating=$rating"
                Invoke-RestMethod -Uri "https://hearthstone-bg-leaderboard.gamerz-bg.com/api/$season/$regionApi/record" -Headers $headers -Method POST -Body "rank=$rank&accountid=$encodedAccountId&rating=$rating"
            }

            # Check if there's a next page available
            $totalPages = $response.leaderboard.pagination.totalPages
            Write-Host "totalPages: $totalPages" -ForegroundColor Yellow
            if ($page -lt $totalPages) {
                $page++
            } else {
                Write-Host "No more pages left."
                break
            }
            sleep(2);
        } else {
            Write-Host "No 'rows' key found in the 'leaderboard' data." -ForegroundColor Red
            break
        }
    } else {
        Write-Host "No 'leaderboard' key found in the response." -ForegroundColor Red
        break
    }
}
