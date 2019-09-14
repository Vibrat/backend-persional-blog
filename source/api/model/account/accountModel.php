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
        foreach($parameters as $param) {
            if (empty($data[$param])) {
                return [
                    'success'   => false,
                    'code'      => 'DB_ACCOUNT_MODEL_PARAM',
                    'message'   => "Parameter $param does not exist or empty"
                ];
            }
        }

        # Check if old-password is correct
        $new_hash_pwd = password_hash($data['new-password'], PASSWORD_BCRYPT);
        $db_user_pwd = "SELECT password FROM `" . DB_PREFIX . "users` WHERE username = :username LIMIT 1";
        $result_db_user_pwd = $this->db->query($db_user_pwd, [
            ':username' => $data['username']
        ])->row('password');
            
        if (!$result_db_user_pwd) {
            return [
                'success'   => false,
                'code'      => 'DB_ACCOUNT_MODEL_QUERY',
                'message'   => 'There is no account for ' . $data['username']
            ];
        }

        # Start change password md5
        if (password_verify($data['old-password'], $result_db_user_pwd)) {
            
            $update_pwd = "UPDATE `" . DB_PREFIX . "users` SET password = :password WHERE username = :username";
            $affected_rows = $this->db->query($update_pwd, [
                ':username' => $data['username'],
                ':password' => $new_hash_pwd
            ])->rowsCount();

            if ($affected_rows > 0 ) {
                return [
                    'success'   => true,
                    'message'   => 'Updated password for username ' . $data['username']
                ];
            } else {
                return [
                    'success'   => false,
                    'code'      => 'DB_ACCOUNT_MODEL_QUERY',
                    'message'   => 'There is an error inserting data into database'
                ];
            }
        }

        return [
            'success'   => false,
            'code'      => 'DB_ACCOUNT_MODEL_RECORD',
            'message'   => 'Password is incorrect'
        ];
    } 

    /**
     * Change password from Admin Role
     * 
     * @param username
     * @param new-password
     */
    public function changePasswordByAdmin(Array $data) {
        
        # Check if data exists
        $params = ['username', 'new-password'];
        foreach($params as $param) {
            if (empty($data[$param])) {
                return [
                    'success'   => false,
                    'code'      => 'DB_ACCOUNT_MODEL_PARAM',
                    'message'   => "Param $param does not exist"
                ];
            }
        }

        # Check if username exists
        $sql_username = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "users` WHERE username = :username LIMIT 1";
        $is_username_exist = $this->db->query($sql_username, [
            ':username'     => $data['username']
        ])->row('total');

        if ($is_username_exist) {
            
            $sql_update_password = "UPDATE `" . DB_PREFIX . "users` SET password = :password WHERE username = :username LIMIT 1";
            $affected_rows = $this->db->query($sql_update_password, [
                ':password' => password_hash($data['new-password'], PASSWORD_BCRYPT),
                ':username' => $data['username']
            ])->rowsCount();
            
            if ($affected_rows) {
                return [
                    'success'   => true,
                    'message'   => 'Updated password for user ' . $data['username']
                ];
            }    

            return [
                'success'   => false,
                'code'      => 'DB_ACCOUNT_MODEL_QUERY',
                'message'   => 'No account matched to be executed'
            ];
        }

        return [
            'success'   => false,
            'code'      => 'DB_ACCOUNT_MODEL_QUERY',
            'message'   => 'Account does not exist'
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

        if (!isset($data['name']) || empty($data['name']) || !is_string($data['name'])) {
            $data['name']   = false;
        }

        if (!isset($data['group']) || !is_string($data['group'])) {
            $data['group'] = false;
        }

        if ($data['group']) {
            $bind_params[':group'] = $data['group'] . '%';
        }

        if ($data['name']) {
            $bind_params[':name'] = $data['name'] . '%';
        }

        # Query Statistics
        $sql  = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "users` u";
        $sql .= " LEFT JOIN `" . DB_PREFIX . "users_permission` p ON (u.id = p.user_id)" .  
                        " LEFT JOIN `" . DB_PREFIX . "users_group` g  ON (p.group_permission_id = g.id) WHERE 1 = 1";
        $sql .= ($data['group'] ? " AND g.`name` LIKE :group" : "");
        $sql .= ($data['name'] ?  " AND u.`username` LIKE :name" : "");

        $query = $this->db->query($sql, $bind_params);
        $total = $query->row('total');

        # Query Data
        $sql_account = "SELECT u.`id`, u.`username`, JSON_OBJECTAGG(IFNULL(g.id, '_'), IFNULL(g.name, '_')) AS `group` FROM `" . DB_PREFIX . "users` u";
        $sql_account .= " LEFT JOIN `" . DB_PREFIX . "users_permission` p ON (u.id = p.user_id)" .  
                        " LEFT JOIN `" . DB_PREFIX . "users_group` g  ON (p.group_permission_id = g.id) WHERE 1 = 1";
        $sql_account .= ($data['group'] ? " AND g.`name` LIKE :group" : "");
        $sql_account .= ($data['name'] ?  " AND u.`username` LIKE :name" : "");
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
