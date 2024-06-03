<?php

namespace Models;

use App\Database\DB;

class LastGames
{
    public static function get(string $table, string $accountid, int $numberOfLastGames) : array
    {
        $lastGamesNumberQuery = $numberOfLastGames + 1;
        // Last 5 games
        //$lastGames = array_slice($ratingData, -6, 6, true);
        $last5GamesQuery = "SELECT accountid, timestamp, rating
        FROM (
            SELECT *, LAG(rating) OVER (PARTITION BY accountid ORDER BY timestamp DESC) AS prev_rating
            FROM $table
            WHERE accountid = ?
        ) AS subquery
        WHERE rating != prev_rating OR prev_rating IS NULL
        ORDER BY timestamp DESC
        LIMIT $lastGamesNumberQuery;
        ";

        $db = new DB();
    
        $pdo = $db->getConnection();

        $stmt = $pdo->prepare($last5GamesQuery);

        $stmt->execute([$accountid]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
    public static function getWithMainId(string $table, string $accountid, int $numberOfLastGames, int $main_id) : array
    {
        $lastGamesNumberQuery = $numberOfLastGames + 1;
        // Last 5 games
        //$lastGames = array_slice($ratingData, -6, 6, true);
        $last5GamesQuery = "SELECT accountid, timestamp, rating, main_id
        FROM (
            SELECT *, LAG(rating) OVER (PARTITION BY accountid ORDER BY timestamp DESC) AS prev_rating
            FROM $table
            WHERE accountid = ? AND main_id = ?
        ) AS subquery
        WHERE rating != prev_rating OR prev_rating IS NULL
        ORDER BY timestamp DESC
        LIMIT $lastGamesNumberQuery;
        ";

        $db = new DB();
    
        $pdo = $db->getConnection();

        $stmt = $pdo->prepare($last5GamesQuery);

        $stmt->execute([$accountid, $main_id]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
