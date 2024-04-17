<?php

namespace Components;

use Components\Html;
use Components\DataGrid\DataGridDBTable;

class DisplayLeaderboard
{
    public static function displayLeaderboard($type, $region, $season, $theme)
    {
        $table = 'battlegrounds_season_' . $season . '_' . $region;
        if ($season === 7) {
            $table .=  '_' . $type;
        }

        $query = "SELECT `rank`, `accountid`, `rating` FROM `$table` ORDER BY `rating` DESC";

        $html = '';

        $html .= HTML::h2(ucfirst($type) . ' ' . strtoupper($region) . ' battlegrounds Season ' . $season . ' Leaderboard', true);

        $html .= HTML::p('This is a searchable (almost) live data of the ' . ucfirst($type) . ' Hearthstone Season ' . $season . ' Leaderboard for the ' . strtoupper($region) . ' region');

        $html .= DataGridDBTable::renderQuery('', $query, $theme, false, false, $type . ' ' . $region);

        return $html;
    }
}
