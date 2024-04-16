<?php

use Controllers\Api\Output;
use Controllers\Api\Checks;
use App\Database\MYSQL;
use App\Security\Firewall;

Firewall::activate();

$checks = new Checks($vars, $_POST);

// Perform the API checks
$checks->apiAdminChecks();

// Awaiting parameters
$allowedParams = ['domain', 'csrf_token'];

// Check if the required parameters are present
$checks->checkParams($allowedParams, $_POST);

// Check if domain is valid
if (!filter_var($_POST['domain'], FILTER_VALIDATE_DOMAIN)) {
    Output::error('Invalid domain');
}

// Strip the csrf_token from the POST array and check if we have such a column in the firewall table
unset($_POST['csrf_token']);

// Check if the provided IP is already in the firewall table
$checkDomain = MYSQL::queryPrepared('SELECT * FROM `csp_approved_domains` WHERE `domain` = ?', [$_POST['domain']]);
if ($checkDomain->num_rows === 1) {
    Output::error('domain already in DB');
}

// Insert the domain in the DB
$addDomain = MYSQL::queryPrepared('INSERT INTO `csp_approved_domains` (`domain`, `created_by`) VALUES (?,?)', [$_POST['domain'], $vars['usernameArray']['username']]);

// Check if the insert was successful
if ($addDomain->affected_rows === 1) {
    echo Output::success('domain added');
} else {
    Output::error('domain not added');
}
