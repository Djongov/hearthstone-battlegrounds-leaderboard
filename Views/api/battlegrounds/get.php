<?php

use Models\Api\Record;
use Controllers\Api\Checks;
use Controllers\Api\Output;

$checks = new Checks($vars, $_GET);

$checks->checkParams(['accountid'], $_GET);

// Let's pick the region and season from the path
$path = explode('/', $_SERVER['REQUEST_URI']);
// /api/7/solo/eu/get or /api/6/eu/get
$season = (int) $path[2];

if ($season !== 6 && $season !== 7) {
    Output::error('Invalid season', 400);
}


$checks->checkParams(['accountid'], $_GET);

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

$record = Record::getRecord($table, $_GET['accountid']);

if (empty($record)) {
    Output::error('Record not found', 404);
}

unset($record[0]['id']);

echo Output::success($record);
