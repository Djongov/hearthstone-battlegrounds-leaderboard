<?php

namespace Models\Api;

use App\Database\MYSQL;
use App\Logs\SystemLog;
use App\Exceptions\UserExceptions;

class User
{
    // Existence checks
    public function exists(string|int $param) : bool
    {
        // If the param is a string, we assume it's a username
        if (is_string($param)) {
            $exists = MYSQL::queryPrepared('SELECT `id` FROM `users` WHERE `username` = ?', [$param]);
        } else {
            $exists = MYSQL::queryPrepared('SELECT `id` FROM `users` WHERE `id` = ?', [$param]);
        }
        return ($exists->num_rows === 0) ? false : true;
    }
    // User get
    public function get(string|int $param) : array
    {
        if (!$this->exists($param)) {
            throw (new UserExceptions)->userNotFound();
        } else {
            if (is_string($param)) {
                $column = 'username';
            } else {
                $column = 'id';
            }
            $user = MYSQL::queryPrepared('SELECT * FROM `users` WHERE `' . $column . '` = ?', [$param]);
            return $user->fetch_assoc();
        }
    }
    // User Deleter
    public function delete(string|int $param) : bool
    {
        if (!$this->exists($param)) {
            throw (new UserExceptions)->userNotFound();
        } else {
            if (is_string($param)) {
                $column = 'username';
            } else {
                $column = 'id';
            }
            $delete = MYSQL::queryPrepared('DELETE FROM `users` WHERE `' . $column . '` = ?', [$param]);
            if ($delete->affected_rows === 0) {
                throw (new UserExceptions)->userNotDeleted();
            } else {
                SystemLog::write('User with ' . $column . ' ' . $param . ' deleted', 'User API');
                return true;
            }
        }
    }
    // User updater
    public function update(array $data, int $id) : bool
    {
        // First let's check if the user exists
        if (!$this->exists($id)) {
            throw (new UserExceptions)->userNotFound();
        }
        MYSQL::checkDBColumnsAndTypes($data, 'users');

        $sql = 'UPDATE `users` SET ';
        $updates = [];
        // Check if all keys in $array match the columns
        foreach ($data as $key => $value) {
            // Add the column to be updated to the SET clause
            $updates[] = "`$key` = ?";
        }
        // Combine the SET clauses with commas
        $sql .= implode(', ', $updates);

        // Add a WHERE clause to specify which organization to update
        $sql .= " WHERE `id` = ?";

        // Prepare and execute the query using queryPrepared
        $values = array_values($data);
        $values[] = $id; // Add the id for the WHERE clause

        $updateUser = MYSQL::queryPrepared($sql, $values);

        if ($updateUser->affected_rows === 0) {
            throw (new UserExceptions)->userNotUpdated();
        } else {
            SystemLog::write('User with id ' . $id . ' updated with ' . json_encode($data), 'User API');
            return true;
        }
    }
    // User creator
    public function create(array $data) : bool
    {
        // First check if the user exists
        if ($this->exists($data['username'])) {
            throw (new UserExceptions)->userAlreadyExists();
        }
        // Now let's check if the structure of the data matches the table
        MYSQL::checkDBColumnsAndTypes($data, 'users');

        $sql = 'INSERT INTO `users` (';
        $columns = [];
        $values = [];
        foreach ($data as $key => $value) {
            $columns[] = "`$key`";
            $values[] = '?';
        }
        $sql .= implode(', ', $columns) . ') VALUES (' . implode(', ', $values) . ')';

        $createUser = MYSQL::queryPrepared($sql, array_values($data));

        if ($createUser->affected_rows === 0) {
            SystemLog::write('User not created with ' . json_encode($data), 'User API');
            throw (new UserExceptions)->userNotCreated();
        } else {
            SystemLog::write('User created with ' . json_encode($data), 'User API');
            return true;
        }
    }
}
