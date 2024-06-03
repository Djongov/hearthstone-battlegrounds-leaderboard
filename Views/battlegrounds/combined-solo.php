<?php

use App\Database\DB;
use Components\DataGrid\DataGrid;

$db = new DB();

$pdo = $db->getConnection();

$sql = "SELECT rank_number AS `rank`, accountid, rating, origin_region
FROM (
    SELECT accountid, rating, origin_region,
           @rownum := @rownum + 1 AS rank_number
    FROM (
        SELECT accountid, rating,
               CASE
                   WHEN source_table = 'battlegrounds_season_7_ap_solo' THEN 'Asia-Pacific'
                   WHEN source_table = 'battlegrounds_season_7_us_solo' THEN 'Americas'
                   WHEN source_table = 'battlegrounds_season_7_eu_solo' THEN 'Europe'
               END AS origin_region
        FROM (
            SELECT 'battlegrounds_season_7_ap_solo' AS source_table, accountid, rating
            FROM battlegrounds_season_7_ap_solo
            UNION ALL
            SELECT 'battlegrounds_season_7_eu_solo' AS source_table, accountid, rating
            FROM battlegrounds_season_7_eu_solo
            UNION ALL
            SELECT 'battlegrounds_season_7_us_solo' AS source_table, accountid, rating
            FROM battlegrounds_season_7_us_solo
        ) AS combined_tables
        ORDER BY rating DESC
    ) AS ranked_data
    CROSS JOIN (SELECT @rownum := 0) AS r
) AS final_data;
";

$pdo->query($sql);

$result = $pdo->query($sql);

if ($result->rowCount() == 0) {
    return [];
}

$array = $result->fetchAll(\PDO::FETCH_ASSOC);

echo DataGrid::createTable('combined', $array, $theme, 'Combined Leaderboard', false, false, false);
