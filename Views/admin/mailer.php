<?php

use Components\Forms;
use Components\HTML;

$formOptions = [
    "inputs" => [
        'input' => [
            [
                'type' => 'text',
                'label' => 'Recipients',
                'name' => 'recipients',
                'placeholder' => 'John.Doe@example.com;Lilith.Sambers@example.com',
                'description' => 'Enter the email addresses of the recipients, separated by a semicolon.',
                'required' => true,
            ],
            [
                'type' => 'text',
                'label' => 'Subject',
                'name' => 'subject',
                'placeholder' => 'Subject',
                'description' => 'Enter the subject of the email.',
                'required' => true,
            ],
        ],
        "tinymce" => [
            [
                "label" => "Email Body",
                "name" => "body",
                "description" => "Enter the message of the email. Can be in HTML format.",
                "required" => true,
            ]
        ],
        "hidden" => [
            [
                "name" => "hidden",
                "value" => "hidden value",
            ]
        ],
    ],
    "id" => "tinymce",
    "action" => "/api/mail/send",
    "submitButton" => [
        "text" => "Send",
        "size" => "large",
    ]
];

// Let's wrap it
echo '<div class="container mx-auto">';
    echo HTML::h2('Mailer', true);
    echo HTML::p('Send an email to via SendGrid and TinyMCE as editor.', ['text-center']);
    echo Forms::render($formOptions);
echo '</div>';

echo '<script src="' . TINYMCE_SCRIPT_LINK . '" referrerpolicy="origin"></script>';
echo '<script src="/assets/js/mailer.js?=' . time() . '"></script>';
