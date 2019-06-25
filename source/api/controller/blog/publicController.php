<?php

/**
 * Public API for Blog API
 * 
 */

 use \System\Model\Controller;

 class PublicController extends Controller {

    /**
     * Check if this api is active
     * 
     * @endpoint GET api=blog/public/enablement&token=<>
     */
    public function enablement() {

        $get_data = $this->http->data('GET');
        if($this->user->isTokenValid($get_data['token'])) {
            
            $this->json->sendBack([
                'success'   => true,
                'code'      => 200,
                'message'   => 'Service is alive'
            ]);
            return;
        }

        $this->json->sendBack([
            'success'   => false,
            'code'      => 403,
            'message'   => 'Token is invalid'
        ]);
    }

    /**
     * Create new blog record
     * 
     * @endpoint POST api=blog/public/create&token=<>
     * @param string title - required - unique value 
     * @param string des - optional 
     * @param string tags - optional
     * @param string category - optional
     * @param string seo_title - required - unique value
     * @param string seo_des - optional
     * @param string seo_url - required - unique value
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
            
            $this->model->load("blog/blog");           
            $post_data = $this->http->data('POST');
            $response = $this->model->blog->addNewRecord($post_data);

            if ($response['success']) {

                $this->json->sendBack([
                    'success'   => true,
                    'code'      => 200,
                    'message'   => 'Successfully create a blog article'
                ]);
                return;
            }

            $this->json->sendBack([
                'success'   => false,
                'code'      => 403,
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
     * Delete a article
     * 
     * @endpoint DELETE api=blog/public/delete&id=<>&token=<>
     * @param string id - blog id
     * @param string token
     */
    public function delete() {

        if  ($this->http->method() != 'DELETE') {

            $this->json->sendBack([
                'success'   => false,
                'code'      => 403,
                'message'   => 'This API only supports method DELETE'
            ]);
            return;
        }

        $get_data = $this->http->data('GET');
        if ($this->user->isTokenValid($get_data['token'])) {
            $this->model->load('blog/blog');
            $response = $this->model->blog->deleteARecord($get_data['id']);
            if ($response['success'] && $response['data']) {
                $this->json->sendBack([
                    'success'   => true,
                    'code'      => 200,
                    'message'   => 'Successfully delete a record'
                ]);
                return;
            }
            
            $this->json->sendBack([
                'success'   => false,
                'code'      => 403,
                'message'   => 'There is no article with id ' . $get_data['id'] 
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
     * Get a blog record
     * 
     * @endpoint GET api=blog/public/get&id=<>&token=<>
     * @param string id - id of blog
     * @param string token
     */
    public function get() {

        if ($this->http->method() != 'GET') {

            $this->json->sendBack([
                'success'   => false,
                'code'      => 403,
                'message'   => 'This API only support method GET'
            ]);
            return;
        }

        $get_data = $this->http->data('GET');
        if($this->user->isTokenValid($get_data['token'])) {
            $this->model->load('blog/blog');

            $response = $this->model->blog->getARecord($get_data['id']);
            if ($response['success']) {
                
                $this->json->sendBack([
                    'success'   => true,
                    'code'      => 200,
                    'data'      => $response['data']
                ]);
                return;
            }

            $this->json->sendBack([
                'success'   => false,
                'code'      => 403,
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
    
     
    public function list() {
     
        if ($this->http->method() != 'GET') {
            $this->json->sendBack([
                'success'   => false,
                'code'      => 403, 
                'message'   => 'This API onlsy supports method GET'
            ]);
            return;
        }

        $get_data = $this->http->data('GET');
        
    }
 }
