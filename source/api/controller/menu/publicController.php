<?php

/**
 * This class is created to perform API Call
 * 
 * (c) All rights reserved, Lam Nguyen | lam.nguyen.mr@outlook.com
 * 
 * 401	Unauthorized
 * 403	Forbidden
 * 404	Not Found
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

        if ($this->http->method() != 'GET') {
            $this->json->sendBack([
                'success'   => false,
                'message'   => 'This API only supports method GET'
            ]);
            return;
        }

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

    /**
     * Add a menu
     * 
     * @endpoint POST  api=menu/public/create&token=<>
     * @param string filter string to filter
     * @param string name name of group
     * @param number order 
     * @param string[] children
     * @param boolean  enable
     */
    public function create() {

        if ($this->http->method() != 'POST') {
            
            $this->json->sendBack([
                'success'   => false,
                'code'      => 403,
                'message'   => 'This API only supports method POST'
            ]);
            return;
        }

        $get_data = $this->http->data('GET');
        if ($this->user->isTokenValid($get_data['token'])) {
            $this->model->load("menu/menu");
            $post_data = $this->http->data('POST');

            if (!empty($post_data['children']) && !is_string($post_data['children'])) {
                $this->json->sendBack([
                    'success'   => false,
                    'code'      => 403,
                    'message'   => 'parameter children must be string type'
                ]);

                return;
            }
            $post_data['children'] = str_replace(' ', '', $post_data['children']);

            $response = $this->model->menu->addNewMenu($post_data);

            if($response['success']) {
                $this->json->sendBack([
                    'success'   => true,
                    'message'   => 'Successfully add new menu'
                ]);
                return;
            }
            
            $this->json->sendBack([
                'success'   => false,
                'message'   => $response['message']
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
     * Read details of a menu
     * 
     * @endpoint GET api=menu/public/read&token=<>&name=<>
     * @param string token
     * @param string api
     * @param string name - Group Name to get information
     */
    public function read() {
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

            if (!isset($get_data['name'])) {
                $this->json->sendBack([
                    'success'   => false,
                    'message'   => 'parameter name does not exist'
                ]);
                return;
            }
            
            $this->model->load('menu/menu');
            $response  = $this->model->menu->readMenu($get_data['name']);

            if ($response['success']) {
                $this->json->sendBack([
                    'success'   => true,
                    'data'      => $response['data'] ? $response['data'] : []
                ]);
                return;
            } 

            $this->json->sendBack([
                'success'   => false,
                'message'   => $response['message']
            ]);
            return;
        }

        $this->json->sendBack([
            'success'   => false,
            'code'      => 401,
            'message'   => 'Token is invalid'
        ]);
    }
}
