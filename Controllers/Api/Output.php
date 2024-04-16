<?php

namespace Controllers\Api;

class Output
{
    // This method will format the output of the API to JSON and structure the data. It will be used by everything that this API outputs
    public static function success(mixed $data, $status_code = 200): string
    {
        // Send the response
        header('Content-Type: application/json');
        http_response_code($status_code);
        if ($status_code === 204) {
            // Do noting
        }
        return json_encode(
            [
                'result' => 'success',
                'timestampUTC' => gmdate("Y-m-d H:i:s"),
                'serverResponseTimeMs' => self::responseTime(),
                'data' => $data
            ],
            JSON_PRETTY_PRINT
        );
    }
    // This method terminates the script after sending the response
    public static function successDie(mixed $data, $status_code = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($status_code);
        if ($status_code === 204) {
            // Do noting
        }
        echo json_encode(
            [
                'result' => 'success',
                'timestampUTC' => gmdate("Y-m-d H:i:s"),
                'serverResponseTimeMs' => self::responseTime(),
                'data' => $data
            ],
            JSON_PRETTY_PRINT
        );
        exit();
    }
    // This method terminates the script and sends an error response
    public static function error(mixed $data, $status_code = 404): string
    {
        header('Content-Type: application/json');
        http_response_code($status_code);
        echo json_encode(
            [
                'result' => 'error',
                'timestampUTC' => gmdate("Y-m-d H:i:s"),
                'serverResponseTimeMs' => self::responseTime(),
                'data' => $data
            ],
            JSON_PRETTY_PRINT
        );
        exit();
    }
    // Calculate the response time, START_TIME is defined in the public index.php file
    public static function responseTime()
    {
        return round((microtime(true) - START_TIME) * 1000);
    }
}
