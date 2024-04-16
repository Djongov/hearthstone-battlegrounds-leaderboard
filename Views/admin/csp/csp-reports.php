<?php

use App\Security\Firewall;
use Controllers\Api\Output;
use App\General;
use App\Database\MYSQL;
use Components\Alerts;
use Components\DataGrid\DataGrid;
use Components\Html;
use Components\DataGrid\SimpleVerticalDataGrid;

// First firewall check
Firewall::activate();

// Admin check
if (!$isAdmin) {
    Output::error('You are not an admin', 403);
}

$result = MYSQL::query('SELECT `id`,`created_at`,`domain`,`url`,`referrer`,`violated_directive`,`effective_directive`,`disposition`,`blocked_uri`,`line_number`,`column_number`,`source_file`,`status_code`,`script_sample` FROM `csp_reports` ORDER BY `id` DESC');

if ($result->num_rows === 0) {
    echo Alerts::danger('No CSP reports data found');
    return;
}

$cspArray = $result->fetch_all(MYSQLI_ASSOC);

// Let's build some autoload charts

function filterValidValues($array)
{
    return array_filter($array, function ($value) {
        return $value !== null && (is_string($value) || is_int($value));
    });
}

// We will need an array with the values of domain
$domains = array_column($cspArray, 'domain');
// Now we need to find out how many entries we have with this domain
$domainsCount = array_count_values(filterValidValues($domains));

// Now the domain data for the chart
$domainData = [
    'type' => 'piechart',
    'data' => [
        'parentDiv' => 'csp-charts',
        'title' => 'Domains',
        'width' => 180,
        'height' => 180,
        'labels' => array_keys($domainsCount),
        'data' => array_values($domainsCount)
    ]
];

$violatedDirectives = array_column($cspArray, 'violated_directive');
$violatedDirectivesCount = array_count_values(filterValidValues($violatedDirectives));

$violatedDirectivesData = [
    'type' => 'piechart',
    'data' => [
        'parentDiv' => 'csp-charts',
        'title' => 'Violated Directives',
        'width' => 180,
        'height' => 180,
        'labels' => array_keys($violatedDirectivesCount),
        'data' => array_values($violatedDirectivesCount)
    ]
];

$statusCodes = array_column($cspArray, 'status_code');
$statusCodesCount = array_count_values(filterValidValues($statusCodes));

$statusCodesData = [
    'type' => 'piechart',
    'data' => [
        'parentDiv' => 'csp-charts',
        'title' => 'Status Codes',
        'width' => 180,
        'height' => 180,
        'labels' => array_keys($statusCodesCount),
        'data' => array_values($statusCodesCount)
    ]
];

echo '<div id="csp-charts" class="flex flex-row flex-wrap p-6 justify-center">';
    $chartsArray = [
        $domainData,
        $violatedDirectivesData,
        $statusCodesData
    ];
    // Now go through them and create an input hidden for each
    foreach ($chartsArray as $array) {
        echo '<input type="hidden" name="autoload" value="' . htmlspecialchars(json_encode($array)) . '" />';
    }
echo '</div>';

// Let's do a blocked_uri table
$blockedUri = array_column($cspArray, 'blocked_uri');

// Let's calculate the total number of blocked_uri based on the occurrence of each key in the $blockedUri array
$indexedArray = array_count_values($blockedUri);

// Sort the array in descending order
ksort($indexedArray, SORT_NUMERIC | SORT_DESC);

echo '<div class="flex justify-center">';
    echo DataGrid::createTable('blocked_uri', General::assocToIndexed($indexedArray), $theme, 'Blocked URIs', false, false);
echo '</div>';

echo DataGrid::createTable('csp_reports', $cspArray, $theme, 'CSP Reports', true, true);
