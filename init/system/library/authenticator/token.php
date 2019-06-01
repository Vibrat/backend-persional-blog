<?php 
namespace Token;

class Token {
    /** @var String $token */
    public $token;

    /**
     * generate new token
     */
    public function createToken() {
        ## If true then save token into SESSION
        return $this->token = $_SESSION['token'] = $this->initToken(25);
    }

    /**
     * return a token
     */
    public function getToken() {
        ## check current flow
        if (isset($token)) {
            return $this->token;
        }

        ## check SESSION
        if ($_SESSION['token'] && time() <= $_SESSION['token_expire']) {
            return $this->token = $_SESSION['token'];
        }

        return false;
    }
    
    /**
     * check if Token is still alive
     * 
     * @var String $token
     */
    public function checkToken(String $token) {

        ## if token is provided
        if (isset($token)) {
            ## Check token here, please rememnber to save in SESSION
            return $token == $_SESSION['token'];
        }

        return false;
    }

    /**
     * init new Token
     * 
     * @return number $number
     */
    private function initToken($number) {
        ## setting expire to check every 2 hours
        $_SESSION['token_expire'] =  time() + (2 * 60 * 60);

        ## return token
        return $this->token = bin2hex(openssl_random_pseudo_bytes($number));
    }
}