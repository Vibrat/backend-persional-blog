<?php

/**
 * Group Permissions
 * 
 * @method newGroupPermissions api=post/account/group-permission/new-group
 */

use System\Model\Controller;

class GroupPermissionController extends Controller
{

  public function index()
  {

    $payload = $this->http->data();

    switch ($this->http->method()) {
      case 'GET':
        $action = $this->http->data()['GET']['action'];

        if ($action == 'permission') {

          $this->listPermissions();
        } elseif ($action == 'list') {

          $this->listGroups();
        }

        break;
      case 'POST':
        switch ($action = $payload['GET']['action']) {
          case 'create':
            $this->newGroupPermissions();
            break;

          case 'update':
            $this->updateGroupPermission()();
            break;

          case 'addUserToGroup':
            $this->addUserToGroup();
            break;
        }

        break;
      case 'PUT':

        $this->methodNotSupport();
        break;
      case 'DELETE':

        $this->deleteGroupPermission();
        break;
      default:

        $this->methodNotSupport();
    }
  }

  public function listGroups()
  {
    $this->json->sendBack([
      'success' => false,
      'message' => 'Please check your token'
    ]);
  }

  public function listPermissions()
  {
    $this->model->load('account/group');
    $getPaylod = $this->http->data()['GET'];

    if ($this->user->isTokenValid($getPaylod['token'])) {

      $permissions = $this->model->group->listPermissions($getPaylod['id']);
      $this->json->sendBack([
        'success' => true,
        'data' => json_decode($permissions)
      ]);
      return;
    }

    $this->json->sendBack([
      'success' => false,
      'message' => 'Please check your token'
    ]);
  }

  /**
   * Create new Group Permission
   *
   * @var String $_POST['token']
   * @var String $_POST['name']
   * @var String $_POST['permission'] 
   */
  public function newGroupPermissions()
  {

    if ($this->http->method() != 'POST') {

      $this->tokenInvalid();
      return;
    }

    if ($this->user->isTokenValid($_POST['token'])) {

      $this->model->load('account/group');

      if ($this->model->group->countGroup($_POST['name'])) {

        $this->json->sendBack([
          'success' => false,
          'message' => 'name already exists'
        ]);

        return;
      }

      $num_rows = $this->model->group->newGroup($_POST);
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

  public function deleteGroupPermission()
  {

    echo 'Delete group permissions';
  }

  private function tokenInvalid()
  {

    $this->json->sendBack([
      'success' => false,
      'message' => 'Token is invalid or user has no permission'
    ]);
  }

  private function methodNotSupport()
  {

    $this->json->sendBack([
      'success' => false,
      'message' => 'Application does not support method ' . $this->http->method()
    ]);
  }
}