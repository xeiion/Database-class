<?php

/* ------------------------------------------------------------------------------
 * * File:	DatabaseClass.php
 * * Class:       mysqli class
 * * Description:	PHP MySQLi query operator
 * * Version:		1.0
 * * Updated:     18-Feb-2013
 * * Author:	Jack petersen
 * * Homepage:	jack-petersen.com
 * *------------------------------------------------------------------------------ */

class DatabaseClass {

    private $connect;
    private $settings = array(
        'host' => 'localhost',
        'user' => 'root',
        'password' => '',
        'database' => 'jack',
        'charset' => 'utf8'
    );

    public function __construct() {
        $this->connect = new mysqli($this->settings['host'], $this->settings['user'], $this->settings['password'], $this->settings['database']);
        $this->connect->set_charset($this->settings['charset']);
    }

    public function __destruct() {
        $this->disconnect();
    }

    /**
     * Return array from result of database
     *
     * @access public
     * @param string
     * @return array
     *
     */
    public function get_result($query, $Array = array()) {
        $dbinfo = $this->connect->query($query);
       
        while ($row = mysqli_fetch_array($dbinfo)) {
            $Array[] = $row;
        }
        return $Array;
    }

    /**
     * Return last id from query
     *
     * @access public
     * @param string
     * @return int
     *
     */
    public function lastInsertID() {
        return mysqli_insert_id($this->connect);
    }

    /**
     * Count number of rows found matching a specific query
     *
     * @access public
     * @param string
     * @return int
     *
     */
    public function num_rows($query) {
        $querys = $this->connect->query($query);

        return $querys->num_rows;
    }

    /**
     * Count number of table fields found matching a specific query
     *
     * @access public
     * @param string
     * @return int
     *
     */
    public function num_field($query) {
        $querys = $this->connect->query($query);

        return mysqli_num_fields($querys);
        mysqli_free_result($querys);
    }

    public function list_fields($query) {
        $querys = $this->connect->query($query);
        return mysqli_fetch_fields($querys);
        mysqli_free_result($querys);
    }

    /**
     * Sanitize user data
     *
     * @access public
     * @param string, array
     * @return string, array
     *
     */
    public function filter($variable = null) {
        if (is_array($variable)) {
            foreach ($variable as $row) {

                $dataPart = $this->connect->real_escape_string($row);
                $dataPart = trim(htmlentities($row, ENT_QUOTES, 'UTF-8'));
                $data[] = $dataPart;
            }
        } else {
            $data = $this->connect->real_escape_string($variable);
            $data = trim(htmlentities($data, ENT_QUOTES, 'UTF-8'));
        }
        return $data;
    }

    /**
     * Delete data from table
     *
     * @access public
     * @param string table name
     * @return bool
     *
     */
    public function delete($table, $where = array(), $limit = '') {
        $query = "DELETE FROM" . $table;

        foreach ($where as $row => $value) {
            $wheres[] = "$row = '$value'";
        }
        $query .= " WHERE " . implode(' AND ', $wheres);

        if (!empty($limit)) {
            $query .= " LIMIT " . $limit;
        }

        $this->connect->query($query);

        if (mysqli_error($this->connect)) {
            printf(mysqli_error($this->connect));
            return false;
        } else {
            return true;
        }
    }
    
      /**
     * Insert data from table
     *
     * @access public
     * @param string table name
     * @return bool
     *
     */

    public function insert($table, $var = array()) {
        $query = "INSERT INTO " . $table;
        $value = array();
        $item = array();
        foreach ($var as $row => $values) {
            $item[] = $this->filter($row);
            $value[] = "'$values'";
        }

        $item = ' (' . implode(', ', $item) . ')';
        $value = '(' . implode(', ', $value) . ')';

        $query .= $item . ' VALUES ' . $value;

        $query = mysqli_query($this->connect, $query);

        if (mysqli_error($this->connect)) {
            printf(mysqli_error($this->connect));
            return false;
        } else {
            return true;
        }
    }

    /**
     * Update user data
     *
     * @access public
     * @param string, array
     * @return string, array
     * @return limit
     * @return where
     *
     */
    public function update($table, $var = array(), $where = array(), $limit = '') {
        $query = "UPDATE " . $table . " SET ";

        foreach ($var as $row => $value) {
            $update[] = "$row = '$value'";
        }
        $query .= implode(', ', $update);

        foreach ($where as $row => $value) {
            $wheres[] = "$row = '$value'";
        }

        $query .= ' WHERE ' . implode(' AND ', $wheres);

        if (!empty($limit)) {
            $query .= ' LIMIT ' . $limit;
        }

        $this->connect->query($query);

        if (mysqli_error($this->connect)) {
            printf(mysqli_error($this->connect));
            return false;
        } else {
            return true;
        }
    }

    /**
     * check if database table exist
     *
     * @access public
     * @param string
     * @return bool
     *
     */
    public function table_exist($table) {
        $checkTable = $this->connect->query("SELECT * FROM $table LIMIT 1");

        if ($checkTable) {
            return true;
        } else {
            return false;
        }
        mysqli_free_result($checkTable);
    }

    /**
     * Disconnect from database
     * Calling from __destruct
     */
    public function disconnect() {
        mysqli_close($this->connect);
    }

}
