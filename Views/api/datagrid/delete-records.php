<?php

use App\Database\MYSQL;
use Controllers\Api\Output;
use Controllers\Api\Checks;
use App\Logs\SystemLog;

$checks = new Checks($vars, $_POST);

// Special XOR check for the delete records endpoint
if (!isset($_POST['table'], $_POST['id']) xor isset($_POST['deleteRecords'], $_POST['row'])) {
    Output::error('Incorrect arguments', 400);
}

$checks->apiChecks();

// If a single delete somes from the button
if (isset($_POST['table'], $_POST['id'])) {
    $deleteSingleRecord = MYSQL::queryPrepared("DELETE FROM `" . $_POST['table'] . "` WHERE `id`=?", $_POST['id']);
    if ($deleteSingleRecord->affected_rows === 0) {
        Output::error('No records were deleted', 400);
    }
    if ($deleteSingleRecord->affected_rows > 0) {
        SystemLog::write('Record id ' . $_POST['id'] . ' deleted from ' . $_POST['table'], 'DataGrid Delete');
        echo Output::success('successfully deleted ' . $deleteSingleRecord->affected_rows . ' records');
    }
// Or mass delete from the Delete selected button
} elseif (isset($_POST['deleteRecords'], $_POST['row'])) {
    $ids = '';
    foreach ($_POST['row'] as $index => $id) {
        if ($index === 0) {
            $ids .= '?';
        } else {
            $ids .= ',?';
        }
    }
    $sql = "DELETE FROM `" . $_POST['deleteRecords'] . "` WHERE `id` IN ($ids)";
    
    $deleteAllRecords = MYSQL::queryPrepared($sql, [...$_POST['row']]);

    
    if ($deleteAllRecords->affected_rows === 0) {
        Output::error('No records were deleted', 400);
    }
    if ($deleteAllRecords->affected_rows > 0) {
        SystemLog::write($deleteAllRecords->affected_rows . ' records deleted from ' . $_POST['deleteRecords'], 'DataGrid Delete');
        echo Output::success('successfully deleted ' . $deleteAllRecords->affected_rows . ' records');
    }
} else {
    Output::error('Incorrect arguments', 400);
}
