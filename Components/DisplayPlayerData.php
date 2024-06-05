<?php

namespace Components;

use Models\ProgressionRank;
use Models\ProgressionRating;
use Components\HTML;
use Components\LastGames;
use Components\OverallPerformance;

class DisplayPlayerData
{
    public string $accountid;
    public string $region;
    public string $type;
    public int $season;
    public ?int $main_id;

    public function __construct(string $accountid, string $region, string $type, int $season, $main_id = null)
    {
        $this->accountid = $accountid;
        $this->region = $region;
        $this->type = $type;
        $this->season = $season;
        $this->main_id = $main_id;
    }
    private function calculatePageNumber($rank)
    {
        return ceil($rank / 25);
    }
    public function display()
    {
        if ($this->main_id !== null) {
            $ratingData = ProgressionRating::getWithMainId($this->accountid, $this->main_id, $this->region, $this->type, $this->season);
            $rankData = ProgressionRank::getWithMainId($this->accountid, $this->main_id, $this->region, $this->type, $this->season);
        } else {
            $ratingData = ProgressionRating::get($this->accountid, $this->region, $this->type, $this->season);
            $rankData = ProgressionRank::get($this->accountid, $this->region, $this->type, $this->season);
        }

        $html = '';

        $html .= HTML::h3('Data for ' . $this->accountid . ' for ' . $this->region . ' ' . $this->type . ' season ' . $this->season, true);

        // Let's show current rank and rating
        $currentRank = $rankData ? end($rankData)['rank'] : 'N/A';

        $html .= HTML::h4('Current rank - ' . $currentRank, true);

        $currentRating = $ratingData ? end($ratingData)['rating'] : 'N/A';

        $html .= HTML::h4('Current MMR - ' . $currentRating, true);

        // Highest rank attained and at what timestamp

        $highestRank = 0;
        $highestRankTimestamp = 0;

        // Check if $rankData is not empty
        if (!empty($rankData)) {
            // Find the maximum rank
            $highestRank = min(array_column($rankData, 'rank'));

            // Find the corresponding timestamp for the highest rank
            $highestRankEntry = array_filter($rankData, function($entry) use ($highestRank) {
                return $entry['rank'] == $highestRank;
            });

            // Extract the timestamp if found
            $highestRankTimestamp = null;
            if (!empty($highestRankEntry)) {
                $highestRankTimestamp = reset($highestRankEntry)['timestamp'];
            }

            $highestRankImage = ($highestRank === 1) ? '<img src="/assets/images/1stplace.png" alt="1st place" title="1st place" class="inline-block h-5 w-5">' : null;

            // Output the result
            $html .= HTML::p("Highest rank: $highestRank $highestRankImage (Timestamp: $highestRankTimestamp)", ['text-center']);
        } else {
            $html .= Alerts::danger("No rank data available.");
        }

        // Last Games
        $numberOfLastGames = 10;
        
        $main_id = $this->main_id ?? null;

        $html .= LastGames::display('rating_progression_season_' . $this->season . '_' . $this->region . '_' . $this->type, $this->accountid, $numberOfLastGames, $main_id);
        
        // Last 24 hours performance

        $html .= RatingOverHours::display($this->accountid, $this->region, $this->type, $this->season, 24, $main_id);

        $html .= OverallPerformance::display($this->accountid, $ratingData, $rankData, $main_id);

        // Calculate the real leaderboard link
        $leaderboardId = ($this->type === 'solo') ? 'battlegrounds' : 'battlegroundsduo'; // 'battlegrounds' or 'battlegroundsduo
        $seasonId = ($this->season === 7) ? 12 : 13;
        $page = $this->calculatePageNumber($currentRank);
        if ($page === 0 || $page === 1) {
            $page = 1;
        }

        $baseLeaderboardUrl = 'https://hearthstone.blizzard.com/en-us/community/leaderboards?region=' . strtoupper($this->region) . '&leaderboardId=' . $leaderboardId . '&season=' . $seasonId . '&page=' . $page;

        $html .= HTML::p('Check the player on the <a href="' . $baseLeaderboardUrl . '" target="_blank" rel="noopener noreferrer" class="text-blue-500 hover:underline">official leaderboard</a>.', ['text-center']);

        // Let's center the back button
        $html .= '<div class="w-full md:w-auto flex items-center my-4">';
            $html .= '<button class="back-button mx-auto py-3 px-5 leading-5 text-white bg-' . COLOR_SCHEME . '-500 hover:bg-' . COLOR_SCHEME . '-600 font-medium text-center focus:ring-2 focus:ring-' . COLOR_SCHEME . '-500 focus:ring-opacity-50 border border-transparent rounded-md shadow-sm">Go Back</button>';
            //$html .= HTML::mediumButtonLink('', 'Go Back', COLOR_SCHEME);
        $html .= '</div>';

        return $html;
    }

}
