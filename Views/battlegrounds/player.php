<?php

use App\Database\DB;
use Components\Alerts;
use Components\HTML;

if (!isset($path['season'], $path['type'], $path['region'])) {
    echo Alerts::danger('Invalid request');
    return;
}
if (!isset($_GET['accountid'])) {
    echo Alerts::danger('Invalid request');
    return;
}

$chartsArray = [];

$db = new DB();

$pdo = $db->getConnection();


/* Rating progression */

$ratingTable = 'rating_progression_season_' . $path['season'] . '_' . $path['region'] . '_' . $path['type'];

$queryRating = "SELECT * FROM `$ratingTable` WHERE `accountid` = ?";
$stmtRating = $pdo->prepare($queryRating);

$stmtRating->execute([$_GET['accountid']]);

$ratingData = $stmtRating->fetchAll(\PDO::FETCH_ASSOC);


/* Rank progression */

$rankTable = 'rank_progression_season_' . $path['season'] . '_' . $path['region'] . '_' . $path['type'];

$queryRank = "SELECT * FROM `$rankTable` WHERE `accountid` = ?";

$stmtRank = $pdo->prepare($queryRank);

$stmtRank->execute([$_GET['accountid']]);

$rankData = $stmtRank->fetchAll(\PDO::FETCH_ASSOC);

echo HTML::h3('Data for ' . $_GET['accountid'] . ' for ' . $path['region'] . ' ' . $path['type'] . ' season ' . $path['season'], true);

// Let's show current rank and rating
$currentRank = $rankData ? end($rankData)['rank'] : 'N/A';
echo HTML::h4('Current rank - ' . $currentRank, true);

$currentRating = $ratingData ? end($ratingData)['rating'] : 'N/A';

echo HTML::h4('Current MMR - ' . $currentRating, true);

// Let's get the lowest rank attained and at what timestamp
$lowestRank = 0;
$lowestRankTimestamp = 0;

// Check if $rankData is not empty
if (!empty($rankData)) {
    // Find the minimum rank
    $lowestRank = max(array_column($rankData, 'rank'));

    // Find the corresponding timestamp for the lowest rank
    $lowestRankEntry = array_filter($rankData, function($entry) use ($lowestRank) {
        return $entry['rank'] == $lowestRank;
    });

    // Extract the timestamp if found
    $lowestRankTimestamp = null;
    if (!empty($lowestRankEntry)) {
        $lowestRankTimestamp = reset($lowestRankEntry)['timestamp'];
    }

    // Output the result
    echo HTML::p("Lowest rank: $lowestRank (Timestamp: $lowestRankTimestamp)", ['text-center']);
} else {
    echo Alerts::danger("No rank data available.");
}

// Highest rank attained and at what timestamp

$highestRank = 0;
$highestRankTimestamp = 0;

// Check if $rankData is not empty
if (!empty($rankData)) {
    // Find the maximum rank
    $highestRank = min(array_column($rankData, 'rank'));

    // Find the corresponding timestamp for the highest rank
    $highestRankEntry = array_filter($rankData, function($entry) use ($highestRank) {
        return $entry['rank'] == $highestRank;
    });

    // Extract the timestamp if found
    $highestRankTimestamp = null;
    if (!empty($highestRankEntry)) {
        $highestRankTimestamp = reset($highestRankEntry)['timestamp'];
    }

    // Output the result
    echo HTML::p("Highest rank: $highestRank (Timestamp: $highestRankTimestamp)", ['text-center']);
} else {
    echo Alerts::danger("No rank data available.");
}

$lastGamesNumber = 10;
$lastGamesNumberQuery = $lastGamesNumber + 1;
// Last 5 games
//$last5Games = array_slice($ratingData, -6, 6, true);
$last5GamesQuery = "SELECT accountid, timestamp, rating
FROM (
    SELECT *, LAG(rating) OVER (PARTITION BY accountid ORDER BY timestamp DESC) AS prev_rating
    FROM $ratingTable
    WHERE accountid = ?
) AS subquery
WHERE rating != prev_rating OR prev_rating IS NULL
ORDER BY timestamp DESC
LIMIT $lastGamesNumberQuery;
";

$stmt = $pdo->prepare($last5GamesQuery);

$stmt->execute([$_GET['accountid']]);

$last5Games = $stmt->fetchAll(\PDO::FETCH_ASSOC);

// Let's display a small table with the rating and the difference between the last entry
if ($last5Games) {
    echo HTML::h4('Last ' . $lastGamesNumber . ' games', true);
    echo '<table class="mx-auto my-4 table-auto w-max-sm border boreder-black dark:border-gray-400 text-center bg-gray-100 dark:bg-gray-900">';
        echo '<thead>';
            echo '<tr>';
                echo '<th class="border px-4 py-2">Rating</th>';
                echo '<th class="border px-4 py-2">MMR</th>';
                echo '<th class="border px-4 py-2">Timestamp</th>';
            echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
            $lastRating = null;
            foreach ($last5Games as $row) {
                $rating = $row['rating'];
                $timestamp = $row['timestamp'];
                if ($lastRating === null) {
                    $lastRating = $rating;
                    continue;
                }
                $difference = $lastRating - $rating;

                // Let's color the difference, if negative, red bold, if positive, green bold
                $color = $difference < 0 ? 'text-red-500' : 'text-green-500';
                // Let's try to predict placement
                $placement = 0;
                // Define the mapping between difference ranges and placement values
                $map = [
                    [-200, -80, 8],
                    [-80, -65, 7],
                    [-65, -50, 6],
                    [-50, -20, 5],
                    [-20, 11, 4],
                    [11, 44, 3],
                    [44, 64, 2],
                    [64, PHP_INT_MAX, 1] // PHP_INT_MAX represents infinity
                ];

                $highMMRMap = [
                    [-200, -100, 8],
                    [-100, -80, 7],
                    [-80, -60, 6],
                    [-60, -10, 5],
                    [-10, 3, 4],
                    [3, 31, 3],
                    [31, 55, 2],
                    [55, PHP_INT_MAX, 1] // PHP_INT_MAX represents infinity
                ];

                // Iterate over the map to find the appropriate placement
                $chosenMap = $rating > 13000 ? $highMMRMap : $map;

                foreach ($chosenMap as $item) {
                    $min = $item[0];
                    $max = $item[1];
                    $placementValue = $item[2];

                    if ($difference > $min && $difference <= $max) {
                        $placement = $placementValue;
                        break;
                    }
                }

                $placementString = '';

                if ($placement === 1) {
                    $placementString = '1st';
                } elseif ($placement === 2) {
                    $placementString = '2nd';
                } elseif ($placement === 3) {
                    $placementString = '3rd';
                } elseif ($placement > 3) {
                    $placementString = $placement . 'th';
                }

                echo '<tr>';
                    echo '<td class="border px-4 py-2">' . $rating . '</td>';
                    echo '<td class="border px-4 py-2 ' . $color . '">' . $difference . ' (' . $placementString . '*)</td>';
                    echo '<td class="border px-4 py-2">' . $timestamp . '</td>';
                echo '</tr>';
                
                $lastRating = $rating;
            }
        echo '</tbody>';
    echo '</table>';
    echo HTML::p('* - possible placement, but will not be always accurate', ['text-center', 'my-4']);
    echo HTML::P('The above table will not show games resulting in 0 rating because of the way data is pulled', ['text-center', 'my-4']);
} else {
    echo Alerts::danger('No rating data found');
}

// Last 24 hours performance

echo HTML::h4('Last 24 hours performance', true);

$last24HoursQuery = "SELECT
    SUM(CASE WHEN rating_change > 0 THEN rating_change ELSE 0 END) AS rating_gained,
    SUM(CASE WHEN rating_change < 0 THEN -rating_change ELSE 0 END) AS rating_lost
FROM (
    SELECT
        MAX(rating) - MIN(rating) AS rating_change
    FROM $ratingTable
    WHERE accountid = ?
      AND timestamp >= NOW() - INTERVAL 24 HOUR
    GROUP BY DATE(timestamp) -- Grouping by date instead of game_id
) AS rating_changes;
";

$stmt = $pdo->prepare($last24HoursQuery);

$stmt->execute([$_GET['accountid']]);

$last24Hours = $stmt->fetch(\PDO::FETCH_ASSOC);

$colorLast24Hours = $last24Hours['rating_gained'] < 0 ? 'text-red-500' : 'text-green-500';

if ($last24Hours) {
    echo HTML::p('Rating gained in the last 24 hours: <span class="' . $colorLast24Hours . '">' . $last24Hours['rating_gained'] . '</span>', ['text-center', 'my-4']);
} else {
    echo Alerts::danger('No rating data found');
}

// Overall performance

echo HTML::h4('Overall performance', true);

// Let's prepare the line chart data. timestamp key in ratinData will be the x-axis and rating will be the y-axis
// We will also prepare the data for the line chart
$ratingData = array_column($ratingData, 'rating', 'timestamp');

if (!$ratingData) {
    echo Alerts::danger('No rating data found');
} else {

    $chartRatingAutoloadData = [
        'type' => 'linechart',
        'data' => [
            'parentDiv' => 'doughnut-limits-holder',
            'title' => $_GET['accountid'] . ' MMR progression',
            'width' => 800,
            'height' => 300,
            'labels' => array_keys($ratingData),
            'datasets' => [
                [
                    'label' => $_GET['accountid'],
                    'data' => array_values($ratingData)
                ]
            ]
        ],
    ];
    array_push($chartsArray, $chartRatingAutoloadData);
}

// Let's prepare the line chart data. timestamp key in ratinData will be the x-axis and rating will be the y-axis

$rankData = array_column($rankData, 'rank', 'timestamp');

if (!$rankData) {
    echo Alerts::danger('No rank data found');
} else {

    $chartRankAutoloadData = [
        'type' => 'linechart',
        'data' => [
            'parentDiv' => 'doughnut-limits-holder',
            'title' => $_GET['accountid'] . ' rank progression',
            'width' => 600,
            'height' => 200,
            'labels' => array_keys($rankData),
            'datasets' => [
                [
                    'label' => $_GET['accountid'],
                    'data' => array_values($rankData)
                ]
            ]
        ]
    ];
    array_push($chartsArray, $chartRankAutoloadData);
}


if ($chartsArray) {
    echo '<div id="doughnut-limits-holder" class="my-12 flex flex-wrap flex-row justify-center items-center">';
        // initiate an array that will pass the following data into hidden inputs so Javascript can have access to this data on page load and draw the charts

        // Now go through them and create an input hidden for each
        foreach ($chartsArray as $array) {
            echo '<input type="hidden" name="autoload" value="' . htmlspecialchars(json_encode($array)) . '" />';
        }
    echo '</div>';
}

// Let's center the back button
echo '<div class="w-full md:w-auto flex items-center my-4">';
    echo '<button class="back-button mx-auto py-3 px-5 leading-5 text-white bg-' . $theme . '-500 hover:bg-' . $theme . '-600 font-medium text-center focus:ring-2 focus:ring-' . $theme . '-500 focus:ring-opacity-50 border border-transparent rounded-md shadow-sm">Go Back</button>';
echo '</div>';

