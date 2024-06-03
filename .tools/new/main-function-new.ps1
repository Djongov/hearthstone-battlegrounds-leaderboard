function scrape {
    param (
        [string]$region,
        [int]$season,
        [string]$type,
        [int]$startPage,
        [int]$maxPage,
        [bool]$debug = $false
    )
    cls
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

    # Load MySQL Connector/NET assembly
    Add-Type -Path "C:\Program Files (x86)\MySQL\Connector NET 8.0\MySql.Data.dll"


    $sslMode = $true
    $ratingGap = 700

    # Define MySQL connection parameters
    $server = "localhost"
    $database = "hearthstone"
    $username = "root"
    $password = $env:DBPASS
    if ($sslMode) {
        $sslCa = "../DigiCertGlobalRootCA.crt.pem"
        # Create MySQL connection
        $connection = New-Object MySql.Data.MySqlClient.MySqlConnection
        $connection.ConnectionString = "server=$server;port=3306;database=$database;uid=$username;password=$password;SslMode=Required;SslCa=$sslCa"
    } else {
        # Create MySQL connection
        $connection = New-Object MySql.Data.MySqlClient.MySqlConnection
        $connection.ConnectionString = "server=$server;port=3306;database=$database;uid=$username;password=$password"
    }

    # Open the connection
    try {
        $connection.Open()
    } catch {
        Write-Host $_ -ForegroundColor Red
        return
    }

    # Convert the region to lowercase because public HS API requires it to be upper but our own API requires it to be lower
    $regionApi = $region.ToLower()

    $table = "battlegrounds_season_" + $season + "_" + $regionApi + "_" + $type

    $ratingProgressionTable = 'rating_progression_season_' + $season + '_' + $region + '_' + $type;
    $rankProgressionTable = 'rank_progression_season_' + $season + '_' + $region + '_' + $type;

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

    if ($maxPage -eq 0) {
        $response = Invoke-RestMethod -Uri $baseApiUrl -Method Get -UserAgent "hearthstone-bg-leaderboard/1.0.0"
        # Check if there's a next page available
        $totalPages = $response.leaderboard.pagination.totalPages
        Write-Output "totalPages: $totalPages"
        $maxPage = $totalPages
        Start-Sleep -Seconds 2
    }

    $run = 1

    while ($true) {
        $startPage = 1
        Write-Host "Scraping Run #$run" -ForegroundColor Green
        # Let's start measuring how long this iteration will take
        $start = Get-Date
        # Loop to fetch and process pages until there are no more pages left
        while ($startPage -le $maxPage) {
            Start-Sleep -Seconds 4
            # Construct the API endpoint URL for the current page
            $url = $baseApiUrl + $startPage

            # Fetch data from the public HS API endpoint
            $response = Invoke-RestMethod -Uri $url -Method Get -UserAgent "hearthstone-bg-leaderboard/1.0.0"

            #Write-Output "Calling API on url $url"

            # Check if the response contains the 'leaderboard' key
            if ($response.PSObject.Properties.Name -contains "leaderboard") {
                # Access the 'leaderboard' key
                $leaderboard = $response.leaderboard

                # Check if the 'leaderboard' key contains the 'rows' key
                if ($leaderboard.PSObject.Properties.Name -contains "rows") {
                    # Access the 'rows' key
                    $rows = $leaderboard.rows

                    Write-Output "Processing page $startPage out of $totalPages"

                    # Because we need to have backticks on the sql command `rank`, but powershell uses the backticks to escape characters, we need to use a variable to store the column name

                    $rankColumnName = '`rank`'
                    $ratingColumnName = '`rating`'
                    $accountIdColumnName = '`accountid`'

                    # Ensure the connection is open
                    if ($connection.State -ne 'Open') {
                        $connection.Open()
                    }

                    $records = @()  # Initialize an empty array to store records

                    foreach ($row in $rows) {
                        $accountId = $row.accountid
                        $rank = $row.rank
                        $rating = $row.rating

                        # Execute SQL query to retrieve existing records
                        $command = $connection.CreateCommand()
                        $query = "SELECT id, $accountIdColumnName, $rankColumnName, $ratingColumnName FROM $table WHERE $accountIdColumnName = '$accountId'"
                        $command.CommandText = $query
                        $reader = $command.ExecuteReader()

                        # Check if any rows are returned
                        if ($reader.HasRows) {
                            while ($reader.Read()) {
                                # Store each row in a hashtable
                                $record = @{
                                    'id'        = $reader['id']
                                    'rank'      = $reader['rank']
                                    'rating'    = $reader['rating']
                                    'accountId' = $reader['accountId']
                                }
                                # Add the hashtable to the array
                                $records += $record
                            }
                            # Now if record has more than 1 row, print it out
                            if ($records.Count -gt 1) {
                                if ($debug) {
                                    Write-Host "Found more than 1 record for account ID: $accountId" -ForegroundColor Cyan
                                    # Print all the records
                                    foreach ($record in $records) {
                                        Write-Host "Account ID: $($record['accountId']), Existing Rank: $($record['rank']), Existing Rating: $($record['rating']), id: $($record['id'])" -ForegroundColor Cyan
                                    }
                                }

                                # Ensure $records is not null
                                if ($null -ne $records) {
                                    # Initialize variables to track the closest rank and rating
                                    $closestRecord = $records[0]
                                    $closestDiffRank = [Math]::Abs($rank - $records[0]['rank'])
                                    $closestDiffRating = [Math]::Abs($rating - $records[0]['rating'])

                                    # Iterate through the records to find the closest one
                                    for ($i = 1; $i -lt $records.Count; $i++) {
                                        $record = $records[$i]
                                        $currentDiffRank = [Math]::Abs($rank - $record['rank'])
                                        $currentDiffRating = [Math]::Abs($rating - $record['rating'])

                                        # Update closest record if the current record is closer
                                        if ($currentDiffRank -lt $closestDiffRank -or ($currentDiffRank -eq $closestDiffRank -and $currentDiffRating -lt $closestDiffRating)) {
                                            $closestRecord = $record
                                            $closestDiffRank = $currentDiffRank
                                            $closestDiffRating = $currentDiffRating
                                            # stop the for loop if we have found the closes trecord
                                            if ($closestDiffRank -eq 0 -and $closestDiffRating -eq 0) {
                                                break
                                            }
                                        }
                                    }

                                    # Print the closest record
                                    if ($debug) {
                                        Write-Host "Closest Record - Account ID: $($closestRecord['accountId']), Rank: $($closestRecord['rank']), Rating: $($closestRecord['rating']), id $($closestRecord['id'])" -ForegroundColor Yellow
                                    }

                                    # Check if the closest record is different from the new values
                                    if ($rank -eq $closestRecord['rank'] -and $rating -eq $closestRecord['rating']) {
                                        # No change, skip updating
                                        if ($debug) {
                                            Write-Host "No changes detected for accountid $accountId" -ForegroundColor Yellow
                                        }
                                    } else {
                                        if ([Math]::Abs($closestRecord['rating'] - $rating) -gt $ratingGap) {
                                            Write-Host "Rating difference is too big ($closestRecord['rating'] - $rating) for $accountid, not inserting to progression tables" -ForegroundColor Red
                                            $reader.Close()  # Close the DataReader before executing another command
                                            
                                            # Insert new
                                            $insertCommand = $connection.CreateCommand()
                                            $insertCommand.CommandText = "INSERT INTO $table ($accountIdColumnName, $rankColumnName, $ratingColumnName) VALUES ('$accountId', $rank, $rating)"

                                            try {
                                                $insertCommand.ExecuteNonQuery() | Out-Null
                                                if ($debug) {
                                                    Write-Host "New record inserted for accountid $accountId with rank $rank and rating $rating" -ForegroundColor Green
                                                }
                                            }
                                            catch {
                                                Write-Host "Error inserting record for accountid $accountId" -ForegroundColor Red
                                                Write-Host $_.Exception.Message -ForegroundColor Red
                                            }
                                            
                                        } else {
                                            # Update
                                            $reader.Close()  # Close the DataReader before executing another command

                                            $updateCommand = $connection.CreateCommand()
                                            $updateCommand.CommandText = "UPDATE $table SET $rankColumnName = $rank, $ratingColumnName = $rating WHERE $accountIdColumnName = '$accountId' AND id=$($closestRecord['id'])"
                                            try {
                                                $updateCommand.ExecuteNonQuery() | Out-Null
                                                if ($debug) {
                                                    Write-Host "Record updated for accountid $accountId (id is $($closestRecord['id'])) with rank $rank and rating $rating. Was rank $($closestRecord['rank']) and rating $($closestRecord['rating'])" -ForegroundColor Green
                                                }
                                            }
                                            catch {
                                                Write-Host "Error updating record for accountid $accountId" -ForegroundColor Red
                                                Write-Host $_.Exception.Message -ForegroundColor Red
                                            }
                                            # Update the rank and progression tables
                                            $insertRatingProgressionCommand = $connection.CreateCommand()
                                            $insertRatingProgressionCommand.CommandText = "INSERT INTO $ratingProgressionTable ($accountIdColumnName, $ratingColumnName, main_id) VALUES ('$accountId', $rating, $($closestRecord['id']))"
                                            try {
                                                $insertRatingProgressionCommand.ExecuteNonQuery() | Out-Null
                                                if ($debug) {
                                                    Write-Host "New record inserted for accountid $accountId with rating $rating in rating progression table $ratingProgressionTable" -ForegroundColor Green
                                                }
                                            }
                                            catch {
                                                Write-Host "Error inserting record for accountid $accountId in rating progression table" -ForegroundColor Red
                                                Write-Host $_.Exception.Message -ForegroundColor Red
                                            }

                                            $insertRankProgressionCommand = $connection.CreateCommand()
                                            $insertRankProgressionCommand.CommandText = "INSERT INTO $rankProgressionTable ($accountIdColumnName, $rankColumnName, main_id) VALUES ('$accountId', $rank, $($closestRecord['id']))"
                                            try {
                                                $insertRankProgressionCommand.ExecuteNonQuery() | Out-Null
                                                if ($debug) {
                                                    Write-Host "New record inserted for accountid $accountId with rank $rank in rank progression table $rankProgressionTable" -ForegroundColor Green
                                                }
                                            }
                                            catch {
                                                Write-Host "Error inserting record for accountid $accountId in rank progression table" -ForegroundColor Red
                                            }
                                        }
                                    }
                                }
                                else {
                                    Write-Host "No records found for account ID: $accountId" -ForegroundColor Red
                                }
                            }
                            else {
                                #Write-Host "Found 1 record for account ID: $accountId" -ForegroundColor Yellow
                                # Details are:
                                #Write-Host "Account ID: $($records[0]['accountId']), Existing Rank: $($records[0]['rank']), Existing Rating: $($records[0]['rating'])" -ForegroundColor Yellow
                                $existingRank = $reader['rank']
                                $existingRating = $reader['rating']
                                $id = $reader['id']

                                # Check if the existing rank and rating are different from the new values
                                if ($rank -eq $existingRank -and $rating -eq $existingRating) {
                                    # No change, skip updating
                                    if ($debug) {
                                        Write-Host "No changes detected for accountid $accountId" -ForegroundColor Yellow
                                    }
                                } else {
                                    if ([Math]::Abs($existingRating - $rating) -gt $ratingGap) {
                                        Write-Host "Rating difference is too big ($existingRating - $rating) for $accountid with id $id, not inserting to progression tables" -ForegroundColor Red
                                        $reader.Close()  # Close the DataReader before executing another command
                                        # Insert new
                                        $insertCommand = $connection.CreateCommand()
                                        $insertCommand.CommandText = "INSERT INTO $table ($accountIdColumnName, $rankColumnName, $ratingColumnName) VALUES ('$accountId', $rank, $rating)"

                                        try {
                                            $insertCommand.ExecuteNonQuery() | Out-Null
                                            Write-Host "New record inserted for accountid $accountId with rank $rank and rating $rating" -ForegroundColor Green
                                        }
                                        catch {
                                            Write-Host "Error inserting record for accountid $accountId" -ForegroundColor Red
                                            Write-Host $_.Exception.Message -ForegroundColor Red
                                        }
                                    } else {
                                        # Update
                                        $reader.Close()  # Close the DataReader before executing another command

                                        $updateCommand = $connection.CreateCommand()
                                        $updateCommand.CommandText = "UPDATE $table SET $rankColumnName = $rank, $ratingColumnName = $rating WHERE $accountIdColumnName = '$accountId' AND id=$id"
                                        try {
                                            $updateCommand.ExecuteNonQuery() | Out-Null
                                            if ($debug) {
                                                Write-Host "Record updated for accountid $accountId (id is $id) with rank $rank and rating $rating. Was rank $existingRank and rating $existingRating" -ForegroundColor Green
                                            }
                                        }
                                        catch {
                                            Write-Host "Error updating record for accountid $accountId" -ForegroundColor Red
                                            Write-Host $_.Exception.Message -ForegroundColor Red
                                        }
                                        # Update the rank and progression tables
                                            $insertRatingProgressionCommand = $connection.CreateCommand()
                                            $insertRatingProgressionCommand.CommandText = "INSERT INTO $ratingProgressionTable ($accountIdColumnName, $ratingColumnName, main_id) VALUES ('$accountId', $rating, $id)"
                                            try {
                                                $insertRatingProgressionCommand.ExecuteNonQuery() | Out-Null
                                                if ($debug) {
                                                    #Write-Host "New record inserted for accountid $accountId with rating $rating in rating progression table $ratingProgressionTable" -ForegroundColor Green
                                                }
                                            }
                                            catch {
                                                Write-Host "Error inserting record for accountid $accountId in rating progression table" -ForegroundColor Red
                                                Write-Host $_.Exception.Message -ForegroundColor Red
                                            }

                                            $insertRankProgressionCommand = $connection.CreateCommand()
                                            $insertRankProgressionCommand.CommandText = "INSERT INTO $rankProgressionTable ($accountIdColumnName, $rankColumnName, main_id) VALUES ('$accountId', $rank, $id)"
                                            try {
                                                $insertRankProgressionCommand.ExecuteNonQuery() | Out-Null
                                                if ($debug) {
                                                    #Write-Host "New record inserted for accountid $accountId with rank $rank in rank progression table $rankProgressionTable" -ForegroundColor Green
                                                }
                                            }
                                            catch {
                                                Write-Host "Error inserting record for accountid $accountId in rank progression table" -ForegroundColor Red
                                            }
                                    }
                                }
                            }
                        } else {
                            # No existing records found, insert new
                            $reader.Close()  # Close the DataReader before executing another command
                            $insertCommand = $connection.CreateCommand()
                            $insertCommand.CommandText = "INSERT INTO $table ($accountIdColumnName, $rankColumnName, $ratingColumnName) VALUES ('$accountId', $rank, $rating)"

                            try {
                                $insertCommand.ExecuteNonQuery() | Out-Null
                                if ($debug) {
                                    Write-Host "New record inserted for accountid $accountId with rank $rank and rating $rating" -ForegroundColor Green
                                }
                            }
                            catch {
                                Write-Host "Error inserting record for accountid $accountId" -ForegroundColor Red
                                Write-Host $_.Exception.Message -ForegroundColor Red
                            }
                        }

                        $records = @()  # Clear the array

                        # Close the reader
                        $reader.Close()
                        # Dispose of the command object
                        $command.Dispose()
                    }

                    # Print the retrieved records
                    foreach ($record in $records) {
                        Write-Host "Account ID: $($record['accountId']), Existing Rank: $($record['rank']), Existing Rating: $($record['rating'])" -ForegroundColor Yellow
                    }

                    # Dispose of the reader and connection objects
                    # Close the DataReader
                    $reader.Close()
                    $connection.Dispose()


                    if ($startPage -lt $maxPage) {
                        $startPage++
                    }
                    else {
                        Write-Output "No more pages left to crawl because curent page ($startPage) is less than maxPage ($maxPage)."
                        $run++
                        # Let's calculcate the sleep duration based on the number of pages. If there are less than 30 pages, the sleep should be bigger
                        if ($maxPage -lt 30) {
                            $sleepDuration = 120
                        }
                        else {
                            $sleepDuration = 10
                        }
                        Write-Host "sleeping for $sleepDuration seconds..."
                        Start-Sleep -Seconds $sleepDuration
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
        # Now let's finish measuring how long this iteration took
        $end = Get-Date
        $elapsed = $end - $start
        # Let's print in minutes
        Write-Output "This iteration took $($elapsed.Minutes) minutes and $($elapsed.Seconds) seconds."
    }

}
