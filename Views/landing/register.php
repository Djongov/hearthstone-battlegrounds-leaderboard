<?php

use Components\Forms;
use Components\Html;
use Components\Alerts;
    
if (LOCAL_USER_LOGIN) {

    if (MANUAL_REGISTRATION) {
    // Registration form here
    echo '<div class="flex items-center justify-center mx-4">
        <div class="flex flex-col w-full max-w-md my-16 px-4 py-8 bg-gray-50 rounded-lg dark:bg-gray-900 sm:px-6 md:px-8 lg:px-10 border border-gray-300 shadow-md">';
            $registrationForm = [
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
                            'description' => 'Provide a password',
                            'id' => uniqid(),
                        ],
                        // Password
                        [
                            'label' => 'Confirm Password',
                            'type' => 'password',
                            'name' => 'confirm_password',
                            'required' => true,
                            'description' => 'Confirm your password',
                            'id' => uniqid(),
                        ],
                        // Name
                        [
                            'label' => 'Name',
                            'type' => 'text',
                            'name' => 'name',
                            'required' => true,
                            'description' => 'Provide your name',
                            'id' => uniqid(),
                        ],
                    ]
                ],
                'action' => '/api/user',
                'theme' => $theme, // Optional, defaults to COLOR_SCHEME
                'method' => 'POST', // Optional, defaults to POST
                'redirectOnSubmit' => '/login',
                'submitButton' => [
                    'text' => 'Register',
                    'size' => 'medium',
                    //'style' => '&#10060;'
                ],
            ];
            echo HTML::h2('Register', true);
            echo HTML::small('Register a new local user account.');
            echo Forms::render($registrationForm);
        echo '</div>';
    echo '</div>';
    } else {
        echo Alerts::danger('Server does not permit manual registration');
    }
} else {
    echo Alerts::danger('Server is set to not allow local logins');
}
