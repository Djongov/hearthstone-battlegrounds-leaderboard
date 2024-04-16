<?php

namespace CSP;

use App\Database\MYSQL;

class Model
{
    public string $dbTable;
    
    public function addPolicy($policy, $description) : bool
    {
        // Add a new policy to the database
        $result = MYSQL::queryPrepared('INSERT INTO `csp_policies` (`policy`, `description`) VALUES (?, ?)', [$policy, $description]);
        if ($result->affected_rows === 0) {
            return false;
        }
        return true;
    }
    public function deletePolicy($id) : bool
    {
        // Delete a policy from the database
        $result = MYSQL::queryPrepared('DELETE FROM `csp_policies` WHERE `id`=?', [$id]);
        if ($result->affected_rows === 0) {
            return false;
        }
        return true;
    }
    public function getPolicies() : array
    {
        // Get all policies from the database
        $result = MYSQL::query('SELECT * FROM `csp_policies`');
        if ($result->num_rows === 0) {
            return false;
        }
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function getPolicyById($id) : array
    {
        // Get a single policy from the database
        $result = MYSQL::queryPrepared('SELECT * FROM `csp_policies` WHERE `id`=?', [$id]);
        if ($result->num_rows === 0) {
            return false;
        }
        return $result->fetch_assoc();
    }
    public function getPolicyByDomain($domain) : array
    {
        // Get a policy by domain from the database
        $result = MYSQL::queryPrepared('SELECT * FROM `csp_policies` WHERE `domain`=?', [$domain]);
        if ($result->num_rows === 0) {
            return false;
        }
        return $result->fetch_assoc();
    }
    public function updatePolicy($id, $policy, $description) : bool
    {
        // Update a policy in the database
        $result = MYSQL::queryPrepared('UPDATE `csp_policies` SET `policy`=?, `description`=? WHERE `id`=?', [$policy, $description, $id]);
        if ($result->affected_rows === 0) {
            return false;
        }
        return true;
    }
    public function addApprovedDomain($domain) : bool
    {
        // Add a new domain to the approved list
        $result = MYSQL::queryPrepared('INSERT INTO `csp_approved_domains` (`domain`) VALUES (?)', [$domain]);
        if ($result->affected_rows === 0) {
            return false;
        }
        return true;
    }
    public function deleteApprovedDomain($id)
    {
        // Delete a domain from the approved list
        
    }
}
