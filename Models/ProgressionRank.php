<?php

namespace Models;

use App\Database\DB;

class ProgressionRank
{
    public static function add(string $accontid, string $region, string $type, int $rank, int $season): void
    {
        $table = 'rank_progression_season_' . $season . '_' . $region . '_' . $type;
        $sql = "INSERT INTO `$table` (`accountid`, `rank`) VALUES (?, ?)";
        $db = new DB();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$accontid, $rank]);
    }
}
