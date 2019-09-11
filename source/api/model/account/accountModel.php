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
     * Change password
     * 
     * @param username
     * @param old-password
     * @param new-password
     */
    public function changePassword(Array $data) {
        
        # Check if parameters exist
        $parameters = ['username', 'old-password', 'new-password'];
        foreach ($data as $key => $item) {
            if (!in_array($key, $parameters)) {
                return [
                    'success'   => false,
                    'code'      => 'DB_ACCOUNT_MODEL_PARAM',
                    'message'   => "Parameter . $item .  does not exist"
                ];
            }
        }

        # Check if old-password is correct
        $hash_pwd = md5($data['old-password']);
        $count_pwd = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "users` WHERE username = :username AND password = :password LIMIT 1";
        $result_count_pwd = $this->db->query($count_pwd, [
            ':username' => $data['username'],
            ':password' => $hash_pwd
        ])->row('total');

        # Start change password md5
        if ($result_count_pwd > 0 ) {
            
            $update_pwd = "UPDATE `" . DB_PREFIX . "users` SET password = :password WHERE username = :username";
            $affected_rows = $this->db->query($update_pwd, [
                ':username' => $data['username'],
                ':password' => $hash_pwd
            ])->rowsCount();

            if ($affected_rows > 0 ) {
                return [
                    'success'   => true,
                    'message'   => 'Updated password for username ' . $data['username']
                ];
            } else {
                return [
                    'success'   => false,
                    'code'      => 'DB_ACCOUNT_MODEL_OPERATOR',
                    'message'   => 'There is an error inserting data into database'
                ];
            }
        }

        return [
            'success'   => false,
            'code'      => 'DB_ACCOUNT_MODEL_RECORD',
            'message'   => 'Record does not exist in database'
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

        # Query Statistics
        $sql  = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "users` u";
        $sql .= " LEFT JOIN `" . DB_PREFIX . "users_permission` p ON (u.id = p.user_id)" .  
                        " LEFT JOIN `" . DB_PREFIX . "users_group` g  ON (p.group_permission_id = g.id)";
        $sql .= ($data['group'] ? " WHERE g.`name` = :group" : "");
        $sql .= " GROUP BY u.`id`, u.`username`";

        $query = $this->db->query($sql, $bind_params);
        $total = $query->row('total');

        # Query Data
        $sql_account = "SELECT u.`id`, u.`username`, JSON_OBJECTAGG(IFNULL(g.id, '_'), IFNULL(g.name, '_')) AS `group` FROM `" . DB_PREFIX . "users` u";
        $sql_account .= " LEFT JOIN `" . DB_PREFIX . "users_permission` p ON (u.id = p.user_id)" .  
                        " LEFT JOIN `" . DB_PREFIX . "users_group` g  ON (p.group_permission_id = g.id)";
        $sql_account .= ($data['group'] ? " WHERE g.`name` = :group" : "");
        $sql_account .= " GROUP BY u.`id`, u.`username`";
        $sql_account .= " LIMIT " . $data['offset'] . ", " . $data['limit'] . "";

        $query = $this->db->query($sql_account, $bind_params);
        $accounts = $query->rows();

        # Reformat Data
        $accounts = array_map(function($item) {
            $item['group'] = json_decode($item['group']);
            
            # Unset default value getting from mysql JSON_OBJECTAGG
            unset($item['group']->_);
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
