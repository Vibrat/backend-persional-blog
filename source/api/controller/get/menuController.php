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
        
        ## parse data from HTTP Header
        $raw = array_shift($data);
        $str_lines = preg_split("/\\r\\n----------------------------\d*-{0,2}\\r\\n(Content-Disposition: form-data; ){0,}/", $raw);
        foreach($str_lines as $line) {

            [$key, $value] = preg_split("/\\r\\n\\r\\n/", $line);
            preg_match("/\"(.*?)\"/", $key, $key);
            $data[$key] = $value;
        }
        
        $this->json->sendBack([
            'isAlive' => true,
            'data' => $data,
            'raw'  =>  $str_lines
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
