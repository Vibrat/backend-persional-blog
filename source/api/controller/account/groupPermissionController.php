<?php

/**
 * Group Permissions
 * 
 * @method newGroupPermissions api=post/account/group-permission/new-group
 */

use System\Model\Controller;

class GroupPermissionController extends Controller
{
  
  public function listGroups($data)
  { 
    if ($this->user->isTokenValid($data['token'])) {
  
      $this->model->load('account/group');
      try {
        $db_res = $this->model->group->listGroups($data);
        $db_num_records = $this->model->group->getSummary();
        
        $this->json->sendBack([
          'success'     => true,
          'data'        => $db_res,
          '_statistics' => [
            'total'     => $db_num_records,
            'offset'    => $data['offset'],
            'limit'     => $data['limit']
          ]
        ]);

        return;
      } catch(\Exception $e) {
        $this->json->sendBack([
          'success' => false,
          'message' => $e->getMessage()
        ]);

        return;
      }
    }

    $this->json->sendBack([
      'success' => false,
      'message' => 'Please check your token'
    ]);
  }

  /**
   * List permissions of a  group
   * 
   * @endpoint api=account/group-permissions/list-permissions&id=<>&token=<>
   * @param int id group id
   * @param string token
   * @access public
   */
  public function listPermissions()
  {

    if ($this->http->method() != 'GET') {
      $this->json->sendBack([
        'success' => false,
        'message' => 'API only support method GET'
      ]);
      
      return;
    }

    $get_data = $this->http->data('GET');
    if ($this->user->isTokenValid($get_data['token'])) {
      
      $this->model->load('account/group');
      $permissions = $this->model->group->listPermissions($get_data['id']);

      $this->json->sendBack([
        'success' => true,
        'data' => json_decode($permissions)
      ]);
      return;
    }

    $this->json->sendBack([
      'success' => false,
      'message' => 'Invalid token'
    ]);
  }

  /**
   * Create new Group Permission
   *
   * @endpoint api=post/account/group-permission/creata&token=<>
   * @param string token
   * @param string name
   * @param string permissions
   */
  public function create()
  {

    // retrieve data
    $get_data = $this->http->data('GET');
    $post_data = $this->http->data('POST');

    // allow only method POST
    if ($this->http->method() != 'POST') {

      $this->tokenInvalid();
      return;
    }

    // validate token
    if ($this->user->isTokenValid($get_data['token'])) {

      $this->model->load('account/group');
      if ($this->model->group->countGroup($post_data['name'])) {

        $this->json->sendBack([
          'success' => false,
          'message' => 'group name already exists'
        ]);

        return;
      }

      $num_rows = $this->model->group->newGroup($post_data);
      $this->json->sendBack([
        'success' => true,
        'affected_rows' => $num_rows
      ]);

      return;
    }

    $this->tokenInvalid();
  }

  public function updateGroupPermission()
  {
    $this->json->sendBack([
      'method' => 'Update'
    ]);

    ## check method
    if ($this->user->isTokenValid($_PUT['token'])) {

      return;
    }

    $this->tokenInvalid();
  }

  public function addUserToGroup()
  {

    if ($this->http->method() != 'POST') {
      
      $this->json->sendBack([
        'success' => fasle,
        'message' => 'Unsupported method for this api'
      ]);

      return;
    }

    if ($this->user->isTokenValid($this->http->data()['GET']['token'])) {

      $this->model->load('account/group');

      if ($this->model->group->addUserToGroup($this->http->data()['POST'])) {
        $this->json->sendBack([
          'success' => true,
          'message' => 'Group has been added a permission'
        ]);

        return;
      }

      $this->json->sendBack([
        'success' => Fasle,
        'message' => 'Error: Please check if Group Exists or UserId Exists'
      ]);
    }
  }

  /**
   * delete a group contact
   * 
   * @endpoint DELETE 
   * @param string token
   * @param string  name group name to delete
   * @access public 
   */
  public function delete()
  {

    $get_data = $this->http->data('GET');
    if ($this->http->method() != 'DELETE') {
      $this->json->sendBack([
        'success' => false,
        'message' => 'API does not support your method'
      ]);
      return;
    }

    if ($this->user->isTokenValid($get_data['token'])) {
      
      $this->model->load('account/group');
      $response = $this->model->group->deleteGroupByName($get_data['name']);
      
      if (!$response) {
        $this->json->sendBack([
          'success' => false,
          'message' => 'group name does not exist in database'
        ]);

        return;
      }

      $this->json->sendBack([
        'success' => true,
        'message' => 'successful delete a group'
      ]);
      return;
    }

    $this->tokenInvalid();
  }

  private function tokenInvalid()
  {

    $this->json->sendBack([
      'success' => false,
      'message' => 'Token is invalid or user has no permission'
    ]);
  }
}