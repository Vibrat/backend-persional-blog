<?php

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
    public function getSummary() {
        $sql = "SELECT COUNT(id) AS total FROM `" . DB_PREFIX . "users_group`";
        return $this->db->query($sql)->row('total');
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
    public function listGroups($data) {
        
        if (
            is_numeric($data['offset']) && 
            is_numeric($data['limit'])
            ) {

            $sql = "SELECT * FROM `" . DB_PREFIX . "users_group` LIMIT " . (int) $data['offset'] . ", " . (int) $data['limit'] . "";
            return $this->db->query($sql)->rows();
        }
       
        throw new \Exception("offset and limit values are in correct");
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

        $sql_num_groups = "SELECT COUNT(*) as total  FROM `" . DB_PREFIX . "users_group` WHERE id = '" . $payload['groupId'] . "'";
        $sql_num_permissions = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "users_permission` WHERE user_id = '" . $payload['userId'] . "' AND group_permission_id = '" . $payload['groupId'] . "'";
        $sql_insert_permissions = "INSERT INTO `" . DB_PREFIX . "users_permission` SET user_id = '" . $payload['userId'] . "', group_permission_id = '" . $payload['groupId'] . "'";

        if ($this->db->query($sql_num_groups)->row('total')) {

            if (!$this->db->query($sql_num_permissions)->row('total')) {

                $query_add = $this->db->query($sql_insert_permissions);

                if ($query_add->rowsCount()) {
                    return true;
                }
            }

            return false;
        }

    }
}