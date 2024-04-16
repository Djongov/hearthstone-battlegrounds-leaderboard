<?php

use App\Security\Firewall;
use Controllers\Api\Output;
use Components\DataGrid\DataGridDBTable;

// First firewall check
Firewall::activate();

// Admin check
if (!$isAdmin) {
    Output::error('You are not an admin', 403);
}

echo DataGridDBTable::renderTable('System Logs', 'system_log', $theme);
