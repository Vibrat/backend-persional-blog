<?php

/**
 * Basic Authentication for Application
 * 
 * 
 * @method new
 * @method login
 * @method logout
 * @method checkToken
 */

use \System\Model\Controller;
use phpDocumentor\Reflection\Types\Boolean;

class BasicAuthController extends Controller
{

    /**
     * Create new Account
     * 
     * @param SESSION $_POST['username']
     * @param SESSION $_POST['password']
     * 
     * @return Array [ 'success' => Boolean, 'token' || 'message' => '' ]
     */
    public function new()
    {
        if ($this->http->method() != 'POST') {
            $this->json->sendBack([
                'success'   => false,
                'code'      => 403,
                'message'   => 'This API only supports method `POST`'
            ]);
            return;
        }

        $get_data = $this->http->data('GET');
        if (!$this->user->isTokenValid($get_data['token'])) {
            $this->json->sendBack([
                'success' => false,
                'message' => [
                    'Token is invalid or user has not valid permissions'
                ]
            ]);

            return;
        };

        $this->model->load('account/account');
        $validator = $this->validateUser($_POST);
        if ($validator['success']) {

            $success = $this->model->account->createAccount($_POST);
            if ($success) {
                $account_info = $this->model->account->getAccount($_POST['username']);
                
                $this->json->sendBack($account_info);
                return;
            }

            $this->json->sendBack([
                'success' => false,
                'message' => 'Unknown error while accessing database'
            ]);
            return;
        }

        $this->json->sendBack($validator);
    }

    /**
     * Delete an account
     * 
     * @endpoint DELETE api=account/basic-auth/delete&username=<>&token=<>
     * @param string username
     * @param string token
     */
    public function delete() {
        if ($this->http->method() != 'DELETE') {
            $this->json->sendBack([
                'success'    => true,
                'code'       => 403,
                'message'    => 'This API only support method DELETE'
            ]);
            return;
        }

        $get_data = $this->http->data('GET');
        if ($this->user->isTokenValid($get_data['token'])) {
            $this->model->load('account/account');

            $response = $this->model->account->deleteAccount($get_data['username']);

            $this->json->sendBack([
                'success'         => $response['success'],
                'code'            => $response['success'] ? 200 : 403,
                'affected_rows'   => $response['affected_rows'],
                'message'         => $response['affected_rows'] ? 
                                     "Successfly delete account" : "No Account exists in server"
            ]);
            return;
        }

        $this->json->sendBack([
            'success'   => false,
            'code'      => 401,
            'message'   => 'Token is invalid'
        ]);
        return;
    }

    /**
     * Login and Return token
     *
     * @param SESSION $_POST['username']
     * @param SESSION $_POST['password']
     * @return ['success' => Boolean, 'token' || 'message' => '']
     */
    public function login()
    {

        $response = [];

        /** @var Authenticator/Authenticator()->login($credentials) $token */
        $token = $this->user->login([
            'username' => $_POST['username'],
            'password' => $_POST['password']
        ]);

        $response['success'] = ($token ? true : false);
        $response[($token ? 'token' : 'message')] = ($token ? $token : 'Authentication failed');

        $this->json->sendBack($response);
    }

    /**
     * Check if an account exist
     * 
     * @endpoint GET api=account/basic-auth/is-account-exist&usernam=<>&token=<>
     * @param string username
     * @param string token
     */
    public function isAccountExist() {
        if ($this->http->method() != 'GET') {

            $this->json->sendBack([
                'success'   => false,
                'code'      => 403,
                'message'   => 'This API only supports method `GET`'
            ]);
            return;
        }

        $get_data = $this->http->data('GET');
        if ($this->user->isTokenValid($get_data['token'])) {
            $this->model->load('account/account');

            $response = $this->model->account->checkAccount($get_data['username']);

            $this->json->sendBack([
                'success'   => $response ? true : false
            ]);
            return;
        }

        $this->json->sendBack([
            'success'   => false,
            'code'      => 401,
            'message'   => 'Token is invalid'
        ]);
    }

    /**
     * Change Password of a username
     * 
     * @endpoint PUT api=account/basic-auth/change-password&token=<>
     * @param GET token
     * @param PUT username
     * @param PUT old-password
     * @param PUT new-password
     */
    public function changePassword() {
        
        if ($this->http->method() != 'PUT') {
            $this->json->sendBack([
                'success'   => false,
                'code'      => 403,
                'message'   => 'This API only supports method PUT'
            ]);
            return;
        }

        $get_data = $this->http->data('GET');
        if ($this->user->isTokenValid($get_data['token'])) {
            ## Do sth here
            return;
        }

        $this->json->sendBack([
            'success'   => false,
            'code'      => 401,
            'message'   => 'Token is invalid'
        ]);
    }

    /**
     * List users
     * 
     * @endpoint GET api=account/basic-auth/list&offset=<>&limit=<>&group=<>
     * @param token
     * @param offset optional
     * @param limit optional default 100
     * @param group optional default *
     */
    public function list()
    {

        if ($this->http->method() != 'GET') {
            $this->json->sendBack([
                'success'   => false,
                'code'      => 403,
                'message'   => 'This API only supports method GET'
            ]);
            return;
        }

        $get_data = $this->http->data('GET');
        if ($this->user->isTokenValid($get_data['token'])) {
            $this->model->load('account/account');

            $response = $this->model->account->listAccounts($get_data);

            $this->json->sendBack($response);
            return;
        }

        $this->json->sendBack([
            'success'   => false,
            'code'      => 401,
            'message'   => 'token is invalid'
        ]);
        return;
    }

    /**
     * Check if token is valid
     * 
     * @endpoint GET api=account/basic-auth/token&token=<>
     * @param string token
     */
    public function token()
    {
        if ($this->http->method() != 'GET') {
            $this->json->sendBack([
                'success'   => false,
                'message'   => 'This API only supports method GET'
            ]);
            return;
        }

        $get_data = $this->http->data('GET');
        $this->json->sendBack([
            'success'   => $this->user->isTokenValid($get_data['token']) ? true : false
        ]);
    }

    /**
     *  Validate User Rule for 'username' and 'password' 
     *
     *  @param Array $data ['username' => 'lamnguyen' , 'password' => 'password']
     *  @return Boolean 
     */
    private function validateUser($data)
    {

        ## validate if empty
        if (!isset($data['username']) || !isset($data['password'])) {
            return [
                'success'   => false,
                'message'   => '`Username` or `password is not set`'
            ];
        }

        ## validate if exists
        if ($this->model->account->checkAccount($data['username'])) {
            return [
                'success'   => false,
                'message'   => 'Username already exists on server'
            ];
        }

        ## validate if password egitibility
        if (!(function ($pwd) {
            ## validate password
            if (strlen($pwd) < 8) {
                return;
            }

            return true;
        })($data['password'])) {
            return [
                'success'   => false,
                'message'   => 'Password is not set'
            ];
        }

        return [
            'success'   => true,
        ];
    }
}
