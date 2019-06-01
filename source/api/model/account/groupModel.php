<?php

/**
 * Class Model for Group Permission
 */

class GroupModel extends BaseModel
{

    public function newGroup(array $data)
    {

        $sql = "INSERT INTO `" . DB_PREFIX . "users_group` SET name = '" . $data['name'] . "', permission = '" . $data['permission'] . "'";
        return $this->db->query($sql)->rowsCount();
    }

    public function countGroup(String $group_name)
    {

        $sql = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "users_group` WHERE name = '" . $group_name . "'";
        return $this->db->query($sql)->row('total');
    }

    /**
     * List Permissions of a Group
     * 
     * @param Int id 
     */
    public function listPermissions($id)
    {
        $query = $this->db->query("SELECT permission FROM `" . DB_PREFIX . "users_group` WHERE id  = '" . (int)$id . "'");

        return $query->row('permission');
    }

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