<?php

namespace Components\DataGrid;

use Components\DataGrid\DataGrid;
use App\Database\MYSQL;
use Components\Alerts;

class DataGridDBTable extends DataGrid
{
    public static function renderTable(string $title, string $table, string $theme, bool $edit = true, bool $delete = true, array $skipColumns = [])
    {
        // First the SELECT query
        if (empty($skipColumns)) {
            $queryResult = MYSQL::query("SELECT * FROM $table ORDER by `id` DESC");
        } else {
            $queryArray = [
                "SET @sql = CONCAT('SELECT ', (SELECT GROUP_CONCAT(COLUMN_NAME) FROM information_schema.columns WHERE table_schema = '" . DB_NAME . "' AND table_name = '$table' AND COLUMN_NAME NOT IN (" . implode(', ', array_map('self::add_quotes', $skipColumns)) . ")), ' from `$table` ORDER by `id` DESC');",
                "PREPARE stmt1 FROM @sql;",
                "EXECUTE stmt1;"
            ];
            $queryResult = MYSQL::multiQuery($queryArray);
        }
        // Save the result as array in $data to be used for export later
        $data = $queryResult->fetch_all(MYSQLI_ASSOC);
        if (count($data) === 0) {
            return Alerts::danger('No results for ' . $table);
        }
        // Create the table
        return self::createTable($table, $data, $theme, $title, $edit, $delete);
    }
    public static function renderQuery(string $title, string $query, string $theme, bool $edit = true, bool $delete = true, $dbName = '')
    {
        $dataResult = MYSQL::query($query);

        if ($dataResult->num_rows === 0) {
            return Alerts::danger('No results for ' . $dbName);
        }

        $data = $dataResult->fetch_all(MYSQLI_ASSOC);
        
        if ($edit || $delete) {
            // If edit or delete is true, we need to have an `id` column and we need it to be the first one
            if (!isset($data[0]['id'])) {
                return Alerts::danger('No `id` column found in the query result. If you are using edit or delete, you need to have an `id` column in your query result');
            }
            if (array_key_first($data[0]) !== 'id') {
                return Alerts::danger('The `id` column needs to be the first column in the query result');
            }
            if ($dbName === '' || $dbName === null) {
                return Alerts::danger('You need to pass a database name if you want to use edit or delete');
            }
        }

        // Create the table
        return self::createTable($dbName, $data, $theme, $title, $edit, $delete);
    }
    private static function add_quotes($str)
    {
        return sprintf("'%s'", $str);
    }
}
