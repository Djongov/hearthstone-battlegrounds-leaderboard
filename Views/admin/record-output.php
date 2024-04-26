<?php

use Components\DataGrid\DataGridDBTable;

echo DataGridDBTable::renderTable('Function app outputs', 'record_events', $theme);
