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