<?php
namespace Database;

/**
 * Class which handles response return from Database
 *
 * @Flow\Scope(")
 */
 class DbResponse {

    /**
     * Query Connection
     * @var PDOStatement
     */
    public $query;

    /**
     * Database Connection
     *
     * @var PDO | boolean
     */
    public $connection;

    /**
     * Initiate Connection, assign PDOStatement to a class
     *
     * @param PDOStatement
     */
    public function initDataConnection($query, $connection = false) {
        $this->query = $query;
        $this->connection = $connection;
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
        $row = $this->query->fetch(\PDO::FETCH_GROUP|\PDO::FETCH_ASSOC);

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
        return ($this->query ? $this->query->fetchAll(\PDO::FETCH_ASSOC) : false);
    }

    /**
     * Return number of records
     */
    public function rowsCount() {
        return ($this->query ? $this->query->rowCount() : false);
    }

    /**
     * Get last inserted id generated by AUTO_INCREMENT
     * This will return:
     *    - 0 if the query does not generate AUTO_INCREMENT
     *    - numerial id  if the query generates AUTO_INCREMENT
     *    - false if no connection is made
     * @return Number | false
     */
    public function lastInsertId() {
        return ($this->connection ? $this->connection->lastInsertId() : false);
    }
 }
