<?php

use App\Database\MYSQL;
use Controllers\Api\Output;
use App\Logs\SystemLog;
use Controllers\Api\Checks;

$checks = new Checks($vars, $_POST);

$checks->checkParams(['table', 'id'], $_POST);

$checks->apiChecks();

$table = $_POST['table'];

// Because the POST data comes from a fetch request, it serializes the data and everything comes through as a string which could lead to DB query errors. Let's convert the data to the correct types
foreach ($_POST as $key => &$value) {
    if (is_numeric($value)) {
        $value = intval($value);
    // Also, if the value is an empty string, let's convert it to null
    } elseif ($value === '') {
        $value = null;
    }
}

$sql = 'UPDATE `' . $_POST['table'] . '` SET ';

unset($_POST['csrf_token']);
unset($_POST['table']);

MYSQL::checkDBColumnsAndTypes($_POST, $table);

$updates = [];

// Check if all keys in $_POST match the columns
foreach ($_POST as $key => $value) {
    if ($value === '') {
        // Set to NULL or a default value as needed
        $updates[] = "`$key` = NULL"; // or "`$key` = DEFAULT"
    } else {
        $updates[] = "`$key` = ?";
    }
}
// Combine the SET clauses with commas
$sql .= implode(', ', $updates);

// Add a WHERE clause to specify which organization to update
$sql .= " WHERE `id` = ?";

// Prepare and execute the query using queryPrepared
$values = array_values($_POST);

$values[] = $_POST['id']; // Add the 'id' for the WHERE clause

$editRecord = MYSQL::queryPrepared($sql, $values);


if ($editRecord->affected_rows === 0) {
    Output::error('Nothing updated', 409);
} else {
    SystemLog::write('Record id ' . $_POST['id'] . ' edited in ' . $table, 'DataGrid Edit');
    echo Output::success('successfully edited ' . $editRecord->affected_rows . ' records in ' . $table . '');
}
