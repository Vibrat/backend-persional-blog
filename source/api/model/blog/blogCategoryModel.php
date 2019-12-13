<?php

use \System\Model\BaseModel;

/**
 * Model For BlogCategory
 * Table: blog_category
 */
class BlogCategoryModel extends BaseModel
{

  /**
   * Add new category
   *
   * @Payload:
   *  - name: string;
   *  - children: string;
   *  - order: number;
   */
  public function addNewCategory(array $data)
  {
    /* Step 1: Validation  */
    if (
      !isset($data['name'])
      || !is_string($data['name'])
      || empty($data['name'])
    ) {
      return [
        'success' => false,
        'code'    => 'ERROR_MODEL_PARAM',
        'message' => 'Parameter `name` should be string and not empty'
      ];
    }

    if (!isset($data['children'])) {
      $data['children'] = '';
    }

    if (!isset($data['order'])) {
      $data['order'] = 0;
    }

    /* Step 2: Check existence */
    $sql_exist = "SELECT COUNT(*)  AS total FROM " . DB_PREFIX . "blog_category WHERE name = :name LIMIT 1";
    $counts = $this->db->query($sql_exist, [
      ':name' => $data['name']
    ])->row('total');

    if ($counts == 0) {

      /* Step 2: Query */
      $sql = "INSERT INTO `" . DB_PREFIX . "blog_category` (`name`, `children`, `order`) VALUES (:name, :children, :order)";
      $affected_rows = $this->db->query($sql, [
        ':name'     => $data['name'],
        ':children' => $data['children'],
        ':order'    => (int) $data['order']
      ])->rowsCount();

      return [
        'success'         => true,
        'code'            => 'OK',
        'affected_rows'   => $affected_rows
      ];
    }

    return [
      'success' => false,
      'code'    => 'ERROR_MODEL_RECORD_EXIST',
      'message' => 'Category name `' . $data['name'] . '` already exists'
    ];
  }

  /**
   * Delete Category
   *
   * @Payload:
   *  - name: string;
   */
  public function deleteCategory(array $data)
  { }

  /**
   * Select Categories
   *
   * @Payload:
   *  - limit: number;
   *  - order: 'desc', 'asc';
   */
  public function selectCategories(array $data)
  { }
}
