<?php
/**
 * Account Model - Modify DB
 * 
 * Please do not use __construct here
 */
class AccountModel extends BaseModel {
   
    /**
     * Check if account exists
     * 
     * @param String $username
     * @return Number number of records exist
     */
    public function checkAccount($username) {
        
        $sql = "SELECT COUNT(*) as total FROM `" . DB_PREFIX ."users` WHERE username = '" . $username . "'";
        $query = $this->db->query($sql);
        
        return $query->row('total');
    }

    /**
     * Create new Account
     *  
     * @param Array $data ['username' => 'lamnguyen', 'password' => '123456789' ]
     */
    public function createAccount($data) {
       
        $sql = "INSERT INTO `" . DB_PREFIX ."users` SET username = '" . $data['username'] . "', password = '" . password_hash($data['password'], PASSWORD_BCRYPT) . "'";
        $query = $this->db->query($sql);

        ## return query of affected rows
        return $query->rowsCount();       
    }
}