<?php

use Controllers\Api\Output;
use Controllers\Api\Checks;
use Components\Html;

$checks = new Checks($vars, $_POST);

$checks->apiChecks();

if (!isset($_POST['data'])) {
    return Output::error('Data is required', 400);
}

//echo Output::success(base64_encode($_POST['data']));
echo '<div class="container break-words">' . HTML::code(base64_encode($_POST['data'])) . '</div>';
