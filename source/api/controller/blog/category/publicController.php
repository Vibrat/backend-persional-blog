<?php

/**
 * Api Controller For Category
 *  - Handle category for post
 *  - Database: blog_cagegory
 *
 * @Endpoint api=blog/category/public
 */

use \System\Model\Controller;

class PublicController extends Controller
{

  /**
   * Create Category
   *
   * @Endpoint POST api=blog/category/public/create&token=<>
   * @Payload:
   *  - name: string;
   */
  public function create()
  { }

  /**
   * Check If Category Exists
   *
   * @Endpoint GET api=blog/category/public/exist&name=<>&token=<>
   */
  public function exist()
  { }

  /**
   * List Categories
   *
   * @Endpoint GET api=blog/category/public/list&limit=<>&token=<>
   */
  public function list()
  { }

  /**
   * Delete Category
   *
   * @Endpoint DELETE api=blog/category/public/delete&name=<>&token=<>
   */
  public function delete()
  { }
}
