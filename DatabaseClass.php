<?php

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
    
    public function get_result($query) {
        $dbinfo = $this->connect->query($query);
        $Array = array();

        while ($row = mysqli_fetch_array($dbinfo)) {
            $Array[] = $row;
        }
        return $Array;
    }

    public function get_row() {
        
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

    public function firstID() {
        
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
    
    public function list_fields($query){
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

    public function delete() {
        
    }

    public function insert() {
        
    }

    public function update() {
        
    }
    
    /**
     * Disconnect from database
     * Calling from __destruct
     */
    
    public function disconnect() {
        mysqli_close($this->connect);
    }

}
