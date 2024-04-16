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

echo HTML::p('Here you can control the firewall. You can add IPs to the firewall, remove them, or view the current list of IPs in the firewall. To put controllers under the firewall, you need to call Firewall::activate(), preferrably at the start of the controller');
// Provide a form that will allow the user to add a new domain to the CSP approved domains list
$firewallAddForm = [
    'inputs' => [
        'input' => [
            [
                'type' => 'text',
                'name' => 'cidr',
                'label' => 'CIDR IP',
                'required' => true,
                'placeholder' => '1.1.1.1/32',
                'description' => 'The CIDR IP to add to the firewall. If you don\'t provide a mask, it will default to /32.'
            ],
            [
                'type' => 'text',
                'name' => 'comment',
                'required' => false,
                'label' => 'Comment',
                'placeholder' => 'VPN Range',
                'description' => 'A comment for this IP (optional)'
            ]
        ]
    ],
    'action' => '/api/firewall',
    'reloadOnSubmit' => true,
    'submitButton' => [
        'size' => 'medium',
        'text' => 'Add'
    ],
];

echo '<div class="p-4 m-4 max-w-md bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700">';
    echo HTML::h2('Add IP', true);
    echo HTML::p('Add an IP to the firewall. Use only CIDR notations');
    echo Forms::render($firewallAddForm, $theme);
echo '</div>';

echo DataGridDBTable::renderTable('Firewall', 'firewall', $theme);
