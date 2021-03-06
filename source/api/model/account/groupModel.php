<?php

use \System\Model\BaseModel;

/**
 * Class Model for Group Permission
 *
 * @method getSummary() return total of records
 * @method newGroup() create new permission group
 * @method listGroup() list data
 * @method countGroup() count number of group based on group_name
 * @method listPermissions($id) list permissions in table users_permission based on group_id
 * @method addUserToGroup ($payload) add a user into group permissions
 */

class GroupModel extends BaseModel
{

    /**
     * return total of records
     *
     * @return number or string
     * @access public
     */
    public function getSummary($groupname)
    {

        if (isset($groupname) && !is_string($groupname)) {
            throw new \Exception("`groupname` is not type string");
        }

        $sql = "SELECT COUNT(id) AS total FROM `" . DB_PREFIX . "users_group`";
        $sql .=  isset($groupname) ? " WHERE  `name` LIKE :groupname" : "";
        return $this->db->query($sql, [
            ':groupname'    => "%" . $groupname . "%"
        ])->row('total');
    }

    /**
     * create new record in table users_group
     *
     * @param string[] properties:'name' & 'permission'
     * @return int
     * @access public
     */
    public function newGroup(array $data)
    {

        $sql = "INSERT INTO `" . DB_PREFIX . "users_group` SET name = '" . $data['name'] . "', permission = '" . $data['permission'] . "'";
        return $this->db->query($sql)->rowsCount();
    }

    /**
     * list records from table users_group
     *
     * @param array properties: 'offset', 'limit'
     * @access public
     */
    public function listGroups($data)
    {

        if (
            is_numeric($data['offset']) &&
            is_numeric($data['limit'])
        ) {

            $sql =  "SELECT * FROM `" . DB_PREFIX . "users_group`";
            $sql .= isset($data['groupname']) ? " WHERE  `name` LIKE :groupname" : "";
            $sql .= " LIMIT " . (int) $data['offset'] . ", " . (int) $data['limit'] . "";
            return $this->db->query($sql, isset($data['groupname']) ? [
                ':groupname' => "%" . $data['groupname'] . "%"
            ] : [])->rows();
        }

        throw new \Exception("offset and limit values are in correct");
    }

    /**
     * Check if a group exists
     *
     * @param group
     */
    public function isGroupExist($data) {
        if (!isset($data['group']) && !is_string($data['group'])) {
            return [
                'success'   => false,
                'code'      => 'MODEL_ACCOUNT_GROUP',
                'message'   => 'parameter `group does not exist`'
            ];
        }

        $sql = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "users_group` WHERE name = :group LIMIT 1";
        $result = $this->db->query($sql, [
            ':group'    => $data['group']
        ])->row('total');

        return [
            'success'   => true,
            'total'     => $result
        ];
    }

    /**
     * count number of records in table 'users_group'
     *
     * @param string $group_name name of group to search
     * @return int number of records
     * @access public
     */
    public function countGroup(String $group_name)
    {

        $sql = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "users_group` WHERE name = '" . $group_name . "'";
        return $this->db->query($sql)->row('total');
    }

    /**
     * List Permissions of a Group
     *
     * @param int id
     * @return string[] permissions list
     * @access public
     */
    public function listPermissions($id)
    {
        $query = $this->db->query("SELECT permission FROM `" . DB_PREFIX . "users_group` WHERE id  = '" . (int) $id . "'");

        return $query->row('permission');
    }

    /**
     * Add a user to group (insert into table users_permission)
     *
     * @param $string[] $payload contains 'groupId', 'userId'
     * @return boolean
     * @access public
     */
    public function addUserToGroup($payload)
    {

        $sql_group_id = "SELECT COUNT(*) as total  FROM `" . DB_PREFIX . "users_group` WHERE id = '" . $payload['groupId'] . "'";
        $sql_num_permissions = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "users_permission` WHERE user_id = '" . $payload['userId'] . "' AND group_permission_id = '" . $payload['groupId'] . "'";
        $sql_insert_permissions = "INSERT INTO `" . DB_PREFIX . "users_permission` SET user_id = '" . $payload['userId'] . "', group_permission_id = '" . $payload['groupId'] . "'";

        if ($this->db->query($sql_group_id)->row('total')) {

            if (!$this->db->query($sql_num_permissions)->row('total')) {

                $query_add = $this->db->query($sql_insert_permissions);

                if ($query_add->rowsCount()) {
                    return true;
                }
            }

            return false;
        }
    }

    /**
     * Add a user to group (insert into table users_permission)
     *
     * @param string[] userId, name as groupname
     * @return number | false lastInsertId that is generated by AUTO_INCREMENT
     */
    public function addUserToGroupByGroupName($payload)
    {
        $sql_group_id = "SELECT `id` FROM `" . DB_PREFIX . "users_group` WHERE name = '" . $payload['groupname'] . "'";
        $sql_num_permissions = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "users_permission` WHERE user_id = '" . $payload['userId'] . "' AND group_permission_id = :groupId";
        $sql_insert_permissions = "INSERT INTO `" . DB_PREFIX . "users_permission` SET user_id = '" . $payload['userId'] . "', group_permission_id = :groupId";

        $group_id = $this->db->query($sql_group_id)->row('id');
        if (isset($group_id) || $group_id === 0) {
            $query = $this->db->query($sql_num_permissions, [
                ':groupId' => $group_id
            ]);
            $total_permissions = $query->row('total');
            if (!$total_permissions) {

                $query_add = $this->db->query($sql_insert_permissions, [
                    ':groupId'  => $group_id
                ]);

                if ($query_add->rowsCount()) {
                    return $query_add->lastInsertId();
                }
            }

            return false;
        }
    }


    /**
     * Remove a user from a group by groupname and userId
     *
     * @param Number userId
     * @param String groupname
     * @return [
     *      'success'   => boolean,
     *      'message'   => string
     * ]
     */
    public function removeUserFromGroupByName(array $data)
    {

        // Validate userId
        if (empty($data['userId']) || !is_numeric($data['userId'])) {
            return [
                'success'   => false,
                'message'   => '`userId` is not number'
            ];
        }

        // Validate groupname
        if (empty($data['groupname']) || !is_string($data['groupname'])) {
            return [
                'success'   => false,
                'message'   => '`groupname` is not string'
            ];
        }

        // remove record into databases
        $sql = "DELETE p FROM `" . DB_PREFIX . "users_permission` AS p"
            . " LEFT JOIN `" . DB_PREFIX . "users_group` AS g ON (p.group_permission_id = g.id)"
            . " WHERE p.user_id = :userId AND g.name = :groupname";

        $query = $this->db->query($sql, [
            ':userId'       => $data['userId'],
            ':groupname'    => $data['groupname']
        ]);

        // Get number of affected rows
        $num_affected_rows = $query->rowsCount();
        return  [
            'success'   => $num_affected_rows != 0,
            'message'   => $num_affected_rows != 0 ? 'No affected rows' : 'successfull'
        ];
    }

    /**
     * Update full permission into table users_group
     *
     * @param Array $data contains 'permission', 'name'
     */
    public function updateGroupPermissions(array $data)
    {

        $sql = "UPDATE `" . DB_PREFIX . "users_group` SET permission = :permission WHERE name = :name";

        $query = $this->db->query($sql, [
            ':permission' => $data['permission'],
            ':name'       => $data['name']
        ]);

        $affected_rows = $query->rowsCount();

        return $affected_rows;
    }

    /**
     * Cascade delete a group
     * affected tables: 'users_group', 'user_permissions'
     *
     *
     * @param string $name name of group to delete
     * @return number number of rows affected
     */
    public function deleteGroupByName($name = '')
    {
        $sql = "DELETE FROM `" . DB_PREFIX . "users_group` WHERE name = :name LIMIT 1";
        return $this->db->query($sql, [
            ':name' => $name
        ])->rowsCount();
    }

    /**
     * Add a permission into table `users_group`, column `permission`
     *
     * @access public
     * @param string[] contains properties 'name' (group name) && 'permission'
     */
    public function addPermission(array $data)
    {
        $sql_permission = "SELECT `permission` FROM `" . DB_PREFIX . "users_group` WHERE name = :name";
        $permission = $this->db->query($sql_permission, [
            ':name' => $data['name']
        ])->row('permission');

        $permission = empty($permission) ? '{ "api": []}' : $permission;
        $permission = json_decode($permission);
        array_push($permission->api, $data['permission']);

        $sql_update_permission = "UPDATE `" . DB_PREFIX . "users_group` SET permission = :permission WHERE name = :name";
        $query = $this->db->query($sql_update_permission, [
            ':permission'   => json_encode($permission),
            ':name'         => $data['name']
        ]);

        return $query->rowsCount();
    }

    /**
     * Update a permission into table `users_group`, column `permission`
     *
     * @param array $data contains:
     *  - state: bool
     *  - group: group name
     *  - permission: permission string or array
     */
    public function updatePermission(array $data) {

        foreach(['state', 'group', 'permission'] as $param) {
            if (!isset($data[$param])) {
                return [
                    'success'   => false,
                    'message'   => 'Param `' . $param .  '` does not exist or incorrect'
                ];
            }
        }

        // check if group exists
        $sql_group_exist = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "users_group` WHERE name = :name LIMIT 1";
        $query = $this->db->query($sql_group_exist, [
            ':name'     =>  $data['group']
        ]);

        if ($query->row('total') == 0) {
            return [
                'success'   => false,
                'message'   => 'No group named ` ' . $data['group'] .  ' `'
            ];
        }

        // get all permissions
        $sql_permissions = "SELECT `permission` FROM `" . DB_PREFIX .  "users_group` WHERE name = :name";
        $permissions = $this->db->query($sql_permissions, [
            ':name'     => $data['group']
        ])->row('permission');

        $permissions = empty($permissions) ? '{ "api": [] }' : $permissions;
        $permissions = json_decode($permissions);

        // check permissions
        $look_up_data = is_string($data['permission']) ? [$data['permission']] : $data['permission'];
        foreach($look_up_data as $permission) {
            $permission_index = array_search($permission, $permissions->api);
            if (is_numeric($permission_index)) {
                // remove permission
                if ($data['state'] == false) {

                    // unset data and remove index when json_encode
                    unset($permissions->api[$permission_index]);
                    $permissions->api = array_values($permissions->api);
                }
            } elseif ($data['state'] == true) {
                // append permission
                $permissions->api[] = $permission;
            }
        }

        // update permissions
        $sql_update_permissions = "UPDATE `" . DB_PREFIX . "users_group` SET permission = :permission WHERE name = :name";
        $query_update_permissions  = $this->db->query($sql_update_permissions, [
            ':permission'   => json_encode($permissions),
            ':name'         => $data['group']
        ]);

        // return
        if ($num_count = $query_update_permissions->rowsCount()) {
            return [
                'success'           => true,
                'affected_rows'     => $num_count
            ];
        } else {
            return [
                'success'           => false,
                'affected_rows'     => 0
            ];
        }
    }
}
