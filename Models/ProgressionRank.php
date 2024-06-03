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
    public static function get(string $accountid, string $region, string $type, int $season): array
    {
        $table = 'rank_progression_season_' . $season . '_' . $region . '_' . $type;
        $sql = "SELECT * FROM `$table` WHERE `accountid` = ?";
        $db = new DB();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$accountid]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    // Also get with main_id
    public static function getWithMainId(string $accountid, int $main_id, string $region, string $type, int $season): array
    {
        $table = 'rank_progression_season_' . $season . '_' . $region . '_' . $type;
        $sql = "SELECT * FROM `$table` WHERE `accountid` = ? AND `main_id` = ?";
        $db = new DB();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$accountid, $main_id]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
