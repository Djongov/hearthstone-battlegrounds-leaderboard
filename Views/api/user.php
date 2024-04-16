<?php

use Controllers\Api\Output;
use Controllers\Api\Checks;
use Controllers\Api\User;
use App\General;
use App\Authentication\JWT;

// This is the API endpoint controller for the user actions

// POST /api/user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // This endpoint is for creating a new local user. Cloud users are create in /auth-verify
    $checks = new Checks($vars, $_POST);
    $checks->apiChecksNoUser();

    // Create the user
    $user = new User();

    $data = $_POST;

    unset($data['csrf_token']);

    $data['last_ips'] = General::currentIP();

    $data['origin_country'] = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

    $data['role'] = 'user';

    $data['theme'] = COLOR_SCHEME;

    $data['provider'] = 'local';

    $data['enabled'] = 1;

    // Decide whether you want to sleep here or not, to slow down the brute force attacks
    // sleep(2);

    echo $user->create($data, 'local');
}


if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    // Let's catch php input stream
    $data = Checks::jsonBody();

    // Also the router info should bring us the id
    if (!isset($routeInfo[2]['id'])) {
        Output::error('Missing user id', 400);
        exit();
    }

    $userId = $routeInfo[2]['id'];

    $checks = new Checks($vars, $data);
    $checks->apiChecks();

    // Get the user data based on the ID
    $user = new User();

    // Make sure that the user submitting this is the same as the user being updated. The only secure way of doing this is by checking the JWT token. This will prevent user from updating another user's data by changing the `username` paramter's value in the request
    if (isset($data['username']) && JWT::extractUserName($_COOKIE[AUTH_COOKIE_NAME]) !== $data['username']) {
        Output::error('You are not allowed to update this user', 409);
        exit();
    }

    if (isset($data['passwword'], $data['confirm_password'])) {
        if ($data['password'] !== $data['confirm_password']) {
            Output::error('Passwords do not match', 400);
            exit();
        }
    }

    if (isset($data['password'])) {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
    }

    unset($data['confirm_password']);
    unset($data['csrf_token']);

    $user->update($data, $userId);
}
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    // Let's check if the csrf token is passed as a query string in the DELETE request
    if (!isset($_GET['csrf_token'])) {
        Output::error('Missing CSRF Token', 401);
        exit();
    }

    // Also the router info should bring us the id
    if (!isset($routeInfo[2]['id'])) {
        Output::error('Missing user id', 400);
        exit();
    }

    $userId = $routeInfo[2]['id'];

    $checks = new Checks($vars, []);
    $checks->apiChecksDelete($_GET['csrf_token']);

    $user = new User();

    $user->delete($userId);
}
