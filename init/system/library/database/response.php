<?php
namespace Database;

/**
 * Class which handles response return from Database
 * 
 * @Flow\Scope(")
 */
 class DbResponse {

    /**
     * Database Connection
     * @var PDOStatement
     */
    public $query;


    /**
     * Initiate Connection, assign PDOStatement to a class
     * 
     * @param PDOStatement
     */
    public function initDataConnection($query) {
        $this->query = $query;
    }

    /**
     * Return a next row value from results
     * 
     * @param String index of a row
     * @return (Array || Value)
     */
    public function row($index = false) {

        if(!$this->query) {
            return;
        }
            
        /** @var PDOStatement $row */
        $row = $this->query->fetch();

        /** Return value if index exists */
        if ($index) {
            return $row[$index];
        }

        /** otherwise return row */
        return $row;
    }

    /**
     * Return all records sorted by row
     */
    public function rows() {
        /** return row of values */
        return ($this->query ? $this->query->fetchAll() : false);
    }

    /**
     * Return number of records
     */
    public function rowsCount() {
        return ($this->query ? $this->query->rowCount() : false);
    }
 }