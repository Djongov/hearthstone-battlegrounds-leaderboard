<?php

use Controllers\Api\Output;
use App\Install;
use Components\Alerts;

// If we need to instrall
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = mysqli_init();
if (defined("MYSQL_SSL") && MYSQL_SSL) {
    mysqli_ssl_set($conn, NULL, NULL, CA_CERT, NULL, NULL);
    try {
        $conn->real_connect('p:' . DB_HOST, DB_USER, DB_PASS, DB_NAME, 3306, MYSQLI_CLIENT_SSL);
        echo Alerts::info('Successfully connected to the database. Nothing to do here.');
    } catch (\mysqli_sql_exception $e) {
        $error = $e->getMessage();
        if (str_contains($error, "Unknown database")) {
            $install = new Install();
            echo $install->start($conn);
        } else {
            Output::error($error, 400);
        }
    }
} else {
    try {
        $conn->real_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        echo Alerts::info('Successfully connected to the database. Nothing to do here.');
    } catch (\mysqli_sql_exception $e) {
        $error = $e->getMessage();
        // Let's check if the Database exists
        if (str_contains($error, "Unknown database")) {
            $install = new Install();
            echo $install->start($conn);
        } else {
            Output::error($error, 400);
        }
    }
}
