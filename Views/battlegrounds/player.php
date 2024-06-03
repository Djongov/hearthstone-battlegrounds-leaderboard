<?php

use Components\Alerts;
use Components\DisplayPlayerData;

if (!isset($path['season'], $path['type'], $path['region'])) {
    echo Alerts::danger('Invalid request');
    return;
}
if (!isset($_GET['accountid'])) {
    echo Alerts::danger('Invalid request');
    return;
}

$season = $path['season'];
$type = $path['type'];
$region = $path['region'];
$accountid = $_GET['accountid'];

$accountInternalIdArray = Models\Api\Record::getRecordByAccountId('battlegrounds_season_' . $season . '_' . $region . '_' . $type, $accountid);

if (!$accountInternalIdArray) {
    echo Alerts::danger('No data found');
    return;
}

if (count($accountInternalIdArray) > 1) {
    echo Alerts::danger('Unfortunately, Blizzard allow for multiple accounts with the same name. This makes for weird distortions of data.');
    
    // Let's find out the Ids
    $accountIdsArray = [];
    foreach ($accountInternalIdArray as $userArray) {
        array_push($accountIdsArray, $userArray['id']);
    }
    // Now remove the duplicates
    $accountIdsArray = array_unique($accountIdsArray);
    foreach ($accountIdsArray as $id) {
        $user = new DisplayPlayerData($accountInternalIdArray[1]['accountid'], $region, $type, $season, $id);
        echo $user->display();
        echo Components\HTML::horizontalLine();
    }
} else {
    $user = new DisplayPlayerData($accountInternalIdArray[0]['accountid'], $region, $type, $season);
    echo $user->display();
}
