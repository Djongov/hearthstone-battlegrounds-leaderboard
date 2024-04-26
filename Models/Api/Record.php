<?php

declare(strict_types=1);

namespace Models\Api;

use App\Database\DB;
use Google\Service\Transcoder\Progress;
use Models\ProgressionRank;
use Models\ProgressionRating;

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
    public static function getRecordByAccountId($table, $accountid) : array
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
    public static function getRecordByRank($table, $rank) : array
    {
        $db = new DB();
        $pdo = $db->getConnection();
        $sql = "SELECT * FROM `$table` WHERE `rank` = $rank";
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
        $currentRecord = self::getRecordByAccountId($this->table, $this->accountid);
        if ($currentRecord[0]['rating'] == $this->rating && $currentRecord[0]['rank'] == $this->rank) {
            return "Rating and rank for " . $this->accountid . " are the same, no need to update";
        }
        
        $table = explode('_', $this->table);
        $season = (int) $table[2];
        $region = $table[3];
        $type = $table[4];
        // See errors when i update the record with a huge difference in rating and rank. I need to prevent this and get warned when it attempts to happen
        if ($currentRecord[0]['rating'] - $this->rating > 450 || $this->rating - $currentRecord[0]['rating'] > 450) {
            $message = "Rating difference is too high for " . $this->accountid . ". Current rating is " . $currentRecord[0]['rating'] . " and new rating is " . $this->rating . ". Region is " . $region . " and type is " . $type . ". In table " . $this->table . ".";
            $to = [
                [
                    'email' => 'djongov@gamerz-bg.com',
                    'name' => 'Dimitar Dzhongov'
                ]
            ];
            \App\Mail\Send::send($to, 'Rating difference is too high', $message);
            return $message;
        }
        $query = "UPDATE `$this->table` SET `rating` = $this->rating, `rank` = $this->rank WHERE `accountid` = '$this->accountid'";
        $update = $pdo->query($query);
        if ($update->rowCount() > 0) {
            // We need to derive the region and type from the table name
            // Only update progression tables if the rating or rank has changed
            //if ($currentRecord[0]['rating'] != $this->rating) {
                ProgressionRank::add($this->accountid, $region, $type, $this->rank, $season);
            //}
            //if ($currentRecord[0]['rank'] != $this->rank) {
                ProgressionRating::add($this->accountid, $region, $type, $this->rating, $season);
            //}
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
}
