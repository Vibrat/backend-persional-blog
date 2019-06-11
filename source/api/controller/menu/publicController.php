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
 * Menu API. This will call index() if action is not declared in url
 * 
 * @Url: \index.php?api=get\pizza\barg
 * @Flow\Scope("singleton")
 * @return Json 
 */
class PublicController extends Controller {

    /**
     * Check if API is enable
     * 
     * @endpoind api=get/menu/is-enable&token=<>
     * @return true | false
     */
    public function isEnable() {
        $this->model->load('menu/menu');

        $this->json->sendBack([
            'success' => true,
            'enable'  => $this->model->menu->isEnable() ? true : false
        ]);
    }

    /**
     * List menu
     * 
     * @endpoint api=get/menu/list&token=<>
     * @return string[] | false
     */
    public function list() {
        $errors = [];
        $is_logged = $this->user->isTokenValid($_GET['token']);

        if (!$is_logged) {
            $this->json->sendBack([
                'success'  => false,
                'message:' => 'Unauthenticated user'
            ]);

            return;
        }

        $this->model->load('menu/menu');
        $data = $this->http->data('GET');
        
        if (!isset($data['limit']) || 
            !is_numeric($data['limit'])) {
            array_push($errors, 'limit parameter does not exist or not numeric');
        } 

        if (!isset($data['offset']) || 
            !is_numeric($data['offset'])) {
            array_push($errors, 'limit parameter does not exist or not numeric');
        }

        if (empty($errors)) {
            $menu_items = $this->model->menu->getMenuList($data);
        }
            
        if (empty($errors)) {
            $this->json->sendBack([
                'success'  => true,
                'data'     => $menu_items
            ]);
        } else {
            $this->json->sendBack([
                'success' => false,
                'message' => $errors
            ]);
        }  
    }
}
