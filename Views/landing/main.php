<?php

use Components\Html;
use Request\NativeHttp;
use App\Logs\SystemLog;
use Components\Forms;
use App\General;

use Models\Api\User;

$user = new User();



//dd($usernameArray);



//$request = NativeHttp::get('https://www.ipqualityscore.com/api/json/leaked/email/IgJi2FQr2iU11QkbkBtoWun5f1YmSk8y/djongov@gamerz-bg.com', [], true);

// $array = [
//     'to' => [
//         [
//             'email' => 'djongov@gamerz-bg.com',
//             'name' => 'Djo'
//         ]
//     ],
//     'subject' => 'Test',
//     'body' => 'Test body'
// ];

// $headers = [
//     'x-api-key: 57a7c24eb9a17c9b8e0d149586143c0e9c518083185375b8ff994a67b99c4df0'
// ];

// var_dump(NativeHttp::post('https://azure-waf-manager-api.sunwellsolutions.com/v1/mail/send', $array, true, $headers));

echo Html::h1('Welcome h1');
echo Html::h2('Welcome h2');
echo Html::h3('Welcome h3');
echo Html::h4('Welcome h4');

echo Html::p('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed elementum nunc sed risus lacinia tempus. Duis sit amet porta nunc. In a venenatis nulla. Donec eget felis interdum, cursus eros nec, venenatis ipsum. Duis velit orci, imperdiet aliquam vulputate vitae, ultricies et eros. Proin congue finibus sapien in venenatis. Aliquam pulvinar pellentesque leo, ac viverra dolor aliquam non. Pellentesque fringilla eget purus ac convallis. Nam eleifend magna libero, rhoncus consequat enim volutpat nec.');

echo General::currentBrowser();

$form_options = [
    'inputs' => [
        // 'checkboxGroup' => [
        //     [
        //         'label' => 'Checkbox 1 in group 1',
        //         'type' => 'checkbox',
        //         'name' => 'foo',
        //         'value' => 'bar',
        //         'description' => 'This is an example checkbox',
        //         'disabled' => false,
        //         'checked' => true,
        //         'group' => 'group1',
        //     ],
        //     [
        //         'label' => 'Checkbox 2 in group 1',
        //         'type' => 'checkbox',
        //         'name' => 'foo',
        //         'value' => 'bar2',
        //         'group' => 'group1',
        //         'description' => 'This is an example checkbox 2',
        //         'disabled' => false,
        //         'checked' => false,
        //     ]
        // ]
        'checkbox' => [
            [
                'label' => 'Checkbox',
                'type' => 'checkbox',
                'name' => 'foo',
                'value' => 'bar',
                'description' => 'This is an example checkbox',
                'disabled' => false,
                'checked' => true,
                'extraClasses' => ['text-red-500']
            ],
            [
                'label' => 'Checkbox 2',
                'type' => 'checkbox',
                'name' => 'foo',
                'value' => 'bar2',
                'description' => 'This is an example checkbox 2',
                'disabled' => false,
                'checked' => false,
                'extraClasses' => ['text-red-500']
            ]
        ],
    ],
    'theme' => $theme, // Optional, defaults to COLOR_SCHEME
    'method' => 'POST', // Optional, defaults to POST
    'action' => '/api/example', // Required
    'additionalClasses' => 'qwerty power', // Optional
    'reloadOnSubmit' => false,
    'resultType' => 'html',
    'submitButton' => [
        'text' => 'Submit',
        'id' => uniqid(),
        'name' => 'submit',
        'type' => 'submit',
        'size' => 'medium',
        'disabled' => false,
        'title' => 'Disabled'
        //'style' => '&#10060;'
    ],
];
echo '<div class="w-[44rem] dark:bg-gray-800 p-2 rounded-md shadow-md shadow-gray-400 my-6">';
    echo Forms::render($form_options);
echo '</div>';


