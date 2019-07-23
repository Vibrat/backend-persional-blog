<?php

/**
 * Group Permissions
 * 
 * @method newGroupPermissions api=post/account/group-permission/new-group
 */

use System\Model\Controller;

class GroupPermissionController extends Controller
{
  
  /**
   * List group of permissions
   * 
   * @endpoint GET api=account/group-permission/list&token=<>&offset=<>&&limit=<>
   * @access public
   */
  public function listGroups()
  { 
    $data = $this->http->data('GET');

    if($this->http->method() != 'GET') {
      $this->json->sendBack([
        'success' => false,
        'message' => 'API only supports method GET'
      ]);
      return;
    }

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

      } catch(\Exception $e) {
        $this->json->sendBack([
          'success' => false,
          'message' => $e->getMessage()
        ]);
      } finally {
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
   * Add single permission into collumn `users_group.permission`
   * 
   * @endpoint POST api=account/group-permission/add-permission?token=<>
   * @param string $data['name'] group name
   * @param string $permission permission json string
   * @access public
   */
  public function addPermission() {

    if ($this->http->method() != 'POST') {
      $this->json->sendBack([
        'success' => false,
        'message' => 'This API only supports method POST'
      ]);
      return;
    }

    $get_data = $this->http->data('GET');
    $post_data = $this->http->data('POST');

    if ($this->user->isTokenValid($get_data['token'])) {
      $this->model->load('account/group');
      
      $response = $this->model->group->addPermission($post_data);

      if ($response) {
        $this->json->sendBack([
          'success' => true,
          'message' => 'successfully update group permission'
        ]);
      } else {
        $this->json->sendBack([
          'success' => false,
          'message' => 'Failed to update permission, please check your permission information'
        ]);
      }
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

  /**
   * Update all permissions in table users_permission
   * 
   * @endpoint PUT api=account/group-permission/update-group-permission&token=<>
   * @param string name ngroup name
   * @param string[] permissions 
   * @access public 
   */
  public function updateGroupPermission()
  {
    
    if ($this->http->method() != 'PUT') {
      $this->json->sendBack([
        'success' => false,
        'message' => 'API only supports method PUT'
      ]);

      return;
    }

    $put_data = $this->http->data('PUT');
    $get_data = $this->http->data('GET');
    
    ## Validate data
    if (!isset($put_data['permission']) || !is_string($put_data['permission'])) {
      $this->json->sendBack([
        'success' => false,
        'message' => 'parameter permission is not set correctly'
      ]);

      return;
    }

    if (!isset($put_data['name']) || !is_string($put_data['name'])) {  
      $this->json->sendBack([
        'success' => false,
        'message' => 'parameter name should exist and have a string value'
      ]);

      return;
    }

    if ($this->user->isTokenValid($get_data['token'])) {
      $this->model->load('account/group');

      if ($this->model->group->updateGroupPermissions($put_data)) {
        $this->json->sendBack([
          'success' => true,
          'message' => 'Successfully update permissions'
        ]);
      } else {
        $this->json->sendBack([
          'success' => false,
          'message' => 'Your data is already saved into database'
        ]);
      }

      return;
    }

    $this->tokenInvalid();
  }

  /**
   * Add a user to group
   * 
   * @endpoint POST account/group-permissions/add-user-to-group&token=<>
   * @param token
   * @param userId `root, VIP2`
   * @param groupId
   * @access public
   */
  public function addUserToGroup()
  {

    if ($this->http->method() != 'POST') {
      
      $this->json->sendBack([
        'success' => false,
        'message' => 'Unsupported method for this api'
      ]);

      return;
    }

    $get_data  = $this->http->data('GET');
    $post_data = $this->http->data('POST');

    if ($this->user->isTokenValid($get_data['token'])) {

      $this->model->load('account/group');
      if ($this->model->group->addUserToGroup($post_data)) {
        $this->json->sendBack([
          'success' => true,
          'message' => 'Group has been added a permission',
          'data' => [
            'userId'  => $post_data['userId'],
            'groupId' => $post_data['groupId']
          ]
        ]);

        return;
      }

      $this->json->sendBack([
        'success' => false,
        'message' => 'Error: Please check if group exists or UserId Exists'
      ]);
      return;
    }

    $this->tokenInvalid();
  }


  /**
   * Add a user to group by groupname
   * 
   * @endpoint POST api=account/group/add-user-to-group-by-name&token=<>
   * @param userId
   * @param groupname
   */
  public function addUserToGroupByName() {
    if ($this->http->method() != 'POST') {
      
      $this->json->sendBack([
        'success' => false,
        'message' => 'Unsupported method for this api'
      ]);

      return;
    }

    $get_data  = $this->http->data('GET');
    $post_data = $this->http->data('POST');

    if ($this->user->isTokenValid($get_data['token'])) {

      $this->model->load('account/group');
      if ($this->model->group->addUserToGroupByGroupName($post_data)) {
        $this->json->sendBack([
          'success' => true,
          'message' => 'Group has been added a permission',
          'data' => [
            'userId'    => $post_data['userId'],
            'groupname' => $post_data['groupname']
          ]
        ]);

        return;
      }

      $this->json->sendBack([
        'success' => false,
        'message' => 'Error: Please check if group exists or UserId Exists'
      ]);
      return;
    }

    $this->tokenInvalid();
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