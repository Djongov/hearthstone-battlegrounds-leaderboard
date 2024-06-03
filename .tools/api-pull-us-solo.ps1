# Let's accept parameters, region, season, type
param (
    [string]$region = "US",
    [int]$season = 7,
    [string]$type = "solo",
    [int]$startPage = 1,
    [int]$maxPage = 0,
    [string]$apiKey = "",
    [string]$secretHeader = ""
)

# Some other param checks
if ($region -ne "US" -and $region -ne "EU" -and $region -ne "AP") {
    Write-Output "Invalid region, only support US, EU or AP. Exiting script."
    exit 1
}

if ($type -ne "solo" -and $type -ne "duos") {
    Write-Output "Invalid type, only supports solo or duos. Exiting script."
    exit 1
}

if ($season -ne 7) {
    Write-Output "Invalid season, only accepts 7. Exiting script."
    exit 1
}

if ($startPage -lt 1) {
    Write-Output "Invalid startPage, must be greater than 0. Exiting script."
    exit 1
}

# Convert the region to lowercase because public HS API requires it to be upper but our own API requires it to be lower
$regionApi = $region.ToLower()

# Initialize the output variable so we can save the output of the API calls
$output = ""

# Decide whether we pull solo or duos data
if ($type -eq "solo") {
    $battlegroundId = "battlegrounds"
}
else {
    $battlegroundId = "battlegroundsduo"
}

Write-Output "Pulling data for $region $season $type"

# Define the base API endpoint URL
$baseApiUrl = "https://hearthstone.blizzard.com/api/community/leaderboardsData?region=$region&leaderboardId=$battlegroundId&page="

# We need to add the API key to the headers for the call to the internal API
$headers = @{
    "X-API-KEY"    = $apiKey
    "secretheader" = $secretHeader
}

if ($maxPage -eq 0) {
    $response = Invoke-RestMethod -Uri $baseApiUrl -Method Get -UserAgent "hearthstone-bg-leaderboard/1.0.0"
    # Check if there's a next page available
    $totalPages = $response.leaderboard.pagination.totalPages
    Write-Output "totalPages: $totalPages"
    $maxPage = $totalPages
    Start-Sleep -Seconds 2
}

while ($true) {
    $startPage = 1
    # Loop to fetch and process pages until there are no more pages left
    while ($startPage -le $maxPage) {
        # Construct the API endpoint URL for the current page
        $url = $baseApiUrl + $startPage

        # Fetch data from the public HS API endpoint
        $response = Invoke-RestMethod -Uri $url -Method Get -UserAgent "hearthstone-bg-leaderboard/1.0.0"

        Write-Output "Calling API on url $url"

        # Check if the response contains the 'leaderboard' key
        if ($response.PSObject.Properties.Name -contains "leaderboard") {
            # Access the 'leaderboard' key
            $leaderboard = $response.leaderboard

            # Check if the 'leaderboard' key contains the 'rows' key
            if ($leaderboard.PSObject.Properties.Name -contains "rows") {
                # Access the 'rows' key
                $rows = $leaderboard.rows

                Write-Output "Processing page $startPage"

                foreach ($row in $rows) {
                    $rank = $row.rank
                    $accountid = $row.accountid
                    $rating = $row.rating
                    # Encode Chinese characters properly
                    $encodedAccountId = [System.Web.HttpUtility]::UrlEncode($accountId)
                    try {
                        # Call the internal API to save the data
                        $callApi = Invoke-RestMethod -Uri "https://hearthstone-bg-leaderboard.gamerz-bg.com/api/$season/$type/$regionApi/record" -Headers $headers -Method POST -Body "rank=$rank&accountid=$encodedAccountId&rating=$rating" -UserAgent "Djo-Automation-Azure"
                        Write-Output $callApi
                        #Collect the output
                        $output += "`n"
                        $output += $callApi
                        $output += "`n"
                        $output += "============================================"
                    }
                    catch {
                        if ($null -ne $_.Exception.Response) {
                            $responseBody = $_.Exception.Response.Content.ReadAsStringAsync().Result
                            Write-Output "Error: $responseBody"
                        }
                        else {
                            Write-Output "Error: $_"
                        }
                    }
                }
                if ($startPage -lt $maxPage) {
                    $startPage++
                }
                else {
                    Write-Output "No more pages left to crawl because curent page ($startPage) is less than maxPage ($maxPage)."
                    break
                }
            }
            else {
                Write-Output "No 'rows' key found in the 'leaderboard' data."
                break
            }
        }
        else {
            Write-Output "No 'leaderboard' key found in the response."
            break
        }
    }
}
# Now send the output to me
#Invoke-RestMethod -Method POST -Uri "https://hearthstone-bg-leaderboard.gamerz-bg.com/api/event-collect" -Body "type=$type&season=$season&region=$regionApi&output=$output" -SkipCertificateCheck
