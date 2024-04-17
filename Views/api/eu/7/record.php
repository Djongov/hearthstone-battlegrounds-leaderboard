<?php

declare(strict_types=1);

namespace Models;

use Controllers\Api\Checks;
use Models\Api\Record;

$checks = new Checks($vars, $_POST);

$checks->checkSecretHeader();

$checks->checkParams(['rank', 'accountid', 'rating'], $_POST);

$record = new Record($_POST["rank"], $_POST["accountid"], $_POST["rating"], "battlegrounds_season_7_eu");

// Check if the accountid already exists in the table
if ($record->recordExist()) {
    // Update the rating if the accountid already exists
    echo $record->updateRecord();
    return;
}
// If not, insert a new record
echo $record->createRecord();
