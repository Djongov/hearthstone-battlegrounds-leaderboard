<?php

namespace Models;

use App\Database\DB;

class ProgressionRating
{
    public static function add(string $accontid, string $region, string $type, int $rating, int $season): void
    {
        $table = 'rating_progression_season_' . $season . '_' . $region . '_' . $type;
        $sql = "INSERT INTO `$table` (`accountid`, `rating`) VALUES (?, ?)";
        $db = new DB();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$accontid, $rating]);
    }
}
