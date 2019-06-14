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
        
        throw new \Exception('Input data for this method is invalidated');
    }

    /**
     * Add new record into table `menu`
     * 
     * @param string $data['category']
     * @param string $data['name']
     * @param number $data['order']
     * @param string $data['children']
     * @param number $data['enable']
     */ 
    public function addNewMenu(Array $data) {
        $errors = [];

        if (!is_string($data['category'])) { 
            array_push($errors, 'parameter category is not string type');
        }

        if (!is_string($data['name']) || empty($data['name'])) {
            array_push($errors, 'parameter name is not string type or empty');
        }

        if(!empty($data['order']) && !is_numeric($data['order'])) {
            array_push($errors, 'parameter order is not number type');
        } elseif (empty($data['order'])) {
            $data['order'] = 0;
        }

        if(!empty($data['children']) && !is_string($data['children'])) {
            array_push($errors, 'parameter children is not string type');
        } elseif (empty($data['children'])) {
            $data['children'] = '';
        }

        if (!empty($data['enable']) && !is_numeric($data['enable'])) {
            array_push($errors, 'parameter enable is numeric');
        } elseif (empty($data['enable'])) {
            $data['enable'] = true;
        }

        if (empty($errors)) {
            $sql_count = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "menu` WHERE name = :name";
            $menu_count = $this->db->query($sql_count, [
                ':name' => $data['name']
            ])->row('total');

            if (!$menu_count) {
                $sql = "INSERT INTO `" . DB_PREFIX . "menu` (`category`, `name`, `children`, `is_init`, `order`) VALUES (:category, :name, :children, :is_init, :order)";
                $query = $this->db->query($sql, [
                    ':category' => $data['category'],
                    ':name'     => $data['name'],
                    ':is_init'  => (int) $data['enable'],
                    ':order'    => (int) $data['order'],
                    ':children'  => $data['children']
                ]);
    
                return [
                    'success'       => true,
                    '_affectedRows' =>$query->rowsCount()
                ];
            }

            return [
                'success'   => false,
                'message'   => 'menu name already exists in database'
            ];
           
        } 

        return [
            'success'   => false,
            'message'      => $errors
        ];

    }
}