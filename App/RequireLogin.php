<?php

namespace App;

use App\Database\MYSQL;
use App\Authentication\AzureAD;
use App\Authentication\JWT;
use App\Authentication\Google;
use Controllers\Api\Output;
use App\Logs\SystemLog;

class RequireLogin
{
    public static function check(bool $apiRoute)
    {
        $loginExempt = [
            '/',
            '/duos/us',
            '/duos/eu',
            '/duos/ap',
            '/solo/us',
            '/solo/eu',
            '/solo/ap',
            '/6/eu',
            '/6/us',
            '/6/ap',
            '/api/*'
        ];
        
        $loggedIn = false;

        $isAdmin = false;

        $usernameArray = [];

        $username = null;
        $provider = '';
        // If auth cookie exists
        if (isset($_COOKIE[AUTH_COOKIE_NAME])) {
            // First parse the JWT token
            $tokenPayload = JWT::parseTokenPayLoad($_COOKIE[AUTH_COOKIE_NAME]);

            // If the issuer is $_SERVER['HTTP_HOST'], we are dealing with a local login
            if ($tokenPayload['iss'] === $_SERVER['HTTP_HOST']) {
                // Check if valid
                if (JWT::checkToken($_COOKIE[AUTH_COOKIE_NAME])) {
                    $provider = 'local';
                    $loggedIn = true;
                } else {
                    if ($apiRoute) {
                        Output::error('Authentication failure', 401);
                    }
                    // If checks for JWT token fail - unset cookie and redirect to /login
                    JWT::handleValidationFailure();
                    header('Location: /login?destination=' . $_SERVER['REQUEST_URI']);
                    exit();
                }
            }

            // Now check if the issuer is the AzureAD endpoint
            if (str_starts_with($tokenPayload['iss'], 'https://login.microsoftonline.com/')) {
                // Check
                if (AzureAD::check($_COOKIE[AUTH_COOKIE_NAME])) {
                    $provider = 'azure';
                    $loggedIn = true;
                } else {
                    if ($apiRoute) {
                        Output::error('Authentication failure', 401);
                    }
                    // If checks for JWT token fail - unset cookie and redirect to /login
                    JWT::handleValidationFailure();
                    header('Location: /login?destination=' . $_SERVER['REQUEST_URI']);
                    exit();
                }
            }
            // Now check Google
            if ($tokenPayload['iss'] === 'https://accounts.google.com') {
                if (Google::check($_COOKIE[AUTH_COOKIE_NAME])) {
                    $provider = 'google';
                    $loggedIn = true;
                } else {
                    if ($apiRoute) {
                        Output::error('Authentication failure', 401);
                    }
                    // If checks for JWT token fail - unset cookie and redirect to /login
                    JWT::handleValidationFailure();
                    header('Location: /login');
                    exit();
                }
            }
            // Now check Microsoft Live
            if ($tokenPayload['iss'] === 'https://login.live.com') {
                // No current way of verifying the token so we will just check if it's not expired
                if (JWT::checkExpiration($_COOKIE[AUTH_COOKIE_NAME])) {
                    $provider = 'mslive';
                    $loggedIn = true;
                } else {
                    if ($apiRoute) {
                        Output::error('Authentication failure', 401);
                    }
                    // If checks for JWT token fail - unset cookie and redirect to /login
                    JWT::handleValidationFailure();
                    header('Location: /login?destination=' . $_SERVER['REQUEST_URI']);
                    exit();
                }
            }

        } else {
            // Redirect to /login but preserve the destination if auth_cookie is missing
            /*
                Do not redirect to /login if uri is in the list or exempt urls
                !str_contains($_SERVER['REQUEST_URI'], '/login') is to prevent infinite redirects
            */
            if (!General::matchRequestURI($loginExempt) && !str_contains($_SERVER['REQUEST_URI'], '/login') && !str_contains($_SERVER['REQUEST_URI'], '/auth-verify')) {
                if ($apiRoute) {
                    Output::error('missing token', 401);
                } else {
                    header('Location: /login?destination=' . $_SERVER['REQUEST_URI']);
                    exit();
                }
            }
        }

        $idTokenInfoArray = [];

        if ($loggedIn && isset($_COOKIE[AUTH_COOKIE_NAME]) && $provider === 'azure') {
            // Let's parse the JWT token from the auth cookie and look at the claims
            $authCookieArray = $tokenPayload;
            // We are mapping what the claims are called in the DB (keys) vs in the JWT token (values)
            $expectedClaims = [
                'username' => 'preferred_username',
                'email' => 'email',
                'name' => 'name',
                'last_ip' => 'ipaddr',
                'country' => 'ctry'
            ];
            foreach ($expectedClaims as $dbClaimName => $JWTClaimName) {
                $idTokenInfoArray[$dbClaimName] = isset($authCookieArray[$JWTClaimName]) ? $authCookieArray[$JWTClaimName] : null;
            }
            // Now the special ones
            $idTokenInfoArray["token_expiry"] = isset($authCookieArray['exp']) ? date("Y-m-d H:i:s", substr($authCookieArray['exp'], 0, 10)) : null;
            // If roles is not set, we set it to user, otherwise we set it to the roles array
            $idTokenInfoArray["roles"] = (isset($authCookieArray['roles'])) ? $authCookieArray['roles'] : ['user'];
            // Now we search for the administrator role as role is an array of roles

            $username = ($loggedIn) ? $idTokenInfoArray["username"] : null;
        }

        if ($provider === 'local') {
            $username = $tokenPayload['username'];
            $usernameArray = $tokenPayload;
            $idTokenInfoArray = $tokenPayload;
        }

        // Now Google
        if ($loggedIn && isset($_COOKIE[AUTH_COOKIE_NAME]) && $provider === 'google') {
            $authCookieArray = $tokenPayload;
            $expectedClaims = [
                'username' => 'email',
                'email' => 'email',
                'name' => 'name',
                'last_ip' => 'ipaddr',
                'country' => 'locale',
                'picture' => 'picture'
            ];
            foreach ($expectedClaims as $dbClaimName => $JWTClaimName) {
                $idTokenInfoArray[$dbClaimName] = isset($authCookieArray[$JWTClaimName]) ? $authCookieArray[$JWTClaimName] : null;
            }
            // Now the special ones
            $idTokenInfoArray["token_expiry"] = isset($authCookieArray['exp']) ? date("Y-m-d H:i:s", substr($authCookieArray['exp'], 0, 10)) : null;
            // If roles is not set, we set it to user, otherwise we set it to the roles array
            $idTokenInfoArray["roles"] = (isset($authCookieArray['roles'])) ? $authCookieArray['roles'] : ['user'];
            // Now we search for the administrator role as role is an array of roles

            $username = ($loggedIn) ? $idTokenInfoArray["username"] : null;
        }
        // Microsoft LIVE
        if ($loggedIn && isset($_COOKIE[AUTH_COOKIE_NAME]) && $provider === 'mslive') {
            $authCookieArray = $tokenPayload;
            $expectedClaims = [
                'username' => 'preferred_username',
                'email' => 'email',
                'name' => 'name'
            ];
            $idTokenInfoArray["last_ip"] = General::currentIP();
            foreach ($expectedClaims as $dbClaimName => $JWTClaimName) {
                $idTokenInfoArray[$dbClaimName] = isset($authCookieArray[$JWTClaimName]) ? $authCookieArray[$JWTClaimName] : null;
            }
            // Now the special ones
            $idTokenInfoArray["token_expiry"] = isset($authCookieArray['exp']) ? date("Y-m-d H:i:s", substr($authCookieArray['exp'], 0, 10)) : null;
            // If roles is not set, we set it to user, otherwise we set it to the roles array
            $idTokenInfoArray["roles"] = ['user'];
            // Now we search for the administrator role as role is an array of roles

            $username = ($loggedIn) ? $idTokenInfoArray["username"] : null;
        }

        // If we are logged in and we have an established username, we need to either fetch user data from the DB or create a new user in the DB
        if ($username !== null) {
            $userResult = MYSQL::query("SELECT * FROM `users` WHERE `username` = '$username'");
            if ($userResult->num_rows > 0) {
                $usernameArray = $userResult->fetch_assoc();
                // Let's check if the last_ip is different from what we have in the DB, and update it
                if ($usernameArray['last_ips'] !== $idTokenInfoArray["last_ip"]) {
                    MYSQL::queryPrepared("UPDATE `users` SET `last_ips`=? WHERE `username`=?",[$idTokenInfoArray["last_ip"], $username]);
                }
            } else {
                if (empty($usernameArray)) {
                    JWT::handleValidationFailure();
                    header('Location: /');
                    exit();
                } else {
                    JWT::handleValidationFailure();
                    header('Location: /logout');
                    exit();
                }
            }
            // Now some admin checks
            // If there is a role = administrator in both token and DB, give admin straight away
            foreach ($idTokenInfoArray["roles"] as $role) {
                if ($role === 'administrator' && $usernameArray['role'] === 'administrator') {
                    $isAdmin = true;
                }
            }
            // Also if the DB says admin, add admin
            if ($usernameArray['role'] === 'administrator') {
                $isAdmin = true;
            }
            // And if DB says admin but the JWT token no longer bears the admin role - remove it
            // elseif ($idTokenInfoArray["role"] !== 'administrator' && $usernameArray['role'] === 'administrator') {
            //     MYSQL::queryPrepared("UPDATE `users` SET `role`=NULL WHERE `username`=?", [$usernameArray['username']]);
            //     // Good to alert as well
            // }
        }

        // Kill disabled users early
        if (isset($usernameArray["enabled"]) && $usernameArray["enabled"] === 0) {
            Output::error('Your user has been disabled', 401);
        }

        //$theme = (isset($usernameArray["theme"])) ? $usernameArray["theme"] : COLOR_SCHEME;
        //$theme = $usernameArray['theme'] ?? COLOR_SCHEME;

        // If this gets executed on /login, we need to keep logged in users away from the login page
        if (str_contains($_SERVER['REQUEST_URI'], 'login') && $loggedIn) {
            if (isset($_GET['destination'])) {
                header('Location: ' . $_GET['destination']);
                exit();
            } else {
                header('Location: /');
                exit();
            }
        }

        return [
            'usernameArray' => $usernameArray,
            'loggedIn' => $loggedIn,
            'isAdmin' => $isAdmin,
        ];

    }
}
