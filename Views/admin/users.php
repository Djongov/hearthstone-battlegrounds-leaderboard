<?php

use App\Security\Firewall;
use Controllers\Api\Output;
use App\Database\MYSQL;
use Components\DataGrid\DataGridDBTable;

// First firewall check
Firewall::activate();

// Admin check
if (!$isAdmin) {
    Output::error('You are not an admin', 403);
}

echo DataGridDBTable::renderTable('Users', 'users', $theme);
