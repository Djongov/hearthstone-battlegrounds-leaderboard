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

if ($_POST['api-action'] !== 'prase-phpinfo') {
    Output::error('Invalid action');
}

$phpInfoArray = General::parsePhpInfo();

echo '<div class="ml-4 dark:text-gray-400">';
    echo DataGrid::createTable('php-info', General::assocToIndexed($phpInfoArray[""]), $theme, '', false, false);
echo '</div>';
