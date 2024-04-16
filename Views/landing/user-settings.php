<?php

use App\General;
use Components\Forms;
use Components\Html;
use App\Authentication\JWT;
use Models\Api\User;
use App\Exceptions\User as UserExceptions;

$user = new User();

/* Profile picture update logic */
$token = JWT::parseTokenPayLoad($_COOKIE[AUTH_COOKIE_NAME]);
// This is mostly Google
if (!empty($usernameArray['picture']) && isset($token['picture'])) {
    $picture = $usernameArray['picture'];
    // Checkl the picture from the JWT token, it might be updated
    $token = JWT::parseTokenPayLoad($_COOKIE[AUTH_COOKIE_NAME]);
    if ($picture !== $token['picture']) {
        $picture = $token['picture'];
        // Save the picture to the user
        try {
            $user->update(['picture' => $picture], $usernameArray['id']);
        } catch (UserExceptions $e) {
            // Handle user-specific exceptions
            echo $e->getMessage();
        } catch (\Exception $e) {
            // Handle other exceptions
            echo $e->getMessage();
        }
    }
} elseif ($usernameArray['picture'] === null) {
    // If no picture is set, use the ui-avatars.com service to generate a picture
    $picture = 'https://ui-avatars.com/api/?name=' . $usernameArray['name'] . '&background=0D8ABC&color=fff';
    // Save the picture to the user
    try {
        $user->update(['picture' => $picture], $usernameArray['id']);
    } catch (UserExceptions $e) {
        // Handle user-specific exceptions
        echo $e->getMessage();
    } catch (\Exception $e) {
        // Handle other exceptions
        echo $e->getMessage();
    }
}

$allowed_themes = ['amber', 'green', 'stone', 'rose', 'lime', 'teal', 'sky', 'purple', 'red', 'fuchsia', 'indigo'];

$locale = (isset($usernameArray['origin_country'])) ? General::country_code_to_locale($usernameArray['origin_country']) : null;
$fmt = new IntlDateFormatter($locale, IntlDateFormatter::LONG, IntlDateFormatter::GREGORIAN);
echo '<div class="flex flex-row flex-wrap items-center mb-4 justify-center">';
    echo '<div class="p-4 m-4 max-w-lg bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700">';
        echo Html::h2('User settings');
        echo '<table class="w-auto">';
        foreach ($usernameArray as $name => $setting) {
            echo '<tr>';
            if ($name === 'id' || $name === 'password') {
                continue;
            }
            // Check if date
            if ($setting !== null && strtotime($setting)) {
                echo ' <td class="w-full"><strong>' . $name . '</strong> : ' . $fmt->format(strtotime($setting)) . '  </td>';
                continue;
            }
            if ($name === 'theme') {
                echo '<td class="w-full"><div class="flex my-2 flex-row"><strong>' . $name . '</strong> : ';
                $themeOptions = [];
                $currentTheme = '';
                foreach ($allowed_themes as $color) {
                    $themeOptions[$color] = $color;
                    if ($theme === $color) {
                        $currentTheme = $color;
                    }
                }
                $updateThemeOptioms = [
                    'inputs' => [
                        'select' => [
                            'select' => [
                                'label' => '',
                                'name' => 'theme',
                                'options' => $themeOptions,
                                'selected_option' => $currentTheme,
                                'searchable' => true
                            ]
                        ],
                        'hidden' => [
                            [
                                'name' => 'username',
                                'value' => $usernameArray['username']
                            ]
                        ],

                    ],
                    'theme' => $theme, // Optional, defaults to COLOR_SCHEME
                    'method' => 'PUT',
                    'action' => '/api/user/' . $usernameArray['id'],
                    'reloadOnSubmit' => true,
                    'submitButton' => [
                        'text' => 'Update',
                        'size' => 'medium',
                        //'style' => '&#10060;'
                    ],
                ];
                echo Forms::render($updateThemeOptioms);
                echo '</div></td>';
                continue;
            }
            // Only show copy to clipboard on non-null items
            echo ' <td class="w-full"><strong>' . $name . '</strong> : <span class="break-all">' . $setting . '</span>  </td>';
            echo '</tr>';
        }
        echo '</table>';
        echo '</div>';
        echo '<div class="p-4 m-4 max-w-lg bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700">';
            echo Html::h2('Session Info');
            echo '<p><strong>Token expiry: </strong>' . $fmt->format(strtotime(date("Y-m-d H:i:s", substr(JWT::parseTokenPayLoad($_COOKIE[AUTH_COOKIE_NAME])['exp'], 0, 10)))) . '</p>';
            echo '<p><strong>Token: </strong></p><p class="break-all c0py">' . $_COOKIE[AUTH_COOKIE_NAME] . '</p>';
            $token = JWT::parseTokenPayLoad($_COOKIE[AUTH_COOKIE_NAME]);
            // echo '<ul>';
            //     foreach ($token as $key => $value) {
            //         if (!is_array($value)) {
            //             echo '<li><strong>' . $key . '</strong> : ' . $value . '</li>';
            //         } else {
            //             echo '<li><strong>' . $key . '</strong> : ';
            //             echo '<ul>';
            //             foreach ($value as $subkey => $subvalue) {
            //                 echo '<li><strong>' . $subkey . '</strong> : ' . $subvalue . '</li>';
            //             }
            //             echo '</ul>';
            //             echo '</li>';
            //         }
            //     }
            // echo '</ul>';
    echo '</div>';

    if (empty($usernameArray['email']) || filter_var($usernameArray['email'], FILTER_VALIDATE_EMAIL) === false) {
        echo '<div class="p-4 m-4 max-w-lg bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700">';
        echo '<div class="flex flex-row flex-wrap items-center mb-4">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="inline-block w-6 h-6 fill-amber-500">
                    <title>Missing Email</title>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>';
        echo Html::h2('Missing Email');
        echo '</div>';
        echo HTML::p('We noticed that we haven\'t got your email address from your token claims. We will try to email you on your username which is <strong>' . $usernameArray['username'] . '</strong> but in case you think you can\'t be receiving emails on your username address, here you can give an aleternative. Don\'t worry, we will only be sending important notifications. Signups for newsletters and others are separate');

        $updateEmailFormOptions = [
            'inputs' => [
                'input' => [
                    [
                        'label' => 'Email Address',
                        'type' => 'email',
                        'placeholder' => '',
                        'name' => 'email',
                        'description' => 'provide a valid email',
                        'disabled' => false,
                        'required' => true,
                    ]
                ],
                'hidden' => [
                    [
                        'name' => 'username',
                        'value' => $usernameArray['username']
                    ]
                ],
            ],
            'theme' => $theme,
            'method' => 'PUT',
            'action' => '/api/user/' . $usernameArray['id'],
            'reloadOnSubmit' => true,
            'submitButton' => [
                'text' => 'Update',
                'size' => 'medium',
                //'style' => '&#10060;'
            ],
        ];

        echo Forms::render($updateEmailFormOptions);

        echo '</div>';
    }
    // Change password for local users
    if ($usernameArray['provider'] === 'local') {
        echo '<div class="p-4 m-4 max-w-md bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700">';
            echo Html::h2('Change Password');
            $changePasswordForm = [
                'inputs' => [
                    'input' => [
                        [
                            'label' => 'New Password',
                            'type' => 'password',
                            'placeholder' => '',
                            'name' => 'password',
                            'description' => 'You new password',
                            'disabled' => false,
                            'required' => true,
                        ],
                        [
                            'label' => 'Confirm Password',
                            'type' => 'password',
                            'placeholder' => '',
                            'name' => 'confirm_password',
                            'description' => 'Confirm your new password',
                            'disabled' => false,
                            'required' => true,
                        ]
                    ],
                    'hidden' => [
                        [
                            'name' => 'username',
                            'value' => $usernameArray['username']
                        ]
                    ],
                ],
                'theme' => $theme,
                'method' => 'PUT',
                'action' => '/api/user/' . $usernameArray['id'],
                'redirectOnSubmit' => '/logout',
                'submitButton' => [
                    'text' => 'Change Password',
                    'size' => 'medium',
                    //'style' => '&#10060;'
                ],
            ];
            echo Forms::render($changePasswordForm);
            echo HTML::small('Successfully changing the password will log you out of the app. You will need to login again with your new password.');
        echo '</div>';
    }
    echo '<div class="p-4 m-4 max-w-fit bg-white rounded-lg border border-gray-200 shadow-md dark:bg-gray-900 dark:border-gray-700">';
        echo Html::h2('Forget About me');
        echo Html::p('This will delete your account in our database along with any data we have about your account.');
        $deleteUserFormOptions = [
            'inputs' => [
                'hidden' => [
                    [
                        'name' => 'username',
                        'value' => $usernameArray['username']
                    ]
                ],
            ],
            'theme' => 'red',
            'method' => 'DELETE',
            'action' => '/api/user/' . $usernameArray['id'] . '?csrf_token=' . $_SESSION['csrf_token'],
            'redirectOnSubmit' => '/logout',
            'confirm' => true,
            'confirmText' => 'Are you sure you want to delete your user?
        This will delete your username from our database. This will also remove you from organization where you are a member. This will NOT remove any logs that have your name in it. Your user will be re-created if you login again to the app.',
            'doubleConfirm' => true,
            'doubleConfirmKeyWord' => $usernameArray['username'],
            'resultType' => 'text',
            'submitButton' => [
                'text' => 'Delete User',
                'size' => 'medium',
                //'style' => '&#10060;'
            ],
        ];

        echo Forms::render($deleteUserFormOptions);

    echo '</div>';

echo '</div>';
