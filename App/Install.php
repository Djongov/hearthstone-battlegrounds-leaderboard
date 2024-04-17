<?php

namespace App;

use Components\Alerts;
use Components\Html;
use Controllers\Api\Output;

class Install
{
    public function start($conn)
    {
        $html = '';
        $html .= HTML::h2('Database does not exist, attempting to create it', true);
        // Connect to MySQL without specifying the database
        if (defined("MYSQL_SSL") && MYSQL_SSL) {
            try {
                mysqli_ssl_set($conn, NULL, NULL, CA_CERT, NULL, NULL);
                $conn->real_connect('p:' . DB_HOST, DB_USER, DB_PASS, null, 3306, MYSQLI_CLIENT_SSL);
            } catch (\mysqli_sql_exception $e) {
                Output::error($e->getMessage(), 400);
            }
        } else {
            try {
                $conn->real_connect(DB_HOST, DB_USER, DB_PASS);
            } catch (\mysqli_sql_exception $e) {
                Output::error($e->getMessage(), 400);
            }
        }

        // Create the database if it doesn't exist
        $conn->query("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");

        // Select the database
        $conn->select_db(DB_NAME);

        // Read and execute queries from the SQL file to create tables
        $migrateFile = dirname($_SERVER['DOCUMENT_ROOT']) . '/.tools/migrate.sql';
        $migrate = file_get_contents($migrateFile);

        try {
            // Execute multiple queries
            $conn->multi_query($migrate);

            // Consume the results of multi_query
            while ($conn->more_results()) {
                $conn->next_result();
                $conn->store_result();
            }

            // Now you can execute additional queries
            $conn->query("INSERT INTO `csp_approved_domains` (`domain`, `created_by`) VALUES ('" . $_SERVER['HTTP_HOST'] . "', 'System')");
            $conn->query("INSERT INTO `firewall` (`ip_cidr`, `created_by`, `comment`) VALUES ('" . General::currentIP() . "/32', 'System', 'Initial Admin IP')");
            // Insert administrator for first time login
            $password = General::randomString(12);
            $conn->query("INSERT INTO `users`(`username`, `password`, `email`, `name`, `last_ips`, `origin_country`, `role`, `last_login`, `theme`, `provider`, `enabled`) VALUES ('admin', '" . password_hash($password, PASSWORD_DEFAULT) . "', 'admin', 'admin', '" . General::currentIP() . "', 'US', 'administrator', NOW(), '" . COLOR_SCHEME . "', 'local', 1)");
            // Print the credentials to the screen
            $html .= Alerts::info('Database "' . DB_NAME . '" and system tables created successfully. Please go to <a class="underline" href="/login">Login</a> page. Use "admin" as username. Do not refresh the page until you have copied the password below.');
            $html .= HTML::p('<span class="c0py">' . $password . '</span>');
        } catch (\mysqli_sql_exception $e) {
            $error = $e->getMessage();
            $html .= Alerts::danger('Error creating tables: ' . $error);
            throw new \Exception("Error creating tables: " . $error);
        }
        return $html;
    }
}
