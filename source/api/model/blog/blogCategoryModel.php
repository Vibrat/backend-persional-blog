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
  { }

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
