<?php

use App\Database\DB;
use Components\Alerts;

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

$queryRating = "SELECT * FROM `rating_progression` WHERE `accountid` = ? AND `region` = ? AND `type` = ? AND `season` = ?";

$stmtRating = $pdo->prepare($queryRating);

$stmtRating->execute([$_GET['accountid'], $path['region'], $path['type'], $path['season']]);

$ratingData = $stmtRating->fetchAll(\PDO::FETCH_ASSOC);

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
            'title' => $_GET['accountid'] . ' rating progression',
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

/* Rank progression */

$queryRank = "SELECT * FROM `rank_progression` WHERE `accountid` = ? AND `region` = ? AND `type` = ? AND `season` = ?";

$stmtRank = $pdo->prepare($queryRank);

$stmtRank->execute([$_GET['accountid'], $path['region'], $path['type'], $path['season']]);

$rankData = $stmtRank->fetchAll(\PDO::FETCH_ASSOC);

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

