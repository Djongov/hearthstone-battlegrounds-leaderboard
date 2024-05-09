<?php

use App\Database\DB;
use Components\DataGrid\DataGrid;

$db = new DB();

$pdo = $db->getConnection();

$sql = "SELECT rank_number AS `rank`, rating, accountid, origin_region
FROM (
    SELECT rating, accountid, origin_region,
           @rownum := @rownum + 1 AS rank_number
    FROM (
        SELECT rating, accountid,
               CASE
                   WHEN source_table = 'battlegrounds_season_7_ap_duos' THEN 'Asia-Pacific'
                   WHEN source_table = 'battlegrounds_season_7_us_duos' THEN 'Americas'
                   WHEN source_table = 'battlegrounds_season_7_eu_duos' THEN 'Europe'
               END AS origin_region
        FROM (
            SELECT 'battlegrounds_season_7_ap_duos' AS source_table, rating, accountid
            FROM battlegrounds_season_7_ap_duos
            UNION ALL
            SELECT 'battlegrounds_season_7_eu_duos' AS source_table, rating, accountid
            FROM battlegrounds_season_7_eu_duos
            UNION ALL
            SELECT 'battlegrounds_season_7_us_duos' AS source_table, rating, accountid
            FROM battlegrounds_season_7_us_duos
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

echo DataGrid::createTable('combined', $array, $theme, 'Combined Leaderboard', false, false);