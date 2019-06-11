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

        if (!$this->user->isTokenValid($_POST['token'])) {
            $this->json->sendBack([
                'success' => false,
                'message' => [
                    'Token is invalid or user has not valid permissions'
                ]
            ]);

            return;
        };

        $this->model->load('account/account');

        if ($this->validateUser($_POST)) {

            $success = $this->model->account->createAccount($_POST);
            if ($success) {

                $this->login();
                return;
            }

            $this->json->sendBack([
                'success' => false,
                'message' => 'Unknown error while accessing database'
            ]);
            return;
        }

        $this->json->sendBack([
            'success' => false,
            'message' => [
                'User already exists'
            ]
        ]);
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
     *  Validate User Rule for 'username' and 'password' 
     *
     *  @param Array $data ['username' => 'lamnguyen' , 'password' => 'password']
     *  @return Boolean 
     */
    private function validateUser($data)
    {
    
    ## validate if empty
        if (!isset($data['username']) || !isset($data['password'])) {
            return;
        }

    ## validate if exists
        if ($this->model->account->checkAccount($data['username'])) {
            return;
        }

    ## validate if password egitibility
        if (!(function ($pwd) {
        ## validate password
            if (strlen($pwd) < 8) {
                return;
            }

            return true;
        })($data['password'])) {
            return;
        }

        return true;
    }
}
