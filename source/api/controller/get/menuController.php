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
class MenuController extends Controller {

    public function isAlive() {
        $data = $this->http->data('PUT');
        
        $this->json->sendBack([
            'isAlive' => true,
            'data' => $data,
        ]);
    }


    public function list() {

        $is_logged = $this->user->isLogged($_GET['token']);

        if (!$is_logged) {
            $this->json->sendBack([
                'success'  => false,
                'message:' => 'Unauthenticated user'
            ]);

            return;
        }

        
    }
}
