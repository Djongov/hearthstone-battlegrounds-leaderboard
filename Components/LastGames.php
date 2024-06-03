<?php

namespace Components;

use Models\LastGames as LastGamesModel;
use Components\HTML;
use Components\Alerts;

class LastGames
{
    
    public static function display(string $table, string $accountid, int $numberOfLastGames, int $main_id = null) : string
    {
        if ($main_id === null) {
            $lastGames = LastGamesModel::get($table, $accountid, $numberOfLastGames);
        } else {
            $lastGames = LastGamesModel::getWithMainId($table, $accountid, $numberOfLastGames, $main_id);
        }

        $html = '';

        // Let's display a small table with the rating and the difference between the last entry
        if ($lastGames) {
            $html .= HTML::h4('Last ' . $numberOfLastGames . ' games', true);
            $html .= '<table class="mx-auto my-4 table-auto w-max-sm border boreder-black dark:border-gray-400 text-center bg-gray-100 dark:bg-gray-900">';
                $html .= '<thead>';
                    $html .= '<tr>';
                        $html .= '<th class="border px-4 py-2">Rating</th>';
                        $html .= '<th class="border px-4 py-2">MMR</th>';
                        $html .= '<th class="border px-4 py-2">Timestamp (UTC)</th>';
                    $html .= '</tr>';
                $html .= '</thead>';
                $html .= '<tbody>';
                    $lastRating = null;
                    foreach ($lastGames as $row) {
                        $rating = $row['rating'];
                        $timestamp = $row['timestamp'];
                        if ($lastRating === null) {
                            $lastRating = $rating;
                            continue;
                        }
                        $difference = $lastRating - $rating;

                        // Let's color the difference, if negative, red bold, if positive, green bold
                        $color = $difference < 0 ? 'text-red-500' : 'text-green-500';
                        // Let's try to predict placement
                        $placement = 0;
                        // Define the mapping between difference ranges and placement values
                        $map = [
                            [-200, -80, 8],
                            [-80, -65, 7],
                            [-65, -50, 6],
                            [-50, -20, 5],
                            [-20, 11, 4],
                            [11, 44, 3],
                            [44, 64, 2],
                            [64, PHP_INT_MAX, 1] // PHP_INT_MAX represents infinity
                        ];

                        $highMMRMap = [
                            [-200, -100, 8],
                            [-100, -80, 7],
                            [-80, -60, 6],
                            [-60, -10, 5],
                            [-10, 3, 4],
                            [3, 28, 3],
                            [28, 50, 2],
                            [50, PHP_INT_MAX, 1] // PHP_INT_MAX represents infinity
                        ];

                        // Iterate over the map to find the appropriate placement
                        $chosenMap = $rating > 13000 ? $highMMRMap : $map;

                        foreach ($chosenMap as $item) {
                            $min = $item[0];
                            $max = $item[1];
                            $placementValue = $item[2];

                            if ($difference > $min && $difference <= $max) {
                                $placement = $placementValue;
                                break;
                            }
                        }

                        $placementString = '';

                        if ($placement === 1) {
                            $placementString = '1st';
                        } elseif ($placement === 2) {
                            $placementString = '2nd';
                        } elseif ($placement === 3) {
                            $placementString = '3rd';
                        } elseif ($placement > 3) {
                            $placementString = $placement . 'th';
                        }

                        $html .= '<tr>';
                            $html .= '<td class="border px-4 py-2">' . $rating . '</td>';
                            $html .= '<td class="border px-4 py-2 ' . $color . '">' . $difference . ' (' . $placementString . '*)</td>';
                            $html .= '<td class="border px-4 py-2">' . $timestamp . '</td>';
                        $html .= '</tr>';
                        
                        $lastRating = $rating;
                    }
                $html .= '</tbody>';
            $html .= '</table>';
            $html .= HTML::p('* - possible placement, but will not be always accurate', ['text-center', 'my-4']);
            $html .= HTML::P('The above table will not show games resulting in 0 rating because of the way data is pulled', ['text-center', 'my-4']);
        } else {
            $html .= Alerts::danger('No rating data found to display last games.');
        }

        return $html;
    }
}
