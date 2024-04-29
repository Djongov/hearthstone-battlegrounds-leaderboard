<?php

use Components\DataGrid\DataGridDBTable;

// Let's find out the type, and region from the path
$parts = explode('/', $_SERVER['REQUEST_URI']);
$type = $parts[1];
$region = $parts[2];

if ($type === "6") {
    $season = 6;
} else {
    $season = 7;
}

$query = "SELECT `rank`,`accountid`,`rating` FROM `battlegrounds_season_6_$region`";


echo DataGridDBTable::renderQuery('Season 6 ' . $region . ' Leaderboard', $query, $theme, false, false, '', false);
