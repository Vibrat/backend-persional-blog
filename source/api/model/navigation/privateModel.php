<?php

class PrivateModel extends BaseModel
{
  public function getMenuTree()
  {
    $sql = "SELECT * FROM `" . DB_PREFIX . "navigation_all`";
    $query = $this->db->query($sql);

    return $query->rows();
  }

  public function getNavigation(array $data)
  {
    /**
     * Prioritize to check if group exists
     *  if not then use token to find groupId
     */
    if (isset($data['group']) && !empty($data['group'])) {
      $sql_group = "SELECT id FROM `" . DB_PREFIX . "users_group` WHERE name = :name LIMIT 1";
      $groupId = $this->db->query($sql_group, [
        ':name' => $data['group']
      ])->row('id');

    } else if (isset($data['token']) && !empty($data['token'])) {
      $sql_group = "SELECT u.group_permission_id as id FROM `" . DB_PREFIX . "users_permission` u";
      $sql_group .= " LEFT JOIN `" . DB_PREFIX . "users_token` t ON (u.user_id = t.id)";
      $sql_group .= " WHERE t.token = :token LIMIT 1";

      $groupId =  $this->db->query($sql_group, [
        ':token'  => $data['token']
      ])->row('id');
    } else {
      return [
        'success' => false,
        'message' => 'There should be one parameter `group` or `token`'
      ];
    }

    if ($groupId != false) {

      $sql_navigation = "SELECT id FROM " . DB_PREFIX . "navigation WHERE groupId = :groupId LIMIT 1";
      $id = $this->db->query($sql_navigation, [
        ':groupId'  => $groupId
      ])->row('id');

      $sql_navigations =
        "
      WITH RECURSIVE menu_tree (id, menu, link, children) AS  (
        SELECT id, menu, link, children FROM `navigation_all` WHERE id = :id
        UNION ALL
        SELECT child.id, child.menu, child.link, child.children
        FROM `menu_tree` AS parent JOIN `navigation_all` AS child ON FIND_IN_SET(child.id, parent.children)
      )
      SELECT * FROM `menu_tree` ORDER BY id;
      ";

      $result = $this->db->query($sql_navigations, [
        ':id' => $id
      ])->rows();

      $keys = array_unique(array_map(function ($item) {
        return $item['id'];
      }, $result));

      $result = array_filter($result, function ($item) use (&$keys) {

        $indentifier = $item['id'];
        $foundKey = array_search($indentifier, $keys);
        if (is_numeric($foundKey)) {
          unset($keys[$foundKey]);
          return true;
        }

        return false;
      });

      // filtering roots
      $roots = array_filter($result, function ($item) use ($id) {
        return $item['id'] == $id;
      });

      function recur($node, $data, $response)
      {
        $children = preg_split('/,/', $node['children']);
        $response['children'] = [];
        foreach ($children as $child) {

          $selected = array_filter($data, function ($item) use ($child) {
            return $item['id'] == $child;
          });

          foreach ($selected as $child) {
            if (!empty($child['children'])) {
              $children = preg_split('/,/', $child['children']);
              $child['children'] = recur($child, $data, $response);
            }
            array_push($response['children'], $child);
          }
        }

        return $response['children'];
      }

      // parsing tree from roots
      $tree = [];
      foreach ($roots as $root) {
        $num_item = array_push($tree, $root);
        $tree[$num_item - 1]['children'] =  recur($root, $result, $tree);
      }

      $result = $result ? $result : [];

      return  [
        'success' => true,
        'message' => 'Ok',
        'data'    => $tree,
        'raw'     => $result,
      ];
    }

    return [
      'success' => false,
      'message' => 'Unable to find group match name'
    ];
  }

  public function changeMenu(array $data)
  {
    if (!isset($data['menu']) || empty($data['menu'])) {
      return [
        'success' => false,
        'message' => 'Parameter `menu` does not exist or empty'
      ];
    }

    $input = [
      'name'    => $data['name'],
      'menu'    => $data['menu'],
      'order'   => isset($data['order']) && is_numeric($data['order']) ? $data['order'] : 0,
      'enable'  => isset($data['enable']) && is_numeric($data['enable']) ? $data['enable'] : 0
    ];

    $menuRes = $this->checkMenuExist($input);
    if (!$menuRes['success'] || $menuRes['data']['total'] == 0) {
      return [
        'success' => false,
        'message' => sprintf('Menu %s does not exist', $input['menu'])
      ];
    }

    $checkGroupExist = $this->checkGroupExist([
      'name'  => $input['name']
    ]);

    if (!$checkGroupExist['success']) {
      return [
        'success' => false,
        'message' => sprintf('Group `%s` does not exist', $input['name'])
      ];
    }

    $checkRecordExist = $this->checkMenuRuleForGroup([
      'id'        => $menuRes['data']['id'],
      'groupId'   => $checkGroupExist['data']['id']
    ]);

    if ($checkRecordExist['success']) {
      $sql = "UPDATE `" . DB_PREFIX . "navigation` SET enable = :enable, `order` = :order WHERE id = :id AND groupId = :groupId";

      if (!isset($data['enable'])) {
        $input['enable'] = $checkRecordExist['data']['enable'];
      }

      if (!isset($data['order'])) {
        $input['order'] = $checkRecordExist['data']['order'];
      }
    } else {
      $sql = "INSERT INTO `" . DB_PREFIX . "navigation` SET id = :id, groupId = :groupId, enable = :enable, `order` = :order";
    }

    $query = $this->db->query($sql, [
      ':id'       => $menuRes['data']['id'],
      ':groupId'  => $checkGroupExist['data']['id'],
      ':enable'   => (int) $input['enable'],
      ':order'    => (int) $input['order']
    ]);

    if ($query->rowsCount()) {
      return [
        'success' => true,
        'message' => 'Database is updated'
      ];
    }

    return [
      'success' => false,
      'message' => 'No row affected'
    ];
  }

  public function checkMenuExist(array $data)
  {
    if (!isset($data['menu'])) {
      return [
        'success' => false,
        'message' => 'Parameter `menu` does not exist'
      ];
    }

    $sql = "SELECT COUNT(*) AS total, id FROM `" . DB_PREFIX .  "navigation_all` WHERE menu = :menu LIMIT 1";
    $result = $this->db->query($sql, [
      ':menu' => $data['menu']
    ])->row();

    return [
      'success' => true,
      'data'  => $result
    ];
  }

  public function checkMenuRuleForGroup(array $data)
  {
    if (!isset($data['id'])) {
      return [
        'success' => false,
        'message' => 'Parameter `id` does not exist'
      ];
    }

    if (!isset($data['groupId'])) {
      return [
        'success' => false,
        'message' => 'Parameter `groupId` does not exist'
      ];
    }

    $sql = "SELECT `id` AS `total`, `enable`, `order` FROM `" . DB_PREFIX . "navigation` WHERE id = :id AND groupId = :groupId LIMIT 1";
    $query = $this->db->query($sql, [
      ':id'       => $data['id'],
      ':groupId'  => $data['groupId']
    ]);

    $response = $query->row();
    if ($response['total']) {
      return [
        'success' => true,
        'message' => 'Record exists',
        'data'    => $response
      ];
    }

    return [
      'success' => false,
      'message' => 'Record does not exist',
      'data'    => $response
    ];
  }

  public function checkGroupExist(array $data)
  {
    if (!isset($data['name'])) {
      return [
        'success' => false,
        'message' => 'Parameter `name` does not exist'
      ];
    }

    $sql = "SELECT id AS total, id FROM `" . DB_PREFIX . "users_group` WHERE name = :name LIMIT 1";
    $query = $this->db->query($sql, [
      ':name' => $data['name']
    ]);

    $response = $query->row();
    if ($response['total']) {
      return [
        'success' => true,
        'message' => sprintf('Group `%s` exist', $data['name']),
        'data'    => $response
      ];
    }

    return [
      'success' => false,
      'message' => sprintf('Group `%s` does not exist', $data['name']),
      'data'    => $response
    ];
  }

  public function checkNavigationExistById(array $data)
  {
    if (!isset($data['id']) || !is_numeric($data['id'])) {
      return [
        'success' => false,
        'message' => 'Parameter `id` does not exist or not valid'
      ];
    }

    $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "navigation` WHERE id = :id LIMIT 1";
    $query = $this->db->query($sql, [
      ':id' => $data['id']
    ]);

    $num_count = $query->row('total');

    if ($num_count) {
      return [
        'success' => true,
        'data'  => $num_count
      ];
    } else {
      return [
        'success' => false,
        'message' => 'No record exists'
      ];
    }
  }

  public function deleteNavigationById(array $data)
  {
    if (!isset($data['id']) || !is_numeric($data['id'])) {
      return [
        'success' => false,
        'message' => 'Parameter `id` does not exist or not valid'
      ];
    }

    $check_response = $this->checkNavigationExistById($data);

    if ($check_response['success']) {

      $sql_delete = "DELETE FROM `" . DB_PREFIX . "navigation` WHERE id = :id LIMIT 1";
      $query = $this->db->query($sql_delete, [
        ':id' => $data['id']
      ]);

      $num_count = $query->rowsCount();
      if ($num_count) {
        return [
          'success' => true,
          'message' => 'successfully delete reccord'
        ];
      } else {
        return [
          'success' => false,
          'message' => 'No record affected'
        ];
      }
    }

    return [
      'success' => false,
      'message' => 'Record does not exist'
    ];
  }

  public function updateSnapshot(array $data)
  {

    $decoded = json_decode($data['data'], true);
    try {
      $this->db->beginTransaction();
      foreach ($decoded as $key => $item) {

        $data = [
          'name'  => $data['name'],
          'menu'  => $item['menu'],
          'order' => 0,
          'enable'  => 1
        ];

        $this->changeMenu($data);
      }
      $this->db->commit();
    } catch (Exception $e) {

      $this->db->rollBack();
      return [
        'success' => false,
        'message' => $e->getMessage()
      ];
    }

    $group = $this->getGroupIdByName($data);

    if (!$group['success']) {
      return $group['message'];
    }

    $navigation = $this->getNavigation([
      'group' => $data['name']
    ]);

    $deleted = array_filter($navigation['data'], function ($item) use ($decoded) {
      $values = array_map(function ($row) {
        return $row['menu'];
      }, $decoded);

      return !in_array($item['menu'], $values);
    });

    foreach ($deleted as $record) {
      $this->deleteNavigationById($record);
    }

    return [
      'success' => true,
      'message' => 'ok'
    ];
  }

  public function getGroupIdByName(array $data)
  {
    if (!isset($data['name'])) {
      return [
        'success' => false,
        'message' => 'Parameter `name` does not exist'
      ];
    }

    $sql = "SELECT id FROM `" . DB_PREFIX . "users_group` WHERE name = :name LIMIT 1";
    $query = $this->db->query($sql, [
      ':name' => $data['name']
    ]);

    $id = $query->row('id');

    if ($id != null) {
      return [
        'success' => true,
        'data'   => $id
      ];
    } else {
      return [
        'success' => false,
        'message' => 'Group does not exist'
      ];
    }
  }
}
