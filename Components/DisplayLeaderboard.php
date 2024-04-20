<?php

namespace Components;

use Components\Html;
use Components\DataGrid\DataGridDBTable;
use Models\SeasonData;

class DisplayLeaderboard
{
    public static function displayLeaderboard($type, $region, $season, $theme)
    {
        $html = '';
        // Data now

        $seasonData = new SeasonData($season, $region, $type);

        $data = $seasonData->getData();

        $html .= HTML::p($seasonData->getDescription());

        // Find the maximum rating
        $maxRating = 0;
        foreach ($data as $user) {
            if ($user["rating"] > $maxRating) {
                $maxRating = $user["rating"];
            }
        }

        // Initialize an array to store the count of users in each rating range
        $ratingRanges = [];

        // Iterate through the data to count users in each rating range
        foreach ($data as $user) {
            $rating = $user["rating"];
            $rangeKey = floor($rating / 1000) * 1000 . "-" . (floor($rating / 1000) * 1000 + 999);
            if (!isset($ratingRanges[$rangeKey])) {
                $ratingRanges[$rangeKey] = 0;
            }
            $ratingRanges[$rangeKey]++;
        }

        // Autoload the ratingRanges array to a bar chart
        $html .= '<div id="doughnut-limits-holder" class="my-12 flex flex-wrap flex-row justify-center items-center">';
            // initiate an array that will pass the following data into hidden inputs so Javascript can have access to this data on page load and draw the charts
            $chartsArray = [
                [
                    'type' => 'barchart',
                    'data' => [
                        'parentDiv' => 'doughnut-limits-holder',
                        'title' => 'Player distribution by rating',
                        'width' => 400,
                        'height' => 200,
                        'labels' => array_keys($ratingRanges),
                        'data' => array_values($ratingRanges)
                    ]
                ]
            ];
            // Now go through them and create an input hidden for each
            foreach ($chartsArray as $array) {
                $html .= '<input type="hidden" name="autoload" value="' . htmlspecialchars(json_encode($array)) . '" />';
            }
        $html .= '</div>';

        $html .= DataGridDBTable::createTable('', $data, $theme, $seasonData->getTitle(), false, false, false);

        return $html;
    }
}
