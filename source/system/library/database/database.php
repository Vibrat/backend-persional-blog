<?php

/**
 * Database Connection created by Lam Nguyen
 *
 * Contact:
 * Lam Nguyen
 * Mail: lam.nguyen.mr@outlook.com
 */

require_once "response.php";

use Database\DbResponse as Db;

/**
 * Connect to database using PDO
 */
class MySqliDatabase
{

    /**
     * Config Store Credentials
     *
     *
     * @param Array [
     *      'username' => String,
     *      'password' => String,
     *      'db'       => String,
     *      'port'     => String,
     *      'url'      => String,
     * ]
     */
    private $config;

    /**
     * Response Handler
     *
     * @var Db
     */
    private $response;

    /**
     * Active Database Connection
     *
     * @var PDO
     */
    private $conn;

    /**
     * Passing credentials to config
     */
    function __construct(array $config)
    {
        /** config and handler */
        $this->config = $config;
        $this->response = new Db();
        $this->conn = $this->connect();
    }

    /**
     * Query values from database
     *
     *  - This method supports both absolute string query and params binding queries.
     *    For a security solution please pass values into $data
     *  - If you just want to query a string without params, leave $data as empty
     *
     * @param String SQL sql query statement
     * @param Array $data  data for params binding
     */
    public function query(String $sql, array $data = [])
    {

        ## Start connection
        if (!$this->conn) {
            $this->conn = $this->connect($sql);
        }

        ## Execute non-param statement
        if (empty($data) && $this->conn && $query = $this->conn->query($sql)) {
            $this->response->initDataConnection($query, $this->conn);
        }

        ## binding to value to prevent XSS
        if ($this->conn && !empty($data)) {
            $cursor = $this->conn->prepare($sql);
            if ($cursor->execute($data)) {
                $this->response->initDataConnection($cursor, $this->conn);
            }
        }

        ## close connection to database
        $this->close($this->conn);

        return $this->response;
    }


    /**
     * Start Connection to database
     *
     * @return PDO
     * @throws Bool
     */
    private function connect()
    {
        /** @var PDO default to false */
        $conn = false;

        try {
            ## making connection to database
            $conn = new PDO(
                "mysql:host=" . $this->config['url'] . ";port=" . $this->config['port']  . ";dbname=" . $this->config['db'],
                $this->config['username'],
                $this->config['password']
            );

            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            ## handle errors
            return false;
        }

        return $conn;
    }

    /**
     * Close Connection to database
     *
     * @param PDO $conn
     */
    private function close($conn)
    {
        if ($conn) $conn = null;
    }

    /**
     * Begin Transaction
     *
     * @see https://www.php.net/manual/en/pdo.begintransaction.php
     */
    public function beginTransaction()
    {
        $this->conn->beginTransaction();
    }

    /**
     * Commit Transaction
     *
     * @see https://www.php.net/manual/en/pdo.commit.php
     */
    public function commit()
    {
        $this->conn->commit();
    }

    /**
     * Rollback Transaction
     *
     * @see https://www.php.net/manual/en/pdo.rollback.php
     */
    public function rollBack()
    {
        $this->conn->rollBack();
    }
}
