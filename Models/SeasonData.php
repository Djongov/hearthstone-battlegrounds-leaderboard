<?php

namespace Models;

use App\Database\DB;

class SeasonData
{
    public int $season;
    public string $region;
    public string $type;
    public function __construct($season, $region, $type)
    {
        $this->season = $season;
        $this->region = $region;
        $this->type = $type;
    }
    public function getTitle() : string
    {
        $title = strtoupper($this->region) . ' battlegrounds Season ' . $this->season . ' Leaderboard';
        if ($this->season === 7) {
            $title = ucfirst($this->type) . ' ' . strtoupper($this->region) . ' battlegrounds Season ' . $this->season . ' Leaderboard';
        }
        return $title;
    }
    public function getTable() : string
    {
        $table = 'battlegrounds_season_' . $this->season . '_' . $this->region;
        if ($this->season === 7) {
            $table .=  '_' . $this->type;
        }
        return $table;
    }
    public function getDescription() : string
    {
        return 'This is a searchable (almost) live data of the ' . ucfirst($this->type) . ' Hearthstone Season ' . $this->season . ' Leaderboard for the ' . strtoupper($this->region) . ' region';
    }
    public function getData() : array
    {
        $table = $this->getTable();
        $db = new DB();
        $pdo = $db->getConnection();
        $sql = "SELECT `rank`, `accountid`, `rating` FROM `$table`";
        $pdo->query($sql);
        $result = $pdo->query($sql);
        if ($result->rowCount() == 0) {
            return [];
        }
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }
}
