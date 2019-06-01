<?php

/**
 * This class is created to perform API Call
 * 
 * (c) All rights reserved, Lam Nguyen | lam.nguyen.mr@outlook.com
 * 
 * Product: MVC API Package
 */


use System\Model\Controller;


/**
 * PizzaController API. This will call index() if action is not declared in url
 * 
 * @Url: \index.php?api=get\pizza\barg
 * @Flow\Scope("singleton")
 * @return Json 
 */
class PizzaController extends Controller {

    public function testFunc(){
        $this->json->sendBack([
            'hello' => 'hi'
        ]);
        return 3;
    }

    /**
     * Pizza API: Get Pizza information
     * 
     * @return Json 
     * @api
     */
    public function barg() {

       $this->model->load('pizza/pizza'); 

       $res = $this->apier->call(
           'POST', 'http://localhost:81/index.php?api=post/account/register/new-account', [
           'username' => 'lamnguyen22323', 'password' => '1234382173468326123123'
       ]);

       $this->json->sendBack($res);
    }

    public function login() {
       
        $token = $this->user->login([
            'username' => $_POST['username'], 
            'password' => $_POST['password']
            ]);

        $this->json->sendBack([
            'token' => $token 
            ]);   
    }

    public function checkLogin() {
        $this->json->sendBack($this->user->getToken());
    }

    public function isLogged() {
        $this->json->sendBack($this->user->isLogged($_GET['token']));
    }
}