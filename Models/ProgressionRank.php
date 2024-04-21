<?php

namespace Models;

use App\Database\DB;

class ProgressionRank
{
    public static function add(string $accontid, string $region, string $type, int $rank, int $season): void
    {
        $sql = "INSERT INTO `rank_progression` (`accountid`, `region`, `type`, `rank`, `season`) VALUES (?, ?, ?, ?, ?)";
        $db = new DB();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$accontid, $region, $type, $rank, $season]);
    }
}
