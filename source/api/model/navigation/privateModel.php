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

    if (!isset($data['group']) || empty($data['group'])) {
      return [
        'success' => false,
        'message' => 'Parameter `group` does not exist'
      ];
    }

    $sql_group = "SELECT id FROM `" . DB_PREFIX . "users_group` WHERE name = :name LIMIT 1";
    $groupId = $this->db->query($sql_group, [
      ':name' => $data['group']
    ])->row('id');

    $sql_navigation =
      "
      SELECT g.id, n.menu, n.link, n.children FROM `" . DB_PREFIX . "navigation_all` n
      LEFT JOIN `" . DB_PREFIX . "navigation` g ON n.id = g.id WHERE g.groupId = :groupId
    ";

    if ($groupId != false) {

      $result = $this->db->query($sql_navigation, [
        ':groupId'  => $groupId
      ])->rows();

      $result = $result ? $result : [];

      return  [
        'success' => true,
        'message' => 'Ok',
        'data'    => $result
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
