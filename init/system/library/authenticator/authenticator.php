<?php
namespace Authenticator;

/**
 * Authenticator class for login
 *
 * Design by Lam Nguyen
 */
class Authenticator {
    /**
     * @var String $username
     * @var String $password
     * @var Bool $isLogged
     */
    private $username;
    private $password;
    public  $isLogged = false;
    /**
     * @var \Token\Token 
     */
    public  $tokener;
    
    /** @var \MysqliDatabase $db */
    private $db;

    function __construct(
        \MysqliDatabase $db, 
        \Token\Token $tokener) 
        {

        /** assign database connection for future use */
        $this->db = $db;

        ## save tokener into Authenticator
        if ($tokener instanceof \Token\Token) {
            $this->tokener = $tokener;
            $this->isLogged = ($this->tokener->token ? true : false) ;
        }
    }

    /**
     * Check if user already logged
     * 
     * @param  String $token
     * @return Bool
     */
    public function isLogged($token) {
        return $this->isLogged || $this->tokener->checkToken($token);
    }

    /**
     * get Token
     */
    public function getToken() {
        return $this->tokener->getToken();
    }

    /**
     * Login Authentication
     * 
     * @param Array $credentials ['username'=> 'lam-nguyen', 'password' => '12312731562' ]
     */
    public function login(Array $credentials) {
        
        ## call database's credentials
        $sqlCountUsers  = 'SELECT * FROM `%susers` WHERE username = "%s"';
        $sqlCountTokens = 'SELECT COUNT(*) AS total FROM `%susers_token` WHERE id = "%d"';
        $sqlUpdateToken = 'UPDATE `%susers_token` SET token = "%s" WHERE id = "%d"';
        $sqlInsertDb    = 'INSERT INTO `%susers_token` SET token = "%s", id = "%d"';

        /** @var Response Connection */
        $row = $this->db->query(sprintf($sqlCountUsers, DB_PREFIX, $credentials['username']))->row();
    
        if (password_verify($credentials['password'], $row['password'])) {
            
            $token = $this->tokener->createToken();
            $numTokens = $this->db->query(sprintf($sqlCountTokens, DB_PREFIX, $row['id']))->row('total');
                
            if ($numTokens) {
                
                $this->db->query(sprintf($sqlUpdateToken, DB_PREFIX, $token, $row['id']));
                return $token;
            } 
                
            $this->db->query(sprintf($sqlInsertDb, DB_PREFIX, $token, $row['id']));
            return $token;       
        }
    
        return false;
    }
}