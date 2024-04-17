<?php

use Components\DisplayLeaderboard;

// Let's find out the type, and region from the path
$parts = explode('/', $_SERVER['REQUEST_URI']);
$type = $parts[1];
$region = $parts[2];

if ($type === "6") {
    $season = 6;
} else {
    $season = 7;
}

echo DisplayLeaderboard::displayLeaderboard($type, $region, $season, $theme);

