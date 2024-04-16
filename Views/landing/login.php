<?php

use App\Authentication\AzureAd;
use App\Authentication\JWT;
use Components\Html;
use Components\Forms;
use Components\Alerts;

$destinationUrl = $_GET['destination'] ?? '/';

if (isset($_COOKIE[AUTH_COOKIE_NAME])) {
    $idToken = JWT::parseTokenPayLoad($_COOKIE[AUTH_COOKIE_NAME]);
    // Decide whether it is a local login or AzureAD login
    if ($idToken['iss'] === $_SERVER['HTTP_HOST']) {
        // Check if valid
        if (JWT::checkToken($_COOKIE[AUTH_COOKIE_NAME])) {
            header('Location: ' . $destinationUrl);
        }
    }
    if ($idToken['iss'] === 'https://login.microsoftonline.com/' . AZURE_AD_TENANT_ID . '/v2.0') {
        // Check if valid
        if (AzureAD::check($_COOKIE[AUTH_COOKIE_NAME])) {
            header('Location: ' . $destinationUrl);
        }
    }
}

echo '<div class="flex items-center justify-center mx-4">';
    echo '<div class="flex flex-col w-full max-w-md my-16 px-4 py-6 bg-gray-50 rounded-lg dark:bg-gray-900 sm:px-6 md:px-8 lg:px-10 border border-gray-300 shadow-md">';
        // Now the different external provider login options
        echo HTML::h3('Login with your account', true, ['my-6']);
        // Azure
        if (AZURE_AD_LOGIN) {
            echo '<div class="flex gap-4 item-center">';
                echo '<a class="mb-4 w-full text-black dark:text-slate-400 font-medium text-center border border-gray-200 rounded-md shadow-sm hover:bg-gray-200 hover:dark:text-black" href="' . AZURE_AD_LOGIN_BUTTON_URL . '">';
                    echo '<div class="flex items-center justify-center py-3 px-3 leading-5">';
                        echo '<img height="32" width="32" src="/assets/images/MSFT.png" alt="MS Logo" />';
                            echo '<span class="ml-3">Sign in with Microsoft Work or school</span>';
                    echo '</div>';
                echo '</a>';
            echo '</div>';
        }
        if (MICROSOFT_LIVE_LOGIN) {
            echo '<div class="flex gap-4 item-center">';
                echo '<a class="mb-4 w-full text-black dark:text-slate-400 font-medium text-center border border-gray-200 rounded-md shadow-sm hover:bg-gray-200 hover:dark:text-black" href="' . MS_LIVE_LOGIN_BUTTON_URL . '">';
                    echo '<div class="flex items-center justify-center py-3 px-3 leading-5">';
                        echo '<img height="32" width="32" src="/assets/images/MSFT.png" alt="MS Logo" />';
                            echo '<span class="ml-3">Sign in with Microsoft live account</span>';
                    echo '</div>';
                echo '</a>';
            echo '</div>';
        }
        // Google
        if (GOOGLE_LOGIN) {
            echo '<div class="flex gap-4 item-center">';
                echo '<a class="mb-4 w-full text-black dark:text-slate-400 font-medium text-center border border-gray-200 rounded-md shadow-sm hover:bg-gray-200 hover:dark:text-black" href="' . GOOGLE_LOGIN_BUTTON_URL . '">';
                    echo '<div class="flex items-center justify-center py-3 px-3 leading-5">';
                        echo '<img height="32" width="32" src="/assets/images/google.png" alt="Google Logo" />';
                        echo '<span class="ml-3">Sign in with Google</span>';
                    echo '</div>';
                echo '</a>';
            echo '</div>';
        }
        // Local login
        if (LOCAL_USER_LOGIN) {
            if (AZURE_AD_LOGIN || GOOGLE_LOGIN || MICROSOFT_LIVE_LOGIN) {
                echo HTML::p('or login with your local account', ['text-center', 'text-gray-500', 'mb-4']);
            }
            $localLoginForm = [
                'inputs' => [
                    'input' => [
                        // Email
                        [
                            'label' => 'Username',
                            'type' => 'text',
                            'placeholder' => 'John.Doe@example.com',
                            'name' => 'username',
                            'required' => true,
                            'description' => 'Provide a valid username (email)',
                            'id' => uniqid(),
                        ],
                        // Password
                        [
                            'label' => 'Password',
                            'type' => 'password',
                            'name' => 'password',
                            'required' => true,
                            'description' => 'Provide a valid password',
                            'id' => uniqid(),
                        ]
                    ],
                    'checkbox' => [
                        [
                            'label' => 'Remember me',
                            'name' => 'remember',
                        ]
                    ],
                    'hidden' => [
                        [
                            'name' => 'state',
                            'value' => ($destinationUrl !== '/' && (substr($destinationUrl, 0, 1) !== '/' || !in_array($destinationUrl, ['/login', '/logout']))) ? '/' : $destinationUrl
                        ]
                    ]
                ],
                'theme' => $theme,
                'method' => 'POST',
                'action' => '/auth-verify',
                'id' => 'local-login-form',
                'resultType' => 'text',
                'submitButton' => [
                    'text' => 'Login',
                    'size' => 'medium',
                ],
            ];

            echo Forms::render($localLoginForm);

            echo '<script src="/assets/js/local-login.js?' . time() . '"></script>';
        }
        if (LOCAL_USER_LOGIN) {
            if (MANUAL_REGISTRATION) {
                echo HTML::small('If you do not have an account, please <a class="underline" href="/register">sign up</a>');
            } else {
                echo HTML::small('If you do not have an account, please contact your administrator');
            }
        }
        if (!LOCAL_USER_LOGIN && !AZURE_AD_LOGIN && !GOOGLE_LOGIN && !MICROSOFT_LIVE_LOGIN) {
            echo Alerts::danger('No login methods are enabled. Check config');
}
    echo '</div>';
    echo '</div>';
echo '</div>';
