<?php

use Models\Api\Record;
use Controllers\Api\Checks;
use Controllers\Api\Output;

$checks = new Checks($vars, $_GET);

// Let's pick the region and season from the path
$vars = explode('/', $_SERVER['REQUEST_URI']);
$region = $vars[2];
$season = $vars[3];

$checks->checkParams(['accountid'], $_GET);

$table = 'battlegrounds_season_' . $season . '_' . $region;

$record = Record::getRecord($table, $_GET['accountid']);

if (empty($record)) {
    Output::error('Record not found', 404);
}

unset($record[0]['id']);

echo Output::success($record);
