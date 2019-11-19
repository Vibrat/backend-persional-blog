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
class MenuController extends Controller {

  public function listAll() {
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
  public function change() {
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
  public function read() {
    if ($this->http->method() != 'GET') {
      $this->json->sendBack([
        'success' => false,
        'code'    => 403,
        'message' => 'This Api only supports method `GET`'
      ]);
      return;
    }

    $get = $this->http->data('GET');
    if($this->user->isTokenValid($get['token'])) {
      $this->model->load('navigation/private');

      $response = $this->model->private->getNavigation($get);
      return;
    }

    $this->json->sendBack([
      'success' => false,
      'code'    => 401,
      'message' => 'Unauthenticated'
    ]);
  }
}
