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
    public function isLogged() {
        return $this->isLogged;
    }

    /**
     * Check whether token is valid under specified permissions
     * 
     * @param String $token 
     * @param String $permission_url post/account/new
     * @return Boolean 
     */
    public function isTokenValid($token, $permission_url = false) {

        $permission_url = ($permission_url ? $permission_url : $_GET['api']);

        # check group permission
        $query_token =  $this->db->query(sprintf(
            AUTHENTICATOR_GET_USER_ID_BY_TOKEN,
            DB_PREFIX,
            $token
        ));

        $query_permission = $this->db->query(sprintf(
            AUTHENTICATOR_CHECK_PERMISSION,
            DB_PREFIX,
            DB_PREFIX,
            $user_id = $query_token->row('id')
        ));

        $permissions = json_decode($query_permission->row('permission'))->api;

        if (in_array($permission_url, ($permissions ? $permissions : [])) || $user_id == 1) {
           
            $query = $this->db->query(sprintf(
                AUTHENTICATOR_CHECK_TOKEN,
                DB_PREFIX, 
                $token)); 

            if ($query->row('total')) {
                return true;
            }   
        }
           
        return;
    }

    /**
     * Login Authentication
     * 
     * @param Array $credentials ['username'=> 'lam-nguyen', 'password' => '12312731562' ]
     */
    public function login(Array $credentials) {

        /** @var Response Connection */
        $row = $this->db->query(sprintf(
            AUTHENTICATOR_COUNT_USERS, 
            DB_PREFIX, 
            $credentials['username']))->row();
    
        if (password_verify($credentials['password'], $row['password'])) {
            
            $token = $this->tokener->createToken();
            $numTokens = $this->db->query(sprintf(
                AUTHENTICATOR_COUNT_TOKENS, 
                DB_PREFIX, 
                $row['id']))->row('total');
                
            if ($numTokens) {
                
                $this->db->query(sprintf(
                    AUTHENTICATOR_UPDATE_TOKENS, 
                    DB_PREFIX, 
                    $token, $row['id']));
                return $token;
            } 
                
            $this->db->query(sprintf(
                AUTHENTICATOR_INSERT_TOKENS, 
                DB_PREFIX, 
                $token, $row['id']));
            return $token;       
        }
    
        return false;
    }
}