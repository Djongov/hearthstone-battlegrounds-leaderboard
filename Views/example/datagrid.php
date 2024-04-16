<?php

use App\General;
use App\Database\MYSQL;
use Components\Html;

echo HTML::h1('DataGrid', true);

echo HTML::p('DataGrid is a special class that can be used to display data in a table format with a filterable and paginated table (datagrid) with just a few lines of code. It is a very powerful tool that can be used to display data from the database, from an API, or a PHP array. Provides a edit/delete buttons and export to csv and tsv.');

// Example 1

echo HTML::h2('Example 1: Displaying data from a MySQL table');

echo HTML::p('This example will display the data from the `users` table in the database. There are switches for Editing or Deleting the data. This is only available for database related DataGrids. The table is filterable and paginated.');

use Components\DataGrid\DataGridDBTable;

echo DataGridDBTable::renderTable('Users', 'users', $theme, true, true);

echo HTML::horizontalLine();

// Example 2

echo HTML::h2('Example 2: Displaying data from a MySQL query');

$query = "SELECT `id`, `theme` FROM `users`";

echo HTML::p('This example will render data from a custom query - ' . $query . '.');

echo DataGridDBTable::renderQuery('Custom query', $query, $theme, true, true, 'users');

echo HTML::horizontalLine();

// Example 3

echo HTML::h2('Example 3: Displaying data from a PHP array');

use Components\DataGrid\DataGrid;

$users = [
    [
        'id' => 1,
        'username' => 'admin',
        'email' => 'example@example.com',
        'role' => 'admin',
        'enabled' => 1
    ],
    [
        'id' => 2,
        'username' => 'user',
        'email' => 'example2@example.com',
        'role' => 'user',
        'enabled' => 1
    ],
];

echo DataGrid::createTable('', $users, $theme, 'User details', false, false);

echo HTML::horizontalLine();

// Example 4

echo HTML::h2('Example 4: Autoloading the DataGrid from Javascript, using the autoloader');

echo HTML::p('This example will autoload the DataGrid from Javascript, using the autoloader. The data is fetched from the database and then passed to the autoloader. Check source code to see how it is done.');

$usersData = MYSQL::query('SELECT * FROM `csp_approved_domains`');

$usersArray = $usersData->fetch_all(MYSQLI_ASSOC);

$autoloadArray = [
    [
        'type' => 'table',
        'parentDiv' => 'dataGridDataLoader',
        //'data' => General::assocToIndexed($_SERVER)
        'data' => $usersArray
    ]
];

foreach ($autoloadArray as $array) {
    echo '<input type="hidden" name="autoload" value="' . htmlspecialchars(json_encode($array)) . '" />';
}

echo '<div id="dataGridDataLoader" class="mx-2 my-12 flex flex-wrap flex-row justify-center items-center"></div>';

echo HTML::horizontalLine();
