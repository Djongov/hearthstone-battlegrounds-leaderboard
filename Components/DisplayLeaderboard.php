<?php

namespace Components;

use Components\Html;
use Components\DataGrid\DataGridDBTable;

class DisplayLeaderboard
{
    public static function displayLeaderboard($type, $region, $season, $theme)
    {
        $html = '';

        $table = 'battlegrounds_season_' . $season . '_' . $region;
        if ($season === 7) {
            $title = ucfirst($type) . ' ' . strtoupper($region) . ' battlegrounds Season ' . $season . ' Leaderboard';
            $table .=  '_' . $type;
        } else {
            $title = strtoupper($region) . ' battlegrounds Season ' . $season . ' Leaderboard';
        }

        $query = "SELECT `rank`, `accountid`, `rating` FROM `$table` ORDER BY `rating` DESC";

        $html .= HTML::p('This is a searchable (almost) live data of the ' . ucfirst($type) . ' Hearthstone Season ' . $season . ' Leaderboard for the ' . strtoupper($region) . ' region');

        $html .= DataGridDBTable::renderQuery($title, $query, $theme, false, false, '', false);

        return $html;
    }
}
