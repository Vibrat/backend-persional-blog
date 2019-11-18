<?php

class PrivateModel extends BaseModel {

    public function getMenuTree() {
        $sql = "SELECT * FROM `" . DB_PREFIX . "navigation_all`";
        $query = $this->db->query($sql);
        
        return $query->rows();
    }
}