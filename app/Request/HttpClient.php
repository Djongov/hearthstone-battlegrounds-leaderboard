<?php

namespace App\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Controllers\Api\Output;

class HttpClient
{
    public $client;

    public function __construct($url)
    {
        $this->client = new Client([
            'base_uri' => $url,
            'timeout'  => 10,
            'http_errors' => false,
            'verify' => CURL_CERT,
            'debug' => false
        ]);
    }

    public function call($method, $path, $data = [], $bearer_token = null, $sendJson = false, $headers = [])
    {
        $method = strtoupper($method);


        $headers['User-Agent'] = 'dashboardTemplate/1.0';

        if ($method === 'POST' && $sendJson) {
            $headers['Content-Type'] = 'application/json';
        }

        if ($bearer_token !== null) {
            $headers['Authorization'] = 'Bearer ' . $bearer_token;
        }

        if ($method === 'GET' && !empty($data)) {
            $path .= '?' . http_build_query($data);
        }

        if ($method === 'POST' && !$sendJson) {
            $options['form_params'] = $data;
        }

        if ($method === 'PUT' || $method === 'PATCH' && $sendJson) {
            $headers['Content-Type'] = 'application/json';
            $options['json'] = $data;
        }

        if ($method === 'PUT' || $method === 'DELETE' || ($method === 'POST' && $sendJson)) {
            // Encode the data as JSON
            $jsonPayload = json_encode($data);

            // Set the JSON payload as the request body
            $options['body'] = $jsonPayload;
            $headers['Content-Type'] = 'application/json';
        }

        $options = [
            'headers' => $headers,
        ];

        // If path is null, then use the base_uri
        if (empty($path)) {
            $path = '';
        }

        try {
            $response = $this->client->request($method, $path, $options);
            // Let's extract the statusCode
            $statusCode = $response->getStatusCode();
            // Let's extract the reasonPhrase
            $reasonPhrase = $response->getReasonPhrase();
            // Let's extract the headers
            $headers = $response->getHeaders();
            // Let's extract the response
            $response = $response->getBody()->getContents();
            // Let's return the response
            if ($statusCode >= 400) {
                if (empty($response)) {
                    return ['response' => null, 'statusCode' => $statusCode];
                    //return ['response' => 'Empty response from ' . $path . ' (' .$statusCode . ':' . $reasonPhrase . ')', 'statusCode' => $statusCode];
                } else {
                    return ['response' => $response, 'statusCode' => $statusCode];
                }
            } else {
                return $response = json_decode($response, true);
            }
        } catch (ConnectException $e) {
            // Handle the connection exception
            $errorMessage = $e->getMessage();
            Output::error('HttpClient ConnectException: ' . $e->getHandlerContext()['error'], 500);
        } catch (\UnexpectedValueException $e) {
            // Handle UnexpectedValueException here
            $errorMessage = $e->getMessage();
            Output::error('HttpClient UnexpectedValueException: ' . $errorMessage, 400);
        } catch (\Exception $e) {
            // Handle other exceptions
            Output::error('HttpClient Exceptiion: ' . $e->getMessage(), 400);
        }
    }
}
