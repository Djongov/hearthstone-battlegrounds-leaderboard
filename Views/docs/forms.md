[title]: # (Forms)
[description]: # (This is how you can use the built-in Forms feature.)

## Forms

If you want to create a form.

``` php
use Components\Forms;
```

Then you need to create an options array

``` php
$formOptions = [
    // If you need input fields, open an 'inputs' key
    'inputs' => [
        'input' => [
            [
                'label' => 'Email Address',
                'type' => 'email',
                'placeholder' => 'John.Doe@example.com',
                'name' => 'email',
                'description' => 'Provide a valid email',
                'id' => uniqid(),
            ]
        ]
    ],
    'hidden' => [
        [
            'name' => 'username',
            'value' => $usernameArray['username']
        ]
    ],
    // Now come the form options and the submit button options
    'theme' => $theme, // Optional, defaults to COLOR_SCHEME
    'method' => 'POST', // Optional, defaults to POST
    'action' => '/api/example', // Required
    'additionalClasses' => 'qwerty power', // Optional
    'reloadOnSubmit' => false,
    //'confirm' => true,
    //'confirmText' => 'Are you sure you want to send this quack?', // Optional, defaults to "Are you sure?" if ommited
    'resultType' => 'text',
    //'doubleConfirm' => true,
    //'doubleConfirmKeyWord' => 'delete',
    'submitButton' => [
        'text' => 'Submit',
        'id' => uniqid(),
        'name' => 'submit',
        'type' => 'submit',
        'size' => 'medium',
        'disabled' => true,
        'title' => 'Disabled'
        //'style' => '&#10060;'
    ]
];
```

### Options

Here are the options that are accepted for Forms.

#### Form Options

These form options are presneted into the options array

| Option   | Type     | Required | Default | Description           |
|----------|----------|----------|---------|-----------------------|
| inputs  | Array   | Yes      | -       | This is the main holder for ```<input>``` type of fields|
| theme  | string   | No       | COLOR_SCHEME      | This sets the theme color for the form. You could set it to the current user theme by giving it value of ```$theme```|
| method  | string     | No      | POST  | ```GET```, ```POST```, ```PUT```, ```DELETE```|
| action  | string     | Yes      | -  | Fetch URI |
| additionalClasses  | string     | No      | -  | String comprised of additional classes to be added to the ```<form>```. Syntax is comma-separated classes such as ``` 'customClass, customClass2' ``` |
| reloadOnSubmit  | bool     | No      | -  | Should the page refresh after the form has successfully submitted and received a HTTP Status OK. If status code is >= 300, it will not refresh |
| redirectOnSubmit  | string     | No      | -  | URL where we redirect after a successful form submission |
| confirm  | bool     | No      | -  | If the form will present a Modal before submitting the form |
| confirmText  | string     | No      | 'Are you sure?'  | What text should appear on the modal. It can support text or html. Needs ```confirm``` to be set and be ```true``` |
| doubleConfirm  | bool     | No      | -  | If this is set and set to ```true``` the form will present a special Modal that would require a special ```keyword``` to be provided before the Submit button is enabled |
| doubleConfirmKeyWord  | string     | No      | -  | Provides the ```keyword``` to be put if ```doubleConfirm``` is set to ```true``` |
| resultType | string | No | ' | Values of ```html``` or ```text``` only. If ```html```, the result will be displayed as ```.innerHTML```, otherwise as ```.innerText``` in the result div |
| submitButton | Array | Yes | - | Array of settings for the submit Button of the form |
