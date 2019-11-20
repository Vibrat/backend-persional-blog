<?php

class PrivateModel extends BaseModel
{
  public function getMenuTree()
  {
    $sql = "SELECT * FROM `" . DB_PREFIX . "navigation_all`";
    $query = $this->db->query($sql);

    return $query->rows();
  }

  public function getNavigation(array $data) {

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

    if (!isset($data['link'])) {
      return [
        'success' => false,
        'message' => 'Parameter `link` does not exist'
      ];
    }

    $input = [
      'name'    => $data['name'],
      'menu'    => $data['menu'],
      'order'   => isset($data['order']) && is_numeric($data['order']) ? $data['order'] : 0,
      'link'    => isset($data['link']) ? $data['link'] : '',
      'enable'  => isset($data['enable']) && is_numeric($data['enable']) ? $data['enable'] : 0
    ];

    $menuRes= $this->checkMenuExist($input);
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

  public function checkMenuRuleForGroup(array $data) {
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

  public function checkGroupExist(array $data) {
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
}
