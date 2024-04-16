<?php

namespace App\Authentication;

use App\Database\MYSQL;
use App\Authentication\JWT;

class TokenCache
{
    // Check if there is a token in the cache for that username
    public static function exist(string $username) : bool
    {
        // Let's see if we have this tokem in the cache
        $cached_token = MYSQL::queryPrepared("SELECT * FROM `cache` WHERE `type`='token' AND `unique_property`=?", [$username]);
        return ($cached_token->num_rows > 0) ? true : false;
    }
    public static function get(string $username) : array
    {
        if (self::exist($username)) {
            $cached_token = MYSQL::queryPrepared("SELECT * FROM `cache` WHERE `type`='token' AND `unique_property`=?", [$username]);
            return $cached_token->fetch_assoc();
        }
    }
    public static function save(string $token) : void
    {
        if (!self::exist($token)) {
            $parsedToken = JWT::parseTokenPayLoad($token);
            // Set the expiration to the token's expiration but convert to mysql datetime
            $expiration = date('Y-m-d H:i:s', $parsedToken['exp']);
            $username = $parsedToken['email'] ?? $parsedToken['username'];
            MYSQL::queryPrepared("INSERT INTO `cache` (`value`, `type`,`unique_property`, `expiration`) VALUES (?,'token',?,?)", [$token, $username, $expiration]);
        }
    }
    public static function update(string $token, string $username) : void
    {
        $parsedToken = JWT::parseTokenPayLoad($token);
        // Set the expiration to the token's expiration but convert to mysql datetime
        $expiration = date('Y-m-d H:i:s', $parsedToken['exp']);
        MYSQL::queryPrepared("UPDATE `cache` SET `value`=?, `expiration`=? WHERE `type`='token' AND `unique_property`=?", [$token, $expiration, $username]);
    }
}
