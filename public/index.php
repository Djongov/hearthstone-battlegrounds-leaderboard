<?php
define("START_TIME", microtime(true));
// Load the autoloader
require_once dirname($_SERVER['DOCUMENT_ROOT']) . '/vendor/autoload.php';

function dd()
{
    array_map(function ($x) {
        var_dump($x);
    }, func_get_args());
    die;
}

use App\App;

// Initialize the app
$app = new App();

// Run the app
$app->init();
