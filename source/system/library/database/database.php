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
class MySqliDatabase {
    
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
     * Passing credentials to config
     */
    function __construct(Array $config) {
        /** config and handler */
        $this->config = $config;
        $this->response = new Db();
    }

    /**
     * Query values from database
     * 
     * @param String SQL statements
     */
    public function query(String $sql) {
        
        ## Start connection
        $conn = $this->connect($sql); 

        ## Add response to hanlder
        
        if ($conn && $query = $conn->query($sql)) {
            $this->response->initDataConnection($query);
        }

        ## close connection to database
        $this->close($conn);
        
        return $this->response;
    }


    /**
     * Start Connection to database
     * 
     * @return PDO
     * @throws Bool
     */
    private function connect() {
        /** @var PDO default to false */
        $conn = false;

        try {
            ## making connection to database
            $conn = new PDO("mysql:host=" . $this->config['url'] . ";port=" . $this->config['port']  . ";dbname=" .$this->config['db'], 
            $this->config['username'], 
            $this->config['password']);

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
    private function close($conn) {
        if ($conn) $conn = null;     
    }
}