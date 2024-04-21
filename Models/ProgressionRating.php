<?php

namespace Models;

use App\Database\DB;

class ProgressionRating
{
    public static function add(string $accontid, string $region, string $type, int $rating, int $season): void
    {
        $sql = "INSERT INTO `rating_progression` (`accountid`, `region`, `type`, `rating`, `season`) VALUES (?, ?, ?, ?, ?)";
        $db = new DB();
        $pdo = $db->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$accontid, $region, $type, $rating, $season]);
    }
}
