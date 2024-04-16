<?php

use Components\Forms;

$formOptions = [
    'inputs' => [
        'textarea' => [
            [
                'name' => 'data',
                'label' => 'Data',
                'required' => true,
                'placeholder' => 'Data to encode',
                'description' => 'The data to encode'
            ]
        ]
    ],
    "action" => "/api/tools/base64encode",
    "submitButton" => [
        "text" => "Encode",
        "size" => "medium",
    ]
];

// Let's wrap it
echo '<div class="container">';
    echo Forms::render($formOptions);
echo '</div>';
