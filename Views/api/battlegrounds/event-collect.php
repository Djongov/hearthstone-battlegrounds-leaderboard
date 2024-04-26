<?php

use App\Database\DB;
use Controllers\Api\Checks;
use Controllers\Api\Output;

$table = 'record_events';

$db = new DB();

$pdo = $db->getConnection();

$checks = new Checks($vars, $_POST);

$checks->checkParams(['output', 'type', 'season', 'region'], $_POST);

$stmt = $pdo->prepare("INSERT INTO `$table` (`output`, `type`, `season`, `region`) VALUES (?, ?, ?, ?)");

try {
    $stmt->execute([$_POST['output'], $_POST['type'], $_POST['season'], $_POST['region']]);
    echo Output::success('Record added');
} catch (\PDOException $e) {
    Output::error($e->getMessage());
}
