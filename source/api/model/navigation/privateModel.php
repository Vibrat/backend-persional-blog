<?php

class PrivateModel extends BaseModel
{

  public function getMenuTree()
  {
    $sql = "SELECT * FROM `" . DB_PREFIX . "navigation_all`";
    $query = $this->db->query($sql);

    return $query->rows();
  }

  public function addMenu(array $data)
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
      'groupId' => $data['groupId'],
      'menu'    => $data['menu'],
      'order'   => isset($data['order']) && is_int($data['order']) ? $data['order'] : 0,
      'link'    => isset($data['link']) ? $data['link'] : ''
    ];

    $menuRes= $this->checkMenuExist($input);
    if (!$menuRes['success'] || $menuRes['data']['total'] == 0) {
      return [
        'success' => false,
        'message' => sprintf('Menu %s does not exist', $input['menu'])
      ];
    }

    $checkGroupExist = $this->checkGroupExist([
      'id'  => $input['groupId']
    ]);
    if (!$checkGroupExist['success']) {
      return [
        'success' => false,
        'message' => sprintf('Group `%s` does not exist', $input['groupId'])
      ];
    }

    $checkRecordExist = $this->checkMenuRuleForGroup([
      'id'        => $menuRes['data']['id'],
      'groupId'   => $input['groupId']
    ]);

    if ($checkRecordExist['success']) {
      return [
        'success' => false,
        'message' => sprintf('Record already added')
      ];
    }

    $sql_add = "INSERT INTO `" . DB_PREFIX . "navigation` SET id = :id, groupId = :groupId, enable = 1";
    $query = $this->db->query($sql_add, [
      ':id'       => $menuRes['data']['id'],
      ':groupId'  => $input['groupId'],
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

    $sql = "SELECT COUNT(*) as total FROM `" . DB_PREFIX . "navigation` WHERE id = :id AND groupId = :groupId LIMIT 1";
    $query = $this->db->query($sql, [
      ':id'       => $data['id'],
      ':groupId'  => $data['groupId']
    ]);

    if ($query->row('total')) {
      return [
        'success' => true,
        'message' => 'Record exists'
      ];
    }

    return [
      'success' => false,
      'message' => 'Record does not exist'
    ];
  }

  public function checkGroupExist(array $data) {
    if (!isset($data['id'])) {
      return [
        'success' => false,
        'message' => 'Parameter `id` does not exist'
      ];
    }

    $sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "users_group` WHERE id = :id LIMIT 1";
    $query = $this->db->query($sql, [
      ':id' => $data['id']
    ]);

    if ($query->row('total')) {
      return [
        'success' => true,
        'message' => sprintf('Group `%s` exist', $data['id'])
      ];
    }

    return [
      'success' => false,
      'message' => sprintf('Group `%s` does not exist', $data['id'])
    ];
  }
}
