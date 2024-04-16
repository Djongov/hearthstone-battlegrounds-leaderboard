<?php

use App\Authentication\AzureAD;
use Controllers\Api\Output;
use Controllers\Api\Checks;
use App\General;
use Controllers\Api\User;
use Models\Api\User as UserModel;
use App\Exceptions\UserExceptions;
use App\Authentication\JWT;
use App\Authentication\Google;
use App\Logs\SystemLog;
use Google\Client;

// Let's decide whether the connection is over HTTP or HTTPS (later for setting up the cookie)
$secure = (str_contains($_SERVER['HTTP_HOST'], 'localhost') || str_contains($_SERVER['HTTP_HOST'], '[::1]')) ? false : true;
// First decide where the auth request is coming from, Azure, local login
// If the request is a GET it must be coming from Google
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_GET['code'])) {
        Output::error('Invalid request', 400);
    }
    $state = $_GET['state'];
    //$nonce = $_GET['nonce'];
    $code = $_GET['code'];

    if (str_contains($state, 'auth-verify') || str_contains($state, 'logout')) {
        // If the state is /auth-verify or /logout, we need to set the state to /
        $state = '/';
    }
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
    $client->setHttpClient(new \GuzzleHttp\Client(['verify' => CURL_CERT, 'timeout' => 60, 'http_errors' => false]));
    // Exchange the code for an access token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if (isset($token['error']) && !isset($token['access_token'])) {
        Output::error('Google Error: ' . $token['error_description'], 200);
    }

    $accessToken = $client->getAccessToken();

    $idToken = $accessToken['id_token'];

    // We are not going to use ['access_token'] as we are only using this for authentication

    // verify the id token
    if (!Google::verifyIdToken($idToken)) {
        Output::error('Invalid token', 400);
    }

    $idTokenArray = JWT::parseTokenPayLoad($idToken);

    $user = new User();

    $userModel = new UserModel();

    if ($userModel->exists($idTokenArray['email'])) {
        $userDetailsArray = $userModel->get($idTokenArray['email']);
        if ($userDetailsArray['provider'] !== 'google') {
            Output::error('User exists but is not a google account', 400);
        }
        // User exists, let's update the last login
        $user->updateLastLogin($idTokenArray['email']);
    } else {
        // User does not exist, let's create it (this will also update the last login)
        $user->create($idTokenArray, 'google');
    }

    setcookie(AUTH_COOKIE_NAME, $idToken, [
        'expires' => $idTokenArray['exp'] + 86400,
        'path' => '/',
        'domain' => str_replace(strstr($_SERVER['HTTP_HOST'], ':'), '', $_SERVER['HTTP_HOST']), // strip : from HOST in cases where localhost:8080 is used
        'secure' => $secure, // This needs to be true for most scenarios, we leave the option to be false for local environments
        'httponly' =>  true, // Prevent JavaScript from accessing the cookie
        'samesite' => 'Lax' // This unlike the session cookie can be Lax
    ]);

    $state = '/';

    if (str_contains($state, 'login') || str_contains($state, 'auth-verify') || str_contains($state, 'logout')) {
        // Invalid destination or state, set a default state
        $state = '/';
    }

    header("Location: " . filter_var($state, FILTER_SANITIZE_URL));
    exit();

    /* Instead of using the oauth client, we will get the data from the token */
    /*
    $client->setAccessToken($token['access_token']);

    $oauth2 = new \Google\Service\Oauth2($client);

    $userInfo = $oauth2->userinfo->get();

    $name = $userInfo->name;

    $profileImage = $userInfo->picture;

    $email = $userInfo->email;

    $country = $userInfo->locale;

    $verifiedEmail = $userInfo->verified_email;
    

    $accessToken = $client->getAccessToken();
    $user = new User();

    if ($user->existByUsername($email)) {
        // User exists, let's update the last login
        $user->recordLastLogin($email);
    } else {
        // User does not exist, let's create it (this will also update the last login)
        $user->createGoogleUser();
    }
    */
}
// If the request is coming from Azure, we should have a $_POST['id_token'] and a $_POST['state'] variable
if (isset($_POST['id_token'], $_POST['state']) || isset($_POST['error'], $_POST['error_description'])) {
    // if error - throw it as an exception
    if (isset($_POST['error'], $_POST['error_description'])) {
        Output::error("Azure Error: " . $_POST['error'] . " with Description: " . $_POST['error_description'], 200);
    }

    // If someone comes directly from /login, we need to set the state to /
    if ($_POST['state'] === '/login' || $_POST['state'] === '/logout') {
        $_POST['state'] = '/';
    }
    $idToken = $_POST['id_token'];
    $idTokenArray = JWT::parseTokenPayLoad($idToken);
    // Let's do some checks on the token to handle data structure we expect


    if (!isset($idTokenArray['preferred_username'], $idTokenArray['name'], $idTokenArray['exp'], $idTokenArray['iss'])) {
        Output::error('Invalid token claims', 400);
    }
    
    // If it is an MSLIVE token, then the issueer wil; be https://login.live.com
    if ($idTokenArray['iss'] === 'https://login.live.com') {
        // No check for now
    } else {
        // Let's call the function to check the JWT token which is returned. We are checking stuff like expiration, issuer, app id. We also do validation of the token signature
        if (!AzureAD::check($idToken)) {
            Output::error('Invalid token', 400);
        }
    }

    // Let's set the "auth_cookie" and put the id token as it's value, set the expiration date to when the token should expire and the rest of the cookie settings
    setcookie(AUTH_COOKIE_NAME, $idToken, [
        'expires' => $idTokenArray['exp'] + 86400,
        'path' => '/',
        'domain' => str_replace(strstr($_SERVER['HTTP_HOST'], ':'), '', $_SERVER['HTTP_HOST']), // strip : from HOST in cases where localhost:8080 is used
        'secure' => $secure, // This needs to be true for most scenarios, we leave the option to be false for local environments
        'httponly' =>  true, // Prevent JavaScript from accessing the cookie
        'samesite' => 'Lax' // This unlike the session cookie can be Lax
    ]);
    // instantiate the user class
    $user = new User();
    $userModel = new UserModel();
    // Check if the user exists in the DB
    if ($userModel->exists($idTokenArray['preferred_username'])) {
        // User exists, let's update the last login
        $userDetailsArray = $userModel->get($idTokenArray['preferred_username']);
        if ($userDetailsArray['provider'] !== 'azure' && $userDetailsArray['provider'] !== 'mslive') {
            Output::error('User exists but is not an Entra ID or MS Live account', 400);
        }
        $user->updateLastLogin($idTokenArray['preferred_username']);
    } else {
        // User does not exist, let's create it (this will also update the last login)
        if ($idTokenArray['iss'] === 'https://login.live.com') {
            $user->create($idTokenArray, 'mslive');
        } else {
            $user->create($idTokenArray, 'azure');
        }
    }

    $destinationUrl = $_POST['state'] ?? null;
    // Valid destination, proceed to redirect to the destination
    header("Location: " . filter_var($destinationUrl, FILTER_SANITIZE_URL));
    exit();
}

// If the request is coming from local login, we should have a $_POST['username'] and a $_POST['password'] parameter
if (isset($_POST['username'], $_POST['password'], $_POST['csrf_token'])) {
    // First check the CSRF token
    $checks = new Checks($vars, $_POST);

    $checks->checkCSRF($_POST['csrf_token']);

    // You can implement a sleep here, to slow down the response to and therefore slow down potential spam on the login form
    sleep(0);

    $user = new User();

    try {
        $userArray = $user->get($_POST['username']);
    } catch (UserExceptions $e) {
        Output::error($e->getMessage());
    } catch (\Exception $e) {
        SystemLog::write('Generic error when trying to get local user ' . $_POST['username'] . ' with error: ' . $e->getMessage(), 'User API');
        Output::error('error', 400);
    }
    
    if (empty($userArray)) {
        Output::error('Invalid username or password', 404); // Do not say if the user exists or not to reduce the risk of enumeration attacks
    }

    if ($userArray['enabled'] === '0') {
        Output::error('User is disabled', 401);
    }

    if (!password_verify($_POST['password'], $userArray['password'])) {
        Output::error('Invalid username or password', 404);
    }

    // By now we assume the user is valid, so let's generate a JWT token
    
    $idToken = JWT::generateToken([
        'iss' => $_SERVER['HTTP_HOST'],
        'username' => $userArray['username'],
        'name' => $userArray['name'],
        'roles' => [
            $userArray['role'],
        ],
        'last_ip' => General::currentIP()
    ]);

    $expiry_addition = ($_POST['remember'] === "1") ? 86400 * 24 * 12 : 86400;

    // Let's set the auth cookie
    setcookie(AUTH_COOKIE_NAME, $idToken, [
        'expires' => time() + $expiry_addition,
        'path' => '/',
        'domain' => str_replace(strstr($_SERVER['HTTP_HOST'], ':'), '', $_SERVER['HTTP_HOST']), // strip : from HOST in cases where localhost:8080 is used
        'secure' => true, // This needs to be true for most scenarios, we leave the option to be false for local environments
        'httponly' =>  true, // Prevent JavaScript from accessing the cookie
        'samesite' => 'Lax' // This needs to be None otherwise, the trip to ms login endpoint and back will not hold the cookie
    ]);
    // Record last login
    $user->updateLastLogin($userArray['username']);

    $destinationUrl = $_POST['state'] ?? null;
    if ($destinationUrl !== null && (substr($destinationUrl, 0, 1) === '/')) {
        // Invalid destination or state, set a default state
        $destinationUrl = '/';
    }
    // Valid destination, proceed with your script
    header("Location: " . filter_var($destinationUrl, FILTER_SANITIZE_URL));
    exit();
}


