<?php

use Components\Forms;
use Components\Html;
use App\Security\Firewall;
use Controllers\Api\Output;
use Components\DataGrid\DataGridDBTable;

// First firewall check
Firewall::activate();

// Admin check
if (!$isAdmin) {
    Output::error('You are not an admin', 403);
}

echo HTML::p('Here you can control the CSP approved domains list. You can add domains to the list. A domain in the list is available to send CSP reports to the CSP reporting endpoint on this app /csp-report - https://' . $_SERVER['HTTP_HOST'] . '/csp-report. If the domain is not in the list, the reporting endpoint will return 401 `Domain not allowed` error.');

// Provide a form that will allow the user to add a new domain to the CSP approved domains list
echo '<div class="p-4 m-4 max-w-md bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700">';
    echo HTML::h2('Add Domain', true);
    $CspApprovedDomainsForm = [
        'inputs' => [
            'input' => [
                [
                    'type' => 'text',
                    'name' => 'domain',
                    'label' => 'Domain',
                    'required' => true,
                    'placeholder' => 'example.com',
                    'description' => 'The domain to add to the CSP approved domains list.'
                ]
            ]
        ],
        'action' => '/api/admin/csp/add',
        'reloadOnSubmit' => true,
        'submitButton' => [
            'size' => 'small',
            'text' => 'Add Domain'
        ],
    ];

    echo Forms::render($CspApprovedDomainsForm, $theme);
echo '</div>';

echo DataGridDBTable::renderTable('CSP Approved Domains', 'csp_approved_domains', $theme);
