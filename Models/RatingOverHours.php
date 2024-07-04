<?php

namespace Models;

use App\Database\DB;

class RatingOverHours
{
    public static function get(string $accountid, string $region, string $type, int $season, int $lastHoursNumber, int $main_id = null) : int
    {
        $ratingTable = 'rating_progression_season_' . $season . '_' . $region . '_' . $type;

        if ($main_id === null) {
            $lastHoursQuery = "SELECT * FROM $ratingTable WHERE `accountid` = ? AND `timestamp` >= NOW() - INTERVAL $lastHoursNumber HOUR ORDER BY `timestamp` DESC";
        } else {
            $lastHoursQuery = "SELECT * FROM $ratingTable WHERE `accountid` = ? AND `main_id`=? AND `timestamp` >= NOW() - INTERVAL $lastHoursNumber HOUR ORDER BY `timestamp` DESC";
        }

        $db = new DB();

        $pdo = $db->getConnection();

        $stmt = $pdo->prepare($lastHoursQuery);

        if ($main_id === null) {
            $stmt->execute([$accountid]);
        } else {
            $stmt->execute([$accountid, $main_id]);
        }

        $array = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (!$array) {
            return 0;
        }

        // Now let's select the first entry and the last entry
        $firstEntry = reset($array);
        $lastEntry = end($array);

        // Let's calculate the rating gained
        return $lastEntry['rating'] - $firstEntry['rating'];
    }
}
