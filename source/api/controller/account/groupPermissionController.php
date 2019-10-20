<?php

/**
 * Group Permissions
 *
 * @method newGroupPermissions api=post/account/group-permission/new-group
 * @var \Http\DataSubmit $this->http
 * @var \Authenticator\Authenticator $this->user
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

    if ($this->http->method() != 'GET') {
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
        $db_num_records = $this->model->group->getSummary($data['groupname']);

        $this->json->sendBack([
          'success'     => true,
          'data'        => $db_res,
          '_statistics' => [
            'total'     => $db_num_records,
            'offset'    => $data['offset'],
            'limit'     => $data['limit']
          ]
        ]);
      } catch (\Exception $e) {
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
   * List all permissions in the application
   *
   * @endpoint GET api=account/group-permission/list-all-permission&token=<>
   * @access public
   */
  public function listAllPermissions()
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

      function recursive_path($initial_path)
      {

        $controller_path = 'Controller.php';

        // Recursively parse paths
        foreach ($permissions = glob($initial_path) as $index => $path) {

          if (is_dir($path)) {

            foreach (recursive_path($path . "/*") as $sub_path) {
              $permissions[] = $sub_path;
            }

          } else if (substr($path, -strlen($controller_path), strlen($controller_path)) != $controller_path) {

            // remove uneccessary paths
            unset($permissions[$index]);
          } else {

            // Rebuild path
            $new_path = "";
            $path = str_replace($controller_path, "", $path);

            foreach (str_split($path) as $key => $char) {
              if (strtoupper($char) == $char && $char != '/') {
                $new_path .= ($key ? '-' : "") . strtolower($char);
              } else {
                $new_path .= $char;
              }
            }

            $permissions[$index] = $new_path;
          }
        }

        return $permissions;
      }

      $permissions = recursive_path(BASE_CONTROLLER . "*");

      $this->json->sendBack([
        'success' => true,
        'data' => [
          'api' => $permissions
        ]
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
  public function addPermission()
  {

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
   * Update a permission for a group
   *
   * @endpoint PUT api=account/group-permission/update-permission
   * @param string state 'true' or 'false'
   * @param string group group name
   * @param string or | string permission
   * @access public with token
   */
  public function updatePermission() {
    if ($this->http->method() !== 'PUT'){
      $this->json->sendBack([
        'success'   => false,
        'code'      => 401,
        'message'   => 'This API only supports method `PUT`'
      ]);
      return;
    }

    $get_data = $this->http->data('GET');
    if ($this->user->isTokenValid($get_data['token'])) {

      $this->model->load('account/group');
      $put_data = $this->http->data('PUT');

      // change state to php bool
      switch($put_data['state']) {
        case 'true':
          $put_data['state']  = true;
          break;
        case 'false':
          $put_data['state']  = false;
          break;
        default:
          $put_data['state'] = null;
      }

      $response = $this->model->group->updatePermission($put_data);

      // Perform action change
      $this->json->sendBack($response);
      return;
    }

    $this->json->sendBack([
      'success'   => false,
      'code'      => 404,
      'message'   => 'Toke is invalid'
    ]);
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
        'code'    => 401,
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
          'code'    => 200,
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
        'code'    => 403,
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
  public function addUserToGroupByName()
  {
    if ($this->http->method() != 'POST') {

      $this->json->sendBack([
        'success' => false,
        'code'    => 401,
        'message' => 'Unsupported method for this api'
      ]);

      return;
    }

    $get_data  = $this->http->data('GET');
    $post_data = $this->http->data('POST');

    if ($this->user->isTokenValid($get_data['token'])) {

      $this->model->load('account/group');
      if ($lastInsertId = $this->model->group->addUserToGroupByGroupName($post_data)) {
        $this->json->sendBack([
          'success' => true,
          'code'    => 200,
          'message' => 'Group has been added a permission',
          'data' => [
            'userId'    => $post_data['userId'],
            'groupId'   => $lastInsertId,
            'groupname' => $post_data['groupname']
          ]
        ]);

        return;
      }

      $this->json->sendBack([
        'success' => false,
        'code'    => '403',
        'message' => 'Username or Group already assigned, or not exist'
      ]);
      return;
    }

    $this->tokenInvalid();
  }

  /**
   * Check if a group exists
   *
   * @endpoint GET apt=account/group-permission/is-group-exist&group=<>&token=<>
   * @param group
   * @param token
   */
  public function isGroupExist()
  {
    if ($this->http->method() != 'GET') {
      $this->json->sendBack([
        'success' => false,
        'code'    => '401',
        'message' => 'This api only supports `GET` method'
      ]);
      return;
    }

    $get_data = $this->http->data('GET');
    if ($this->user->isTokenValid($get_data['token'])) {
      $this->model->load('account/group');

      $response = $this->model->group->isGroupExist($get_data);

      $this->json->sendBack([
        'success' => $response['success'] && $response['total'] > 0 ? true : false,
        'code'    => 200,
        'total'   => $response['total']
      ]);
      return;
    }

    $this->json->sendBack([
      'success' => false,
      'code'    => 403,
      'message' => 'Unauthenticated'
    ]);
  }

  /**
   * Remove a user from group by name
   *
   * @endpoint DELETE api=account/group-permission/remove-user-from-group-by-name&token=<>&userId=<>&groupname=<>
   * @return [
   *      'success'   => boolean,
   *      'code'      => number,
   *      'message'?  => string,
   *      'data'?     => []
   * ]
   */
  public function removeUserFromGroupByName()
  {
    // check method, success passs, else return
    if ($this->http->method() != 'DELETE') {
      $this->json->sendBack([
        'success'   => false,
        'code'      => 405,
        'message'   => 'API only supports method `DELETE`'
      ]);
      return;
    }

    // check Token, success pass, else return
    $get_data = $this->http->data('GET');
    if ($this->user->isTokenValid($get_data['token'])) {

      $params['userId'] = $get_data['userId'] ? $get_data['userId'] : null;
      $params['groupname'] = $get_data['groupname'] ? $get_data['groupname'] : null;

      if (in_array(null, $params)) {
        $this->json->sendBack([
          'success' => false,
          'code'    => 400,
          'message' => '`userId`  or `groupname` is not provided yet'
        ]);
        return;
      }

      /* @var GroupModel */
      $this->model->load('account/group');

      $response = $this->model->group->removeUserFromGroupByName($params);

      $this->json->sendBack([
        'success'   => $response['success'],
        'code'      => $response['success'] ? 200 : 304,
        'message'   => $response['success'] ? 'sucessful remove a user to group' : 'No effect'
      ]);
      return;
    }

    // Token is invalid
    $this->json->sendBack([
      'success'   => false,
      'code'      => 401,
      'message'   => 'Token is invalid'
    ]);
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
