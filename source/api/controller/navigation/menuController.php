<?php

/**
 * This class is created to perform API Call
 *
 * (c) All rights reserved, Lam Nguyen | lam.nguyen.mr@outlook.com
 *
 * 401	Unauthorized
 * 403	Forbidden
 * 404	Not Found
 * Product: MVC API Package
 */

use System\Model\Controller;


/**
 * Menu API. This will call index() if action is not declared in url
 *
 * @Url: \index.php?api=navigation\menu
 * @Flow\Scope("singleton")
 * @return Json
 */
class MenuController extends Controller
{

  public function listAll()
  {
    if ($this->http->method() != 'GET') {
      $this->json->sendBack([
        'success' => false,
        'code'    => 403,
        'message' => 'This api only supports method `GET`'
      ]);
      return;
    }

    $data['GET'] =  $this->http->data('GET');
    if ($this->user->isTokenValid($data['GET']['token'])) {
      $this->model->load('navigation/private');

      // Doing some work here
      $this->json->sendBack([
        'success' => true,
        'code'    => 200,
        'data'    => $this->model->private->getMenuTree()
      ]);
      return;
    }

    $this->json->sendBack([
      'success' => false,
      'code'    => 401,
      'message' => 'Unauthenticated'
    ]);
  }

  /**
   * Add/ Update a permission of navigation
   *
   * @Endpoint POST api=navigation/menu/change&token=<>
   */
  public function change()
  {
    if ($this->http->method() != 'POST') {
      $this->json->sendBack([
        'success' => false,
        'code'    => 403,
        'message' => 'This api only supports method `POST`'
      ]);
      return;
    }

    $get = $this->http->data('GET');
    if ($this->user->isTokenValid($get['token'])) {
      $this->model->load('navigation/private');

      // doing somthing here
      $post = $this->http->data('POST');
      $result = $this->model->private->changeMenu($post);

      if ($result['success']) {

        $this->json->sendBack([
          'success' => true,
          'code'    => 200,
        ]);
        return;
      }

      $this->json->sendBack([
        'success' => false,
        'code'    => 400,
        'message' => $result['message']
      ]);
      return;
    }

    $this->json->sendBack([
      'success' => false,
      'code'    => 401,
      'message' => 'Unauthenticated'
    ]);
  }

  /**
   * Read a Navigation
   *
   * @Endpoint GET api=navigation/menu/read&token=<>
   */
  public function read()
  {
    if ($this->http->method() != 'GET') {
      $this->json->sendBack([
        'success' => false,
        'code'    => 403,
        'message' => 'This Api only supports method `GET`'
      ]);
      return;
    }

    $get = $this->http->data('GET');
    if ($this->user->isTokenValid($get['token'])) {
      $this->model->load('navigation/private');

      $response = $this->model->private->getNavigation($get);

      if ($response['success']) {
        $this->json->sendBack([
          'success' => true,
          'code'    => 200,
          'data'    => $response['data']
        ]);
        return;
      }

      $this->json->sendBack([
        'success' => false,
        'code'    => 204,
        'message' => $response['message']
      ]);
      return;
    }

    $this->json->sendBack([
      'success' => false,
      'code'    => 401,
      'message' => 'Unauthenticated'
    ]);
  }

  /**
   * Update a snapshot of permission by group name
   *
   * @Endpoint PUT api=navigation/menu/snapshot&token=<>
   * @payload
   *  ```
   *  name: string; => group name
   *  data: string; => stringified json
   *  ```
   */
  public function snapshot()
  {
    if ($this->http->method() != 'PUT') {
      $this->json->sendBack([
        'success' => false,
        'code'  => 403,
        'message' => 'This api only supports method `PUT`'
      ]);
      return;
    }

    $get = $this->http->data('GET');
    if ($this->user->isTokenValid($get['token'])) {

      $put = $this->http->data('PUT');
      $this->model->load('navigation/private');
      $response = $this->model->private->updateSnapshot($put);

      $this->json->sendBack($response);
      return;
    }

    $this->json->sendBack([
      'success' => false,
      'code'  => 401,
      'message' => 'Unauthenticated'
    ]);
  }

  /**
   * Delete a record from table `navigation` by id
   *
   * @Endpoint DELETE api=navigation/menu/delete&token=<>
   * @payload
   *  ```
   *  id: number
   *  ```
   */
  public function delete()
  {
    // check method
    if ($this->http->method() != 'DELETE') {
      $this->json->sendBack([
        'success' => false,
        'code'    => 403,
        'message' => 'This api only supports method `DELETE`'
      ]);
      return;
    }

    // validate token
    $get = $this->http->data('GET');
    if ($this->user->isTokenValid($get['token'])) {

      // load model
      $this->model->load('navigation/private');

      // delete record
      $delete = $this->http->data('DELETE');
      $response = $this->model->private->deleteNavigationById($delete);

      // send response back
      $this->json->sendBack($response);
      return;
    }

    // send response if error
    $this->json->sendBack([
      'success' => false,
      'code'    => 401,
      'message' => 'Unauthenticated'
    ]);
  }
}
