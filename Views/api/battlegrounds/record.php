<?php

declare(strict_types=1);

namespace Models;

use Controllers\Api\Checks;
use Controllers\Api\Output;
use Google\Service\Transcoder\Progress;
use Models\Api\Record;

$checks = new Checks($vars, $_POST);

$checks->checkSecretHeader();

$checks->checkParams(['rank', 'accountid', 'rating'], $_POST);

// Let's pick the region and season from the path
$path = explode('/', $_SERVER['REQUEST_URI']);
// /api/7/solo/eu/record or /api/6/eu/record
$season = (int) $path[2];

if ($season !== 6 && $season !== 7) {
    Output::error('Invalid season', 400);
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

$rank = (int) $_POST['rank'];
$accountid = $_POST['accountid'];
$rating = (int) $_POST['rating'];

$record = new Record($rank, $accountid, $rating, $table);

// Check if the accountid already exists in the table
if ($record->recordExist()) {
    // Update the rating if the accountid already exists
    echo $record->updateRecord();
    return;
}
// If not, insert a new record
echo $record->createRecord();
