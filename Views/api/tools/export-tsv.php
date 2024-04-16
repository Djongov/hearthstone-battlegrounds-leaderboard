<?php
use Controllers\Api\Checks;

$checks = new Checks($vars, $_POST);

$checks->apiChecksNoCSRFHeader(false);

if (isset($_POST['data'])) {
    $data = $_POST['data'];
} else {
    $data = '';
}
//var_dump($_POST['data']);
$data = unserialize($data);

if (isset($_POST['type'])) {
    $type = htmlspecialchars($_POST['type']);
} else {
    $type = '';
}

// Now, Sentinel returns JSON in a weird format where [ { has space between the two and we need to clean them, otherwise CSV breaks if there is JSON - }]
// Took this genius method from https://stackoverflow.com/questions/51497618/replace-value-in-multidimensional-php-array
foreach ($data as &$set) {
    foreach ($set as &$subset) {
        $subset = ($subset !== null) ? preg_replace('/\s+/', '', $subset) : null;
    }
}
unset($set, $subset); // avoid future variable interferences

//$data[0]["details_matches_s"] = preg_replace('/\s+/', '',$data[0]["details_matches_s"]);

// file name for download
$fileName = $type . "-logs-data-" . date('Y-m-d-H-i-s') . ".tsv";

$unique_heads = '';
$excelData = '';
// Let's map what the head of the CSV will be
foreach ($data as $data_id => $heads) {
    $unique_heads = array_unique(array_keys($heads));
}

// Display column names as first row 
$excelData = implode("\t", array_values($unique_heads)) . "\n";


// And the actual data under the columns
foreach ($data as $value) {
    $excelData .= implode("\t", array_values($value)) . PHP_EOL;
}

// Headers for download
header("Content-Type: text/tab-separated-values");
//header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$fileName\"");

// Render excel data
echo $excelData;
