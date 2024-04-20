<?php

use Models\Api\Record;
use Controllers\Api\Checks;
use Controllers\Api\Output;

$checks = new Checks($vars, $_GET);

// Let's pick the region and season from the path
$path = explode('/', $_SERVER['REQUEST_URI']);
// /api/7/solo/eu/get or /api/6/eu/get
$season = (int) $path[2];

if ($season !== 6 && $season !== 7) {
    Output::error('Invalid season', 400);
}

// Only allow GET accountid and rank
if (count($_GET) > 2) {
    Output::error('Invalid parameters', 400);
}

// Check if either 'accountid' or 'rank' parameter is present in the GET request
if ((!isset($_GET['accountid']) && !isset($_GET['rank'])) || (isset($_GET['accountid']) && isset($_GET['rank']))) {
    // If both parameters are present or neither parameter is present
    Output::error("Please provide only one of 'accountid' or 'rank' in the GET request.", 400);
}
// Check if 'accountid' parameter is present in the GET request
elseif(isset($_GET['accountid'])) {
    $accountId = $_GET['accountid'];
}
// Check if 'rank' parameter is present in the GET request
else {
    $rank = $_GET['rank'];
    if (!is_numeric($rank)) {
        Output::error('Invalid rank, must be integer', 400);
    }
}

if ($season === 6) {
    $region = $path[3];
    $table = 'battlegrounds_season_' . $season . '_' . $region;
} else {
    $region = $path[4];
    $type = $path[3];
    $table = 'battlegrounds_season_' . $season . '_' . $region . '_' . $type;
}

$allowedRegion = ['eu', 'us', 'ap'];

foreach ($allowedRegion as $reg) {
    if (!in_array($region, $allowedRegion)) {
        Output::error('Invalid region', 400);
    }
}

if (isset($_GET['accountid'])) {
    $record = Record::getRecordByAccountId($table, $accountId);
} else {
    $record = Record::getRecordByRank($table, $rank);
}

if (empty($record)) {
    Output::error('Record not found', 404);
}

unset($record[0]['id']);

echo Output::success($record);
