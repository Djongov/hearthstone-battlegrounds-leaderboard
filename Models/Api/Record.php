<?php

declare(strict_types=1);

namespace Models\Api;

use App\Database\DB;

class Record
{
    public $rank;
    public $accountid;
    public $rating;
    public $table;

    public function __construct($rank, $accountid, $rating, $table)
    {
        $this->rank = $rank;
        $this->accountid = $accountid;
        $this->rating = $rating;
        $this->table = $table;
    }
    public function recordExist() : bool
    {
        $db = new DB();
        $pdo = $db->getConnection();
        $sql = "SELECT * FROM `$this->table` WHERE `accountid` = '$this->accountid'";
        $pdo->query($sql);
        $result = $pdo->query($sql);
        return $result->rowCount() > 0;
    }
    public static function getRecord($table, $accountid) : array
    {
        $db = new DB();
        $pdo = $db->getConnection();
        $sql = "SELECT * FROM `$table` WHERE `accountid` = '$accountid'";
        $pdo->query($sql);
        $result = $pdo->query($sql);
        if ($result->rowCount() == 0) {
            return [];
        }
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }
    public function createRecord() : string
    {
        $db = new DB();
        $pdo = $db->getConnection();
        $query = "INSERT INTO `$this->table` (`rank`, `accountid`, `rating`) VALUES ($this->rank,'$this->accountid',$this->rating)";
        if ($pdo->query($query)) {
            return "New record created successfully for " . $this->accountid . " with rating " . $this->rating . " and rank " . $this->rank;
        }
    }
    public function updateRecord() : string
    {
        $db = new DB();
        $pdo = $db->getConnection();
        $currentRecord = self::getRecord($this->table, $this->accountid);
        if ($currentRecord[0]['rating'] == $this->rating && $currentRecord[0]['rank'] == $this->rank) {
            return "Rating and rank for " . $this->accountid . " are the same, no need to update";
        }
        $query = "UPDATE `$this->table` SET `rating` = $this->rating, `rank` = $this->rank WHERE `accountid` = '$this->accountid'";
        $update = $pdo->query($query);
        if ($update->rowCount() > 0) {
            return "Record updated successfully for " . $this->accountid . ' with new rating ' . $this->rating . " and rank " . $this->rank . ". Was " . $currentRecord[0]['rating'] . " and rank " . $currentRecord[0]['rank'];
        } else {
            return "Nothing to update for " . $this->accountid . " with rating " . $this->rating . " and rank " . $this->rank;
        }
    }
    public function deleteRecord() : string
    {
        $db = new DB();
        $pdo = $db->getConnection();
        $query = "DELETE FROM `$this->table` WHERE `accountid` = '$this->accountid'";
        if ($pdo->query($query)) {
            return "Record deleted successfully for " . $this->accountid;
        } else {
            return "Did not delete record for " . $this->accountid;
        }
    }
    public function ratingAndRankAreTheSame() : bool
    {
        $record = self::getRecord($this->table, $this->accountid);
        return $record[0]['rating'] == $this->rating && $record[0]['rank'] == $this->rank;
    }
}
