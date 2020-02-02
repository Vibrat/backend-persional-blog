<?php

use \System\Model\BaseModel;

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

    /**
     * Read a menu from table `menu`
     *
     * @param string $name name of menu to query
     */
    public function readMenu($name) {
        if (!is_string($name)) {
            return [
                'success'   => false,
                'message'   => '$name is not a valid string'
            ];
        }

        $sql = "SELECT `category`, `name`, `children`, `is_init`, `order` FROM `" . DB_PREFIX . "menu` WHERE name = :name LIMIT 1";
        $query = $this->db->query($sql, [
            ':name' => $name
        ]);

        return [
            'success'   => true,
            'data'      =>$query->row()
        ];
    }

    /**
     * Update a record in table 'menu'
     *
     * @param string name: name of menu to update information
     * @param string category: category information to update
     * @param number order: number type string
     * @param string children: string delimited by comma
     * @param number is_init: 1 | 0
     */
    public function updateMenu($data) {
        // parameters that are allowed in this method
        $params = ['category', 'order', 'children', 'is_init'];
        if (!isset($data['name'])) {
            return [
                'success'   => false,
                'message'   => 'Parameter `name is missing`'
            ];
        }

        // Check if record already exists
        $sql_count_menu = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "menu` WHERE `name` = :name LIMIT 1";
        $counts_menu = $this->db->query($sql_count_menu, [
            ':name'     => $data['name']
        ])->row('total');

        // Start Update information
        if ($counts_menu) {
            $sql_update_menu = "UPDATE `" . DB_PREFIX . "menu` SET ";
            $bind_params = [];

            foreach($params as $key) {
                if (isset($data[$key])) {
                    $sql_update_menu .= "`" . $key . "` = :" . $key . "";
                    if (next($params)) {
                        $sql_update_menu .= ', ';
                    }

                    $bind_params = array_merge($bind_params, [
                        ':' . $key => $data[$key]
                        ]);
                }
            }

            $sql_update_menu .= " WHERE `name` = :name";
            $query = $this->db->query($sql_update_menu,
                array_merge([
                    ':name' => $data['name']
                ], $bind_params));

            return [
                'success'   => true,
                'data' => $query->rowsCount()
            ];
        }

        return [
            'success'   => false,
            'message'   => 'name ' . $data['name'] . ' doesn\'t exist'
        ];
    }

    /**
     * Delete a record in table menu
     *
     * @param string name - name of a record
     */
    public function deleteMenu(String $name) {

        $sql = "DELETE FROM `" . DB_PREFIX . "menu` WHERE `name` = :name LIMIT 1";
        $query = $this->db->query($sql, [
            ':name' => $name
        ]);

        $affected_rows = $query->rowsCount();

        if ($affected_rows) {
            return [
                'success'   => true,
                'message'   => sprintf('successfully delete menu `%s`', $name)
            ];
        }

        return [
            'success'   => false,
            'message'   => sprintf('There is not menu named `%s`', $name)
        ];
    }
}
