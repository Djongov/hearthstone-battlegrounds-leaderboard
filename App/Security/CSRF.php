<?php

namespace App\Security;

class CSRF
{
    public static function generate()
    {
        return bin2hex(random_bytes(35));
    }
    public static function create()
    {
        if (isset($_SESSION['csrf_token'])) {
            return $_SESSION['csrf_token'];
        } else {
            $token = self::generate();
            $_SESSION['csrf_token'] = $token;
            return $token;
        }
    }
    public static function createTag()
    {
        $token = self::create();
        return '<input type="hidden" name="csrf_token" value="' . $token . '" />';
    }
}
