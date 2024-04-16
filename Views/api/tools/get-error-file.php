<?php

use Components\Html;
use Controllers\Api\Output;
use Controllers\Api\Checks;
use App\General;
use App\Security\Firewall;
use Components\DataGrid\DataGrid;

Firewall::activate();

$checks = new Checks($vars, $_POST);

// Perform the API checks
$checks->apiAdminChecks();

// Awaiting parameters
$allowedParams = ['api-action', 'csrf_token'];

// Check if the required parameters are present
$checks->checkParams($allowedParams, $_POST);

if ($_POST['api-action'] !== 'get-error-file') {
    Output::error('Invalid action');
}

echo '<div class="ml-4 dark:text-gray-400">';

$file = ini_get('error_log');
if (is_file($file)) {
    if (is_readable($file)) {
        if (empty(file($file))) {
            echo HTML::p('File (' . $file . ') is empty');
            return;
        }
        echo HTML::h3(realpath($file));
        $errorFileArray = [];
        $f = file($file);
        $f = implode(PHP_EOL, $f);
        $f = explode(PHP_EOL . '[', $f);
        foreach ($f as $line) {
            if ($line === "") {
                continue;
            }
            // </div>';
            array_push($errorFileArray, $line);
        }
        $errorFileArray = General::assocToIndexed($errorFileArray);
        echo DataGrid::createTable('error-file', $errorFileArray, $theme, 'Error file', false, false);
    } else {
        echo '<p class="red">File (' . $file . ') not readable</p>';
    }
} else {
    echo '<p class="red">File (' . $file . ') does not exist</p>';
}
echo '</div>';
