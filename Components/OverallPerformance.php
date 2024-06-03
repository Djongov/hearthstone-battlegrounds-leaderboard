<?php

namespace Components;

use Components\HTML;
use Components\Alerts;

class OverallPerformance
{
    public static function display(string $accountid, array $ratingData, array $rankData, int $main_id = null) : string
    {
        // Overall performance

        $html = '';

        $chartsArray = [];

        $html .= HTML::h4('Overall performance', true);
        
        $chartsHolderId = ($main_id === null) ? 'doughnut-limits-holder' : 'doughnut-limits-holder-' . $main_id;

        // Let's prepare the line chart data. timestamp key in ratinData will be the x-axis and rating will be the y-axis
        // We will also prepare the data for the line chart
        $ratingData = array_column($ratingData, 'rating', 'timestamp');

        if (!$ratingData) {
            $html .= Alerts::danger('No rating data found');
        } else {
            $chartRatingAutoloadData = [
                'type' => 'linechart',
                'data' => [
                    'parentDiv' => $chartsHolderId,
                    'title' => $accountid . ' MMR progression',
                    'width' => 800,
                    'height' => 300,
                    'labels' => array_keys($ratingData),
                    'datasets' => [
                        [
                            'label' => $accountid,
                            'data' => array_values($ratingData)
                        ]
                    ]
                ],
            ];
            array_push($chartsArray, $chartRatingAutoloadData);
        }

        // Let's prepare the line chart data. timestamp key in ratinData will be the x-axis and rating will be the y-axis

        $rankData = array_column($rankData, 'rank', 'timestamp');

        if (!$rankData) {
            $html .= Alerts::danger('No rank data found');
        } else {
            $chartRankAutoloadData = [
                'type' => 'linechart',
                'data' => [
                    'parentDiv' => $chartsHolderId,
                    'title' => $accountid . ' rank progression',
                    'width' => 600,
                    'height' => 200,
                    'labels' => array_keys($rankData),
                    'datasets' => [
                        [
                            'label' => $accountid,
                            'data' => array_values($rankData)
                        ]
                    ]
                ]
            ];
            array_push($chartsArray, $chartRankAutoloadData);
        }


        if ($chartsArray) {
            $html .= '<div id="' . $chartsHolderId . '" class="my-12 flex flex-wrap flex-row justify-center items-center">';
                // initiate an array that will pass the following data into hidden inputs so Javascript can have access to this data on page load and draw the charts

                // Now go through them and create an input hidden for each
                foreach ($chartsArray as $array) {
                    $html .= '<input type="hidden" name="autoload" value="' . htmlspecialchars(json_encode($array)) . '" />';
                }
            $html .= '</div>';
        }

        return $html;
    }
}
