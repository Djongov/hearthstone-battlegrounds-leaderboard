<?php

namespace Components;

use Models\RatingOverHours as RatingOverHoursModel;
use Components\HTML;
use Components\Alerts;

class RatingOverHours
{
    public static function display(string $accountid, string $region, string $type, int $season, int $lastHoursNumber, int $main_id = null)
    {
        $ratingGained = RatingOverHoursModel::get($accountid, $region, $type, $season, $lastHoursNumber, $main_id);

        $html = '';

        $colorLast24Hours = $ratingGained <= 0 ? 'text-red-500' : 'text-green-500';

        if ($lastHoursNumber) {
            $html .= HTML::p('Rating gained in the last 24 hours: <span class="' . $colorLast24Hours . '">' . $ratingGained . '</span>', ['text-center', 'my-4']);
        } else {
            $html .= Alerts::danger('No rating data found');
        }

        return $html;
    }
}
