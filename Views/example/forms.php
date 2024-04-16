<?php

use Components\Forms;
use Components\Html;

echo Html::h1('Forms', true);

echo Html::p('This is a forms page. Here is how we can use the form abilities built into the system.', ['text-center']);

echo Html::h2('Form example');

echo HTML::p('This is a sample form array to render a form with the least amount of options. It only produces a button and a hidden input field, suitable for button click actions.');

$formOptions = [
    'inputs' => [
        'hidden' => [
            [
                'name' => 'hidden',
                'value' => 'hidden value',
            ]
        ],
    ],
    'action' => '/api/example',
    'submitButton' => [
        'text' => 'Submit',
        'size' => 'large',
    ]
];

echo Forms::render($formOptions);

echo HTML::code('
$formOptions = [
    "inputs" => [
        "hidden" => [
            [
                "name" => "hidden",
                "value" => "hidden value",
            ]
        ],
    ],
    "action" => "/api/example",
    "submitButton" => [
        "text" => "Submit",
        "size" => "large",
    ]
];
');


