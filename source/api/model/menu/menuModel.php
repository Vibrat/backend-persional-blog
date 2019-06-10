<?php

class MenuModel extends BaseModel {
   
    /**
     * Check if module is installed
     */
    public function isEnable() {
       $sql = "SELECT is_init FROM `" .  DB_PREFIX . "modules_config` WHERE name LIKE '%menu.sql' LIMIT 1";
       return $this->db->query($sql)->row('is_init');
    }

    /**
     * List data from table `menu`
     * 
     * @param String offset 
     * @param String limit
     */
    public function getMenuList($data) { 
        
        if (is_numeric($data['offset']) && 
            is_numeric($data['limit'])) {
            
            $sql = "SELECT `category`, `name`, `order` FROM `" . DB_PREFIX . "menu` LIMIT " . $data['offset'] . ", " . $data['limit'] . "";
            $query = $this->db->query($sql);
    
            return $query->rows();
        }
        
        throw new \Exception('Input data for this method is ninvalidated');
    }
}