<?php

/**
 * Account Model - Modify DB
 * 
 * Please do not use __construct here
 */
class AccountModel extends BaseModel
{

    /**
     * Check if account exists
     * 
     * @param String $username
     * @return Number number of records exist
     */
    public function checkAccount($username)
    {

        $sql = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "users` WHERE username = :username LIMIT 1";
        $query = $this->db->query($sql, [
            ':username' => $username
        ]);

        return $query->row('total');
    }

    /**
     * Create new Account
     *  
     * @param Array $data ['username' => 'lamnguyen', 'password' => '123456789' ]
     */
    public function createAccount($data)
    {

        $sql = "INSERT INTO `" . DB_PREFIX . "users` SET username = '" . $data['username'] . "', password = '" . password_hash($data['password'], PASSWORD_BCRYPT) . "'";
        $query = $this->db->query($sql);

        ## return query of affected rows
        return $query->rowsCount();
    }

    /**
     * Get an account information based on username
     * 
     * @param String $username
     */
    public function getAccount(String $username) {
        if (isset($username) && is_String($username)) {
            $sql_account  = "SELECT u.`id`, u.`username`, g.`name` AS `groupname` FROM `" . DB_PREFIX . "users` u";
            $sql_account .= " LEFT JOIN `" . DB_PREFIX . "users_permission` p ON (u.id = p.user_id)" .  
                            " LEFT JOIN `" . DB_PREFIX . "users_group` g  ON (p.group_permission_id = g.id)";
            $sql_account .= " WHERE u.username = :username";

            $query = $this->db->query($sql_account, [
                ':username' => $username
            ]);

            return [
                'success' => true,
                'data'    => $query->row()
            ];
        }

        return [
            'success'   => false,
            'data'      => []
        ];
    }

    /**
     * List users
     * 
     * @param offset optional default 0
     * @param limit optional default 100
     * @param group optional default *
     */
    public function listAccounts($data)
    {

        $bind_params = [];
        if (!isset($data['offset']) || !is_numeric($data['offset'])) {
            $data['offset'] = 0;
        }

        if (!isset($data['limit']) || !is_numeric($data['limit'])) {
            $data['limit'] = 100;
        } elseif ($data['limit'] >= 1000) {
            return [
                'success'   => false,
                'message'   => 'Parameter `limit` has a maximum value of `1000`'
            ];
        }

        if (!isset($data['group']) || !is_string($data['group'])) {
            $data['group'] = false;
        }

        if ($data['group']) {
            $bind_params[':group'] = $data['group'];
        }

        $sql  = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "users` u";
        $sql .= " LEFT JOIN `" . DB_PREFIX . "users_permission` p ON (u.id = p.user_id)" .  
                        " LEFT JOIN `" . DB_PREFIX . "users_group` g  ON (p.group_permission_id = g.id)";
        $sql .= ($data['group'] ? " WHERE g.`name` = :group" : "");
        $sql .= " GROUP BY u.`id`, u.`username`";

        $query = $this->db->query($sql, $bind_params);
        $total = $query->row('total');

        $sql_account = "SELECT u.`id`, u.`username`, JSON_OBJECTAGG(IFNULL(g.id, '_'), IFNULL(g.name, '_')) AS `groupname` FROM `" . DB_PREFIX . "users` u";
        $sql_account .= " LEFT JOIN `" . DB_PREFIX . "users_permission` p ON (u.id = p.user_id)" .  
                        " LEFT JOIN `" . DB_PREFIX . "users_group` g  ON (p.group_permission_id = g.id)";
        $sql_account .= ($data['group'] ? " WHERE g.`name` = :group" : "");
        $sql_account .= " GROUP BY u.`id`, u.`username`";
        $sql_account .= " LIMIT " . $data['offset'] . ", " . $data['limit'] . "";

        $query = $this->db->query($sql_account, $bind_params);
        $accounts = $query->rows();

        // Reformat Data
        $accounts = array_map(function($item) {
            $item['groupname'] = json_decode($item['groupname']);
            
            // Unset default value getting from mysql JSON_OBJECTAGG
            unset($item['groupname']->_);
            return $item;
        }, $accounts);

        return [
            'success' => true,
            'code'    => 200,
            'data'    => $accounts,
            'total'   => $total,
            'offset'  => $data['offset'],
            'limit'   => $data['limit']    
        ];
    }


    public function deleteAccount(String $username) {

        if ($username == 'root') {
            return [
                'success'   => false,
                'message'   => 'Cannot delete user `root`'
            ];
        }

        $sql = "DELETE FROM `" . DB_PREFIX . "users` WHERE `username` = :username LIMIT 1";
        $query = $this->db->query($sql, [
            ':username'     => $username
        ]);

        $rows_affected = $query->rowsCount();

        return [
            'success'   =>  $rows_affected ? true : false,
            'affected_rows' => $rows_affected
        ];
    }
}
