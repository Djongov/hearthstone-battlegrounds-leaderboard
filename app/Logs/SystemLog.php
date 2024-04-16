<?php

namespace App\Logs;

use App\Authentication\JWT;
use App\Database\MYSQL;

class SystemLog
{
    public static function write($message, $category)
    {
        if (isset($_COOKIE[AUTH_COOKIE_NAME])) {
            $username = JWT::extractUserName($_COOKIE[AUTH_COOKIE_NAME]);
        } else {
            $username = 'unknown';
        }
        // Find out the real client IP
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $clientIp = $_SERVER["HTTP_CF_CONNECTING_IP"];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $clientIp = str_replace(strstr($_SERVER['HTTP_CLIENT_IP'], ':'), '', $_SERVER['HTTP_CLIENT_IP']);
        } else {
            // or just use the normal remote addr
            $clientIp = $_SERVER['REMOTE_ADDR'];
        }
        $fullUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        MYSQL::queryPrepared("INSERT INTO `system_log` (`text`, `client_ip`, `user-agent`, `created_by`, `category`, `uri`, `method`) VALUES (?, ?, ?, ?, ?, ?, ?)", [$message, $clientIp, $_SERVER['HTTP_USER_AGENT'], $username, $category, $fullUrl, $_SERVER['REQUEST_METHOD']]);
    }
}
