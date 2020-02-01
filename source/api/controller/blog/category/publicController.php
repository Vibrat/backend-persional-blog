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
  {
    // Validate Method
    if ($this->http->method() != 'POST') {
      $this->json->sendBack([
        'success' => true,
        'code'    => 405,
        'message' => 'This api only supports method `POST`'
      ]);
      return;
    }

    // Validate token
    $get = $this->http->data('GET');
    if ($this->user->isTokenValid($get['token'])) {
      // Load model
      $this->model->load('blog/blogCategory');

      $post = $this->http->data('POST');
      $response = $this->model->blogCategory->addNewCategory($post);

      if ($response['success']) {
        $this->json->sendBack([
          'success' => true,
          'code'    => 201,
          'message' => 'category is created'
        ]);
        return;
      }

      switch ($response['code']) {
        case 'ERROR_MODEL_PARAM':
          $this->json->sendBack([
            'success' => false,
            'code'    => 400,
            'message' => $response['message']
          ]);
          break;
        case 'ERROR_MODEL_RECORD_EXIST':
          $this->json->sendBack([
            'success' => false,
            'code'    => 409,
            'message' => $response['message']
          ]);
          break;
        default:
          $this->json->sendBack([
            'success' => false,
            'code'    => 400,
            'message' => $response['message']
          ]);
      }

      return;
    }

    $this->json->sendBack([
      'success' => false,
      'code'    => 401,
      'message' => 'Unauthenticated'
    ]);
  }

  /**
   * Check If Category Exists
   *
   * @Endpoint GET api=blog/category/public/exist&name=<>&token=<>
   */
  public function exist()
  {
    // Validate method
    if ($this->http->method() != 'GET') {
      $this->json->sendBack([
        'success' => false,
        'code'    => 405,
        'message' => 'This api only supports method `GET`'
      ]);
      return;
    }

    $get = $this->http->data('GET');
    if ($this->user->isTokenValid($get['token'])) {
      // load model
      $this->model->load('blog/blogCategory');

      $response = $this->model->blogCategory->checkCategory($get['name']);
      // @TODO: Validate response and send back here
      $this->json->sendBack($response);
      return;
    }

    $this->json->sendBack(
      [
        'success' => false,
        'code'    => 401,
        'message' => 'Unauthenticated'
      ]);
  }

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
