<?php

use Google\Client;

define("GOOGLE_CLIENT_ID", $_ENV['GOOGLE_CLIENT_ID']);
define("GOOGLE_CLIENT_SECRET", $_ENV['GOOGLE_CLIENT_SECRET']);
$google_nonce = $_SESSION['nonce'] ?? null;
$client = new Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri('https://' . $_SERVER['HTTP_HOST'] . '/auth-verify');
$client->addScope("email");
$client->addScope("profile");
$client->addScope("openid");
$client->setPrompt('select_account consent');
$client->setAccessType('offline');
// Set the state too
$client->setState($destination);
// Set nonce
$client->setLoginHint($google_nonce);
define("GOOGLE_LOGIN_BUTTON_URL", $client->createAuthUrl());
define("GOOGLE_LOGOUT_BUTTON_URL", 'https://accounts.google.com/logout');
define("GOOGLE_REFRESH_TOKEN_URL", 'https://accounts.google.com/o/oauth2/auth?response_type=code&client_id=' . GOOGLE_CLIENT_ID . '&redirect_uri=' . $_SERVER['HTTP_HOST'] . '/auth-verify&scope=openid+profile+email&state=redir=https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
